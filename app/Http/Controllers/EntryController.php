<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Header;
use App\Models\Subgroup;
use App\Models\Recurrency;
use App\Models\RecurrencyHeader;
use App\Models\RecurrencyItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Pagination\LengthAwarePaginator;

class EntryController extends Controller
{
    /**
     * Show the login form but only when user is not logged in already.
     *
     * @return \Illuminate\View\View
     */
    public function create($id = null, $recurring = false)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'You have to be logged in.');
        }

        $user = Auth::user();
        $collection = $user->collection;
        $groups = $collection->groups()->with('subgroups')->orderBy('name')->get();
        $listOfItems = [];
        $header = null;
        $allBadges = $user->badges;
        $recurringData = [];

        if ($id) {
            $header = Header::where('user_id', $user->id)->findOrFail($id);
            $header->blade = request('blade');
            $listOfItems = $header->items()->with('group', 'subgroup')->get();
            // Ensure badges is always an array (force update on the model, not just the collection)
            foreach ($listOfItems as $item) {
                // If badges is a string, decode it and set it as an attribute (so $item->badges returns array)
                if (is_string($item->badges)) {
                    $decoded = json_decode($item->badges, true);
                    $item->setAttribute('badges', is_array($decoded) ? $decoded : []);
                } elseif (is_null($item->badges)) {
                    $item->setAttribute('badges', []);
                }
            }
        }

        // If validation failed, repopulate items from old('items')
        if (old('items')) {
            $listOfItems = collect(old('items'))->map(function ($item) {
                return (object)$item;
            });
        }

        // Create a variable with all groups and their subgroups (id => name), subgroups sorted alphabetically (case-insensitive)
        $groupSubgroupMap = $groups
            ->map(function ($group) use ($collection) {
                if ($collection->id == 1) {
                    // If collection is 1, use the translations from standard group
                    $translatedName = Lang::get('std-groups.group-name.' . $group->name);
                    $sortedSubgroups = $group->subgroups->sortBy(function ($subgroup) {
                        return mb_strtolower($subgroup->name);
                    });
                    $translatedSubgroups = $sortedSubgroups->mapWithKeys(function ($subgroup) {
                        return [
                            $subgroup->id => Lang::get('std-groups.subgroup-name.' . $subgroup->name)
                        ];
                    });
                } else {
                    // Otherwise, use the group's and subgroups' names directly
                    $translatedName = $group->name;
                    $sortedSubgroups = $group->subgroups->sortBy(function ($subgroup) {
                        return mb_strtolower($subgroup->name);
                    });
                    $translatedSubgroups = $sortedSubgroups->pluck('name', 'id');
                }
                return [
                    'id' => $group->id,
                    'name' => $translatedName,
                    'type' => $group->type,
                    'subgroups' => $translatedSubgroups->toArray(),
                    'sort_key' => mb_strtolower($translatedName),
                ];
            })
            ->sortBy('sort_key')
            ->values()
            ->map(function ($group) {
                unset($group['sort_key']);
                return $group;
            });

        // dump($groupSubgroupMap);

        if (isset($header->recurrency_id) && $header->recurrency_id != null) {
            $recurrence = Recurrency::findOrFail($header->recurrency_id);
            // $recurring = true;
            $recurringData = [
                'base' => $recurrence->base,
                'frequency' => $recurrence->frequency,
                'rule' => $recurrence->rule,
                'day-of-month' => $recurrence->day_of_month,
                'day-of-week' => $recurrence->day_of_week,
                'month' => $recurrence->month,
                'number-of-occurrences' => $recurrence->number_of_occurrences,
                'occurrences-end-date' => $recurrence->occurrences_end_date,
                'occurrences-number' => $recurrence->occurrences_number,
                'name' => $recurrence->name,
                'recurringOccurrenceDates' => $recurrence->occurrences_dates,
            ];
        } else {
            $recurringData = [
                'base' => 'month',
                'frequency' => 1,
                'rule' => 5,
                'day-of-month' => 15,
                'day-of-week' => 0,
                'month' => 0,
                'number-of-occurrences' => 1,
                'occurrences-end-date' => today()->addYear()->format('Y-m-d'),
                'occurrences-number' => 12,
                'name' => '',
                'recurringOccurrenceDates' => '',
            ];
        }

        return view('entries.entry', compact('groups', 'listOfItems', 'groupSubgroupMap', 'header', 'allBadges', 'recurring', 'recurringData', 'user'));
    }

    public function store(Request $request)
    {
        // dd($request->all());

        if ($request->has('items')) {
            $items = $request->input('items');
            foreach ($items as $idx => $item) {
                if (isset($item['badges']) && is_string($item['badges'])) {
                    $decoded = json_decode($item['badges'], true);
                    $request->merge([
                        "items.$idx.badges" => is_array($decoded) ? $decoded : [],
                    ]);
                }
            }
        }

        $recurring = false;
        if ($request->has('recurring') && $request->input('recurring') == '1') {
            $recurring = true;
        }

        // Validate the request data (adjust rules as needed for your fields)
        $validatedData = $request->validate([
            'date' => 'required|date',
            'place' => 'nullable|string|max:50',
            'location' => 'nullable|string|max:50',
            'note' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.group_id' => 'required|integer|exists:groups,id',
            'items.*.subgroup_id' => 'required|integer|exists:subgroups,id',
            'items.*.amount' => 'required|numeric',
            'items.*.note' => 'nullable|string',
            'items.*.badges' => 'nullable|array',
            'items.*.badges.*' => 'integer',
            'manually_modified' => 'boolean',
            // 'recurrence_id' => 'nullable|integer',
        ]);

        $user = Auth::user();
        $collection = $user->collection;
        $groups = $collection->groups()->get();
        $groupTypeMap = $groups->pluck('type', 'id');

        if ($recurring) {
            // Handle recurring entry data
            $recurrenceData = $request->validate([
                'rec-name' => 'required|string|max:100',
                'base' => 'required|string|in:week,month,year',
                'frequency' => 'required|integer|min:1',
                'rule' => 'required|integer|min:1',
                'day-of-month' => 'required|integer|min:1|max:31',
                'day-of-week' => 'required|integer|min:0|max:6',
                'month' => 'required|integer|min:0|max:11',
                'number-of-occurrences' => 'required|integer|min:1|max:3',
                'occurrences-end-date' => 'nullable|date',
                'occurrences-number' => 'nullable|integer|min:2',
                'recurrence-id' => 'nullable|integer',
                'recurringOccurrenceDates' => 'nullable|string',
            ]);
            // dd($recurrenceData);
            if ($recurrenceData['recurrence-id'] > 0) {
                // If recurrence-id is set, update the existing recurrence
                $recurrence = Recurrency::findOrFail($recurrenceData['recurrence-id']);
                $recurrence->update([
                    'user_id' => $user->id,
                    'name' => $recurrenceData['rec-name'],
                    'base' => $recurrenceData['base'],
                    'frequency' => $recurrenceData['frequency'],
                    'rule' => $recurrenceData['rule'],
                    'day_of_month' => $recurrenceData['day-of-month'],
                    'day_of_week' => $recurrenceData['day-of-week'],
                    'month' => $recurrenceData['month'],
                    'number_of_occurrences' => $recurrenceData['number-of-occurrences'],
                    'occurrences_end_date' => $recurrenceData['occurrences-end-date'],
                    'occurrences_number' => $recurrenceData['occurrences-number'],
                    'occurrences_dates' => $recurrenceData['recurringOccurrenceDates'],
                ]);
            } else {
                // Create a new recurrence
                $recurrence = Recurrency::create([
                    'user_id' => $user->id,
                    'name' => $recurrenceData['rec-name'],
                    'base' => $recurrenceData['base'],
                    'frequency' => $recurrenceData['frequency'],
                    'rule' => $recurrenceData['rule'],
                    'day_of_month' => $recurrenceData['day-of-month'],
                    'day_of_week' => $recurrenceData['day-of-week'],
                    'month' => $recurrenceData['month'],
                    'number_of_occurrences' => $recurrenceData['number-of-occurrences'],
                    'occurrences_end_date' => $recurrenceData['occurrences-end-date'],
                    'occurrences_number' => $recurrenceData['occurrences-number'],
                    'occurrences_dates' => $recurrenceData['recurringOccurrenceDates'],
                ]);
                $recurrenceData['recurrence-id'] = $recurrence->id;
            }
        }


        $datesArray = [];
        $validatedData['manually_modified'] = true;
        if ($recurring) {
            $datesArray = json_decode($recurrenceData['recurringOccurrenceDates'], true);
            $firstOccurrenceDate = is_array($datesArray) && count($datesArray) > 0 ? $datesArray[0] : null;
            $validatedData['date'] = $firstOccurrenceDate ?: $validatedData['date'];
            $validatedData['manually_modified'] = false; // Set manually_modified to false for recurring entries
        }


        // Check if this is an update or create
        if ($request->has('header_id')) {
            // Update existing header
            $header = Header::where('user_id', $user->id)->findOrFail($request->input('header_id'));
            if (!$recurring) {
                $recurrenceData['recurrence-id'] = $header->recurrency_id;
            }
            $header->update([
                'date' => $validatedData['date'],
                'place_of_purchase' => $validatedData['place'] !== null ? $validatedData['place'] : '',
                'location' => $validatedData['location'] !== null ? $validatedData['location'] : '',
                'note' => $validatedData['note'] ?? null,
                'amount' => $validatedData['amount'],
                'recurrency_id' => $recurrenceData['recurrence-id'] ?? null,
                'manually_modified' => $validatedData['manually_modified'], // Set manually_modified based on the request
            ]);
            // Delete old items
            $header->items()->delete();
            $headerId = $header->id;
        } else {
            // Create new header
            $header = Header::create([
                'user_id' => $user->id,
                'date' => $validatedData['date'],
                'place_of_purchase' => $validatedData['place'] !== null ? $validatedData['place'] : '',
                'location' => $validatedData['location'] !== null ? $validatedData['location'] : '',
                'note' => $validatedData['note'] ?? null,
                'amount' => $validatedData['amount'],
                'recurrency_id' => $recurrenceData['recurrence-id'] ?? null,
                'manually_modified' => $validatedData['manually_modified'], // Set manually_modified based on the request
            ]);
            $headerId = $header->id;
        }

        // Create the items (for both create and update)
        foreach ($user->badges as $badge) {
            $badgeIdToInternalId[$badge->badge_id] = $badge->id;
        }
        foreach ($validatedData['items'] as $itemData) {
            if (floatval($itemData['amount']) == 0) {
                continue;
            }
            $groupType = $groupTypeMap[$itemData['group_id']] ?? null;
            Item::create([
                'header_id' => $header->id,
                'group_id' => $itemData['group_id'],
                'subgroup_id' => $itemData['subgroup_id'],
                'amount' => $itemData['amount'],
                'group_type' => $groupType,
                'note' => $itemData['note'] ?? null,
                'badges' => $itemData['badges'] ?: [], // Ensure badges is always an array
            ]);
        }

        // If this is a recurring entry, create RecurrencyHeader and RecurrencyItem
        if ($recurring) {
            // $datesArray = json_decode($recurrenceData['recurringOccurrenceDates'], true);
            $this->deleteOldHeaderAndItems($user->id, $header->recurrency_id, $header->date);
            $this->copyHeaderAndItems($headerId, $datesArray);
            $this->createOrUpdateRecurrencyHeaderAndItems($headerId, $recurrenceData['recurrence-id']);
        }


        // Redirect or return response
        $page = $request->input('page', 1);
        $selectedBadge = $request->input('badge-id', null);
        if ($request->has('header_id') && $request->has('blade')) {
            if ($request->input('blade') === 'list-badges') {
                return redirect()->route('entry.list-badges', ['page' => $page, 'badge-id' => $selectedBadge])->with('success', 'Entry updated successfully.');
            } elseif ($request->input('blade') === 'list-only-recurrences') {
                return redirect()->route('entry.list-only-recurrences', ['page' => $page, 'recurrence-id' => $recurrenceData['recurrence-id']])->with('success', 'Entry updated successfully.');
            } else {
                return redirect()->route('entry.list', ['page' => $page])->with('success', 'Entry updated successfully.');
            }
        } else {
            return redirect()->route('entry.create')->with('success', 'Entry saved successfully.');
        }
    }

    public function getSubgroups($groupId)
    {
        $subgroups = Subgroup::where('group_id', $groupId)->orderBy('name')->get(['id', 'name']);
        return response()->json($subgroups);
    }

    public function suggestPlaces(Request $request)
    {
        $query = $request->input('q');
        $user = Auth::user();

        $places = Header::where('user_id', $user->id)
            ->where('place_of_purchase', 'like', $query . '%')
            ->distinct()
            ->orderBy('place_of_purchase')
            ->limit(10)
            ->pluck('place_of_purchase');

        return response()->json($places);
    }

    public function suggestLocations(Request $request)
    {
        $query = $request->input('q');
        $user = Auth::user();

        $places = Header::where('user_id', $user->id)
            ->where('location', 'like', $query . '%')
            ->distinct()
            ->orderBy('location')
            ->limit(10)
            ->pluck('location');

        return response()->json($places);
    }

    public function list(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'You have to be logged in.');
        }

        $user = Auth::user();
        $itemsOnPage = config('appoptions.list_default_length', 20);

        // If no page is set, find the page for today's date
        if (!$request->has('page')) {
            $today = now()->format('Y-m-d');
            $allHeaders = Header::where('user_id', $user->id)
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            $index = $allHeaders->search(function ($header) use ($today) {
                return $header->date <= $today;
            });

            if ($index === false) {
                $page = 1;
            } else {
                $page = intval(floor($index / $itemsOnPage)) + 1;
            }

            return redirect()->route('entry.list', ['page' => $page]);
        }

        // Assuming your Header model has a user_id column
        $headers = Header::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($itemsOnPage); // or ->get() if you don't want pagination

        $dateFormat = $user->date_format ?? 'Y-m-d'; // fallback if not set

        return view('entries.list', compact('headers', 'dateFormat'));
    }

    public function listOnlyRecurrences(Request $request)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'You have to be logged in.');
        }

        $user = Auth::user();
        $itemsOnPage = config('appoptions.list_default_length', 20);

        // If no page is set, find the page for today's date
        if (!$request->has('page')) {
            $today = now()->format('Y-m-d');
            $allHeaders = Header::where('user_id', $user->id)
                ->where('recurrency_id', $request->input('recurrence-id', null))
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->get();

            $index = $allHeaders->search(function ($header) use ($today) {
                return $header->date <= $today;
            });

            if ($index === false) {
                $page = 1;
            } else {
                $page = intval(floor($index / $itemsOnPage)) + 1;
            }

            return redirect()->route('entry.list-only-recurrences', ['page' => $page, 'recurrence-id' => $request->input('recurrence-id', null)]);
        }

        // Assuming your Header model has a user_id column
        $headers = Header::where('user_id', $user->id)
            ->where('recurrency_id', $request->input('recurrence-id', null))
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($itemsOnPage); // or ->get() if you don't want pagination

        $dateFormat = $user->date_format ?? 'Y-m-d'; // fallback if not set

        $recurrenceName = '';
        if ($request->has('recurrence-id') && $request->input('recurrence-id') > 0) {
            $recurrence = Recurrency::findOrFail($request->input('recurrence-id'));
            $recurrenceName = $recurrence->name;
        }

        return view('entries.list-only-recurrences', compact('headers', 'dateFormat', 'recurrenceName'));
    }

    public function listBadges()
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'You have to be logged in.');
        }

        $user = Auth::user();

        $listOfBadges = $user->badges;
        $selectedBadge = request('badge-id');
        if ($selectedBadge === null && $listOfBadges && $listOfBadges->count() > 0) {
            $selectedBadge = $listOfBadges->first()->id;
        }

        $itemsOnPage = config('appoptions.list_default_length', 20);;
        $page = request('page', 1);

        $filtered = Header::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->get()
            ->filter(function ($header) use ($selectedBadge) {
                $badges = $header->badges();
                return !empty($badges) && ($selectedBadge === null || array_key_exists($selectedBadge, $badges));
            })
            ->values();

        $totalBadgeAmount = $filtered->sum(function ($header) use ($selectedBadge) {
            $badges = $header->badges();
            return isset($badges[$selectedBadge]) ? $badges[$selectedBadge] : 0;
        });

        $headers = new LengthAwarePaginator(
            $filtered->forPage($page, $itemsOnPage),
            $filtered->count(),
            $itemsOnPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        $totalPageBadgeAmount = $headers->sum(function ($header) use ($selectedBadge) {
            $badges = $header->badges();
            return isset($badges[$selectedBadge]) ? $badges[$selectedBadge] : 0;
        });

        $dateFormat = $user->date_format ?? 'Y-m-d'; // fallback if not set
        $badges = [];

        $labelBadge = Lang::get('list-badges.label_badge');

        return view('entries.list-badges', compact('headers', 'dateFormat', 'badges', 'listOfBadges', 'labelBadge', 'selectedBadge', 'totalBadgeAmount', 'totalPageBadgeAmount'));
    }

    public function listRecurrences()
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'You have to be logged in.');
        }

        $user = Auth::user();
        // Eager load RecurrencyHeader for each Recurrency
        $allRecurrences = Recurrency::where('user_id', $user->id)
            ->with('recurrencyHeader')
            ->orderBy('name', 'desc')
            ->get();

        // Paginate recurrences
        $itemsOnPage = config('appoptions.list_default_length', 20);
        $page = request('page', 1);

        $recurrences = new LengthAwarePaginator(
            $allRecurrences->forPage($page, $itemsOnPage),
            $allRecurrences->count(),
            $itemsOnPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('entries.list-recurrences', compact('recurrences'));
    }

    public function destroy($id)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'You have to be logged in.');
        }

        $user = Auth::user();
        $header = Header::where('user_id', $user->id)->findOrFail($id);

        // Delete the header and its associated items
        $header->items()->delete();
        $header->delete();

        // Redirect back to the previous page (preserve pagination)
        return redirect()->back()->with('success', 'Entry deleted successfully.');
    }

    public function addRecurring($id)
    {
        return $this->create($id, true);
    }

    public function copyHeaderAndItems($headerId, $datesArray = null)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'You have to be logged in.');
        }

        $user = Auth::user();
        $originalHeader = Header::where('user_id', $user->id)->findOrFail($headerId);
        $originalItems = $originalHeader->items;

        if (!$datesArray || !is_array($datesArray) || count($datesArray) === 0) {
            return redirect()->back()->with('error', 'No dates provided for copying.');
        }

        $newHeaders = [];
        // Skip the first date in the array (assumed to be the original header's date)
        $datesToCopy = array_slice($datesArray, 1);
        foreach ($datesToCopy as $date) {
            // Replicate header
            $newHeader = $originalHeader->replicate();
            $newHeader->date = $date;
            $newHeader->manually_modified = false;
            $newHeader->save();

            // Replicate items
            foreach ($originalItems as $item) {
                $newItem = $item->replicate();
                $newItem->header_id = $newHeader->id;
                $newItem->save();
            }
            $newHeaders[] = $newHeader;
        }
    }

    public function createOrUpdateRecurrencyHeaderAndItems($headerId, $recurrencyId)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'You have to be logged in.');
        }

        $user = Auth::user();
        $originalHeader = Header::where('user_id', $user->id)->findOrFail($headerId);
        $originalItems = $originalHeader->items;

        // Check if RecurrencyHeader with this recurrency_id exists
        $recurrencyHeader = RecurrencyHeader::where('recurrency_id', $recurrencyId)->first();
        if ($recurrencyHeader) {
            // Update existing RecurrencyHeader
            $recurrencyHeader->update([
                'amount' => $originalHeader->amount,
                'place_of_purchase' => $originalHeader->place_of_purchase,
                'location' => $originalHeader->location,
                'note' => $originalHeader->note,
            ]);
            // Delete old items
            $recurrencyHeader->items()->delete();
        } else {
            // Create a new RecurrencyHeader
            $recurrencyHeader = RecurrencyHeader::create([
                'recurrency_id' => $recurrencyId,
                'amount' => $originalHeader->amount,
                'place_of_purchase' => $originalHeader->place_of_purchase,
                'location' => $originalHeader->location,
                'note' => $originalHeader->note,
            ]);
        }

        // Replicate items (create new for both update and create)
        foreach ($originalItems as $item) {
            RecurrencyItem::create([
                'recurrency_header_id' => $recurrencyHeader->id,
                'group_id' => $item->group_id,
                'subgroup_id' => $item->subgroup_id,
                'group_type' => $item->group_type,
                'amount' => $item->amount,
                'note' => $item->note,
                'badges' => json_encode($item->badges),
            ]);
        }
    }

    public function deleteOldHeaderAndItems($userId, $recurrencyId, $date)
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'You have to be logged in.');
        }

        // Find all headers for this user with the same recurrency_id and date > $date
        $headers = Header::where('user_id', $userId)
            ->where('recurrency_id', $recurrencyId)
            ->where('date', '>', $date)
            ->get();

        foreach ($headers as $header) {
            // Delete all items for this header
            $header->items()->delete();
            // Delete the header itself
            $header->delete();
        }
    }
}

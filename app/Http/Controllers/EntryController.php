<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Header;
use App\Models\Subgroup;
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
    public function create($id = null)
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

        return view('entries.entry', compact('groups', 'listOfItems', 'groupSubgroupMap', 'header', 'allBadges'));
    }

    public function store(Request $request)
    {
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
        ]);

        $user = Auth::user();
        $collection = $user->collection;
        $groups = $collection->groups()->get();
        $groupTypeMap = $groups->pluck('type', 'id');

        // Check if this is an update or create
        if ($request->has('header_id')) {
            // Update existing header
            $header = Header::where('user_id', $user->id)->findOrFail($request->input('header_id'));
            $header->update([
                'date' => $validatedData['date'],
                'place_of_purchase' => $validatedData['place'] !== null ? $validatedData['place'] : '',
                'location' => $validatedData['location'] !== null ? $validatedData['location'] : '',
                'note' => $validatedData['note'] ?? null,
                'amount' => $validatedData['amount'],
            ]);
            // Delete old items
            $header->items()->delete();
        } else {
            // Create new header
            $header = Header::create([
                'user_id' => $user->id,
                'date' => $validatedData['date'],
                'place_of_purchase' => $validatedData['place'] !== null ? $validatedData['place'] : '',
                'location' => $validatedData['location'] !== null ? $validatedData['location'] : '',
                'note' => $validatedData['note'] ?? null,
                'amount' => $validatedData['amount'],
            ]);
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

        // Redirect or return response
        $page = $request->input('page', 1);
        $selectedBadge = $request->input('badge-id', null);
        if ($request->has('header_id') && $request->has('blade')) {
            if ($request->input('blade') === 'list-badges') {
                return redirect()->route('entry.list-badges', ['page' => $page, 'badge-id' => $selectedBadge])->with('success', 'Entry updated successfully.');
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

    public function list()
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'You have to be logged in.');
        }

        $user = Auth::user();
        $itemsOnPage = config('appoptions.list_default_length', 20);
        // Assuming your Header model has a user_id column
        $headers = Header::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate($itemsOnPage); // or ->get() if you don't want pagination

        $dateFormat = $user->date_format ?? 'Y-m-d'; // fallback if not set

        return view('entries.list', compact('headers', 'dateFormat'));
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
}

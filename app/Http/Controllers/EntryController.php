<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Header;
use App\Models\Subgroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            $listOfItems = $header->items()->with('group', 'subgroup')->get();
        }

        // If validation failed, repopulate items from old('items')
        if (old('items')) {
            $listOfItems = collect(old('items'))->map(function($item) {
                return (object)$item;
            });
        }

        // Create a variable with all groups and their subgroups (id => name), subgroups sorted alphabetically (case-insensitive)
        $groupSubgroupMap = $groups->map(function ($group) {
            $sortedSubgroups = $group->subgroups->sortBy(function ($subgroup) {
                return mb_strtolower($subgroup->name);
            });
            return [
                'id' => $group->id,
                'name' => $group->name,
                'type' => $group->type, // Add type here
                'subgroups' => $sortedSubgroups->pluck('name', 'id')->toArray(),
            ];
        });

        return view('entries.entry', compact('groups', 'listOfItems', 'groupSubgroupMap', 'header', 'allBadges'));
    }

    public function store(Request $request)
    {
        // Validate the request data (adjust rules as needed for your fields)
        $validatedData = $request->validate([
            'date' => 'required|date',
            'place' => 'required|string|max:50',
            'location' => 'required|string|max:50',
            'note' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.group_id' => 'required|integer|exists:groups,id',
            'items.*.subgroup_id' => 'required|integer|exists:subgroups,id',
            'items.*.amount' => 'required|numeric',
            'items.*.note' => 'nullable|string',
            'items.*.badges' => 'nullable|array',
            'items.*.badges.*' => 'integer|exists:badges,id',
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
                'place_of_purchase' => $validatedData['place'],
                'location' => $validatedData['location'],
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
                'place_of_purchase' => $validatedData['place'],
                'location' => $validatedData['location'],
                'note' => $validatedData['note'] ?? null,
                'amount' => $validatedData['amount'],
            ]);
        }

        // Create the items (for both create and update)
        foreach ($validatedData['items'] as $itemData) {
            if (floatval($itemData['amount']) == 0) {
                continue;
            }
            $groupType = $groupTypeMap[$itemData['group_id']] ?? null;
            // Save badges as array (will be cast to JSON)
            $badges = $itemData['badges'] ?? [];
            Item::create([
                'header_id' => $header->id,
                'group_id' => $itemData['group_id'],
                'subgroup_id' => $itemData['subgroup_id'],
                'amount' => $itemData['amount'],
                'group_type' => $groupType,
                'note' => $itemData['note'] ?? null,
                'badges' => $badges,
            ]);
        }

        // Redirect or return response
        if ($request->has('header_id')) {
            return redirect()->route('entry.list')->with('success', 'Entry updated successfully.');
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
        // Assuming your Header model has a user_id column
        $headers = Header::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(5); // or ->get() if you don't want pagination

        $dateFormat = $user->date_format ?? 'Y-m-d'; // fallback if not set

        return view('entries.list', compact('headers', 'dateFormat'));
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

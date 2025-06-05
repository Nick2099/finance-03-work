<?php

namespace App\Http\Controllers;

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
    public function create()
    {
        if (!Auth::check()) {
            return redirect('/')->with('error', 'You have to be logged in.');
        }

        $user = Auth::user();
        $collection = $user->collection;
        $groups = $collection->groups()->with('subgroups')->orderBy('name')->get();
        $items = [];

        // Create a variable with all groups and their subgroups (id => name), subgroups sorted alphabetically (case-insensitive)
        $groupSubgroupMap = $groups->map(function ($group) {
            $sortedSubgroups = $group->subgroups->sortBy(function ($subgroup) {
                return mb_strtolower($subgroup->name);
            });
            return [
                'id' => $group->id,
                'name' => $group->name,
                'subgroups' => $sortedSubgroups->pluck('name', 'id')->toArray(),
            ];
        });

        return view('entries.entry', compact('groups', 'items', 'groupSubgroupMap'));
    }

    public function store(Request $request)
    {
        // Debugging line to see the request data
        // dd($request->all());

        // Validate the request data (adjust rules as needed for your fields)
        $validatedData = $request->validate([
            'date' => 'required|date',
            'place' => 'required|string|max:50',
            'location' => 'required|string|max:50',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.group_id' => 'required|integer|exists:groups,id',
            'items.*.subgroup_id' => 'required|integer|exists:subgroups,id',
            'items.*.amount' => 'required|numeric',
        ]);

        // Create the header (main entry)
        $header = \App\Models\Header::create([
            'user_id' => Auth::id(),
            'date' => $validatedData['date'],
            'place_of_purchase' => $validatedData['place'],
            'location' => $validatedData['location'],
            'description' => $validatedData['description'] ?? null,
            'amount' => $validatedData['amount'],
        ]);

        $user = Auth::user();
        $collection = $user->collection;
        $groups = $collection->groups()->get();

        // Create a map of group_id => type for quick lookup
        $groupTypeMap = $groups->pluck('type', 'id');

        // Create the items
        foreach ($validatedData['items'] as $itemData) {
            $groupType = $groupTypeMap[$itemData['group_id']] ?? null;
            \App\Models\Item::create([
                'header_id' => $header->id,
                'group_id' => $itemData['group_id'],
                'subgroup_id' => $itemData['subgroup_id'],
                'amount' => $itemData['amount'],
                'group_type' => $groupType,
                // Add other fields as needed
            ]);
        }

        // Redirect or return response
        return redirect()->route('entry.create')->with('success', 'Entry created successfully.');
    }

    public function getSubgroups($groupId)
    {
        $subgroups = \App\Models\Subgroup::where('group_id', $groupId)->orderBy('name')->get(['id', 'name']);
        return response()->json($subgroups);
    }

    public function suggestPlaces(Request $request)
    {
        $query = $request->input('q');
        $user = Auth::user();

        $places = \App\Models\Header::where('user_id', $user->id)
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

        $places = \App\Models\Header::where('user_id', $user->id)
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
        $headers = \App\Models\Header::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(3); // or ->get() if you don't want pagination

        $dateFormat = $user->date_format ?? 'Y-m-d'; // fallback if not set

        return view('entries.list', compact('headers', 'dateFormat'));
    }
}

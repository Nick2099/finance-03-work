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
            return redirect('/')->with('success', 'You are already logged in.');
        }
        return view('entries.entry');
    }

    public function store(Request $request)
    {
        dd("Store method called");

        // Validate the request data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Create a new entry in the database
        // $entry = Auth::user()->entries()->create($validatedData);

        // Redirect to the entry page or perform any other action
        return redirect()->route('entries.show', ['entry' => $entry->id])
            ->with('success', 'Entry created successfully.');
    }

    public function getSubgroups($groupId)
    {
        $subgroups = \App\Models\Subgroup::where('group_id', $groupId)->orderBy('name')->get(['id', 'name']);
        return response()->json($subgroups);
    }
}

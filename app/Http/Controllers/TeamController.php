<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::orderBy('name')->get();

        return view('teams.index', compact('teams'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:teams,name',
            'captain_name' => 'nullable|string|max:100',
            'contact_number' => 'nullable|string|max:20',
            'purok' => 'nullable|string|max:100',
            'logo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')->store('team-logos', 'public');
        } else {
            $validated['logo_path'] = null;
        }

        Team::create($validated);

        return back()->with('status', 'Team registered.');
    }

    public function destroy(Team $team)
    {
        $team->delete();

        return back()->with('status', 'Team removed.');
    }
}

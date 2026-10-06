<?php

namespace App\Http\Controllers;

use App\Models\Bracket;
use App\Models\Team;
use App\Services\BracketGeneratorService;
use Illuminate\Http\Request;

class BracketController extends Controller
{
    public function create()
    {
        $teams = Team::orderBy('name')->get();

        return view('brackets.create', compact('teams'));
    }

    public function store(Request $request, BracketGeneratorService $generator)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:150',
            'sport_type' => 'required|string|max:50',
            'team_ids' => 'required|array|min:2',
            'team_ids.*' => 'exists:teams,id',
        ]);

        $bracket = Bracket::create([
            'title' => $validated['title'],
            'sport_type' => $validated['sport_type'],
            'format' => 'single_elimination',
            'status' => 'draft',
        ]);

        $generator->generateSingleElimination($bracket, $validated['team_ids']);

        return redirect()
            ->route('brackets.show', $bracket)
            ->with('status', 'Bracket generated for '.count($validated['team_ids']).' teams.');
    }

    public function show(Bracket $bracket)
    {
        $roundsOfMatches = [];

        for ($round = 1; $round <= $bracket->total_rounds; $round++) {
            $roundsOfMatches[$round] = $bracket->matchesByRound($round);
        }

        return view('brackets.show', compact('bracket', 'roundsOfMatches'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\GameMatch;
use App\Services\BracketGeneratorService;
use App\Services\ScheduleService;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function schedule(Request $request, GameMatch $match, ScheduleService $scheduler)
    {
        $validated = $request->validate([
            'scheduled_at' => 'required|date',
            'venue' => 'required|string|max:100',
        ]);

        $isAvailable = $scheduler->isVenueAvailable(
            venue: $validated['venue'],
            scheduledAt: $validated['scheduled_at'],
            ignoreMatchId: $match->id,
        );

        $message = $scheduler->describeAvailability($isAvailable, $validated['venue'], $validated['scheduled_at']);

        if (! $isAvailable) {
            return back()->withErrors(['scheduled_at' => $message]);
        }

        $match->scheduled_at = $validated['scheduled_at'];
        $match->venue = $validated['venue'];
        $match->status = 'scheduled';
        $match->save();

        return back()->with('status', $message);
    }

    public function recordResult(Request $request, GameMatch $match, BracketGeneratorService $generator)
    {
        $validated = $request->validate([
            'team1_score' => 'required|integer|min:0',
            'team2_score' => 'required|integer|min:0',
        ]);

        $match->team1_score = $validated['team1_score'];
        $match->team2_score = $validated['team2_score'];

        if ($validated['team1_score'] > $validated['team2_score']) {
            $match->winner_id = $match->team1_id;
            $match->status = 'completed';
        } elseif ($validated['team2_score'] > $validated['team1_score']) {
            $match->winner_id = $match->team2_id;
            $match->status = 'completed';
        } else {
            // Barangay league rule: ties are not allowed to stand - flag for a rematch
            $match->winner_id = null;
            $match->status = 'tied';
        }

        $match->save();

        if ($match->status === 'completed') {
            $generator->advanceWinner($match);

            return back()->with('status', 'Result recorded. Winner advanced to the next round.');
        } else {
            return back()->with('status', 'Match ended in a tie. Please schedule a tiebreaker.');
        }
    }
}

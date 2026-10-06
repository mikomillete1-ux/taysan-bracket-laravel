<?php

namespace App\Services;

use App\Models\Bracket;
use App\Models\GameMatch;
use InvalidArgumentException;

class BracketGeneratorService
{
    /**
     * Build a single-elimination bracket for the given team IDs.
     *
     * Deliberately written with explicit if / else branches (instead of a
     * log2() one-liner) so the logic is easy to trace and explain during
     * a capstone defense.
     */
    public function generateSingleElimination(Bracket $bracket, array $teamIds): void
    {
        $teamCount = count($teamIds);

        if ($teamCount < 2) {
            throw new InvalidArgumentException('At least 2 teams are required to generate a bracket.');
        }

        // Step 1 - decide bracket size and number of rounds
        if ($teamCount <= 2) {
            $bracketSize = 2;
            $totalRounds = 1;
        } elseif ($teamCount <= 4) {
            $bracketSize = 4;
            $totalRounds = 2;
        } elseif ($teamCount <= 8) {
            $bracketSize = 8;
            $totalRounds = 3;
        } elseif ($teamCount <= 16) {
            $bracketSize = 16;
            $totalRounds = 4;
        } elseif ($teamCount <= 32) {
            $bracketSize = 32;
            $totalRounds = 5;
        } else {
            $bracketSize = 64;
            $totalRounds = 6;
        }

        $bracket->total_rounds = $totalRounds;
        $bracket->status = 'ongoing';
        $bracket->save();

        // Step 2 - place teams into slots in the order they were given
        // (registration order / draw order). Empty slots become byes.
        $slots = [];
        for ($i = 0; $i < $bracketSize; $i++) {
            if ($i < $teamCount) {
                $slots[$i] = $teamIds[$i];
            } else {
                $slots[$i] = null;
            }
        }

        // Step 3 - create round 1 matches, handling byes with if-else
        $matchNumber = 1;
        $advancingFromRound1 = [];

        for ($i = 0; $i < $bracketSize; $i += 2) {
            $teamA = $slots[$i];
            $teamB = $slots[$i + 1];

            if ($teamA !== null && $teamB === null) {
                // Team A has no opponent this round - auto-advances
                $advancingFromRound1[] = $teamA;

                GameMatch::create([
                    'bracket_id' => $bracket->id,
                    'round' => 1,
                    'match_number' => $matchNumber,
                    'team1_id' => $teamA,
                    'team2_id' => null,
                    'winner_id' => $teamA,
                    'status' => 'skipped',
                ]);
            } elseif ($teamA === null && $teamB !== null) {
                // Team B has no opponent this round - auto-advances
                $advancingFromRound1[] = $teamB;

                GameMatch::create([
                    'bracket_id' => $bracket->id,
                    'round' => 1,
                    'match_number' => $matchNumber,
                    'team1_id' => null,
                    'team2_id' => $teamB,
                    'winner_id' => $teamB,
                    'status' => 'skipped',
                ]);
            } elseif ($teamA === null && $teamB === null) {
                // Both empty - only happens with a very small field, skip slot
                GameMatch::create([
                    'bracket_id' => $bracket->id,
                    'round' => 1,
                    'match_number' => $matchNumber,
                    'team1_id' => null,
                    'team2_id' => null,
                    'winner_id' => null,
                    'status' => 'skipped',
                ]);
            } else {
                // Normal match - both teams present
                GameMatch::create([
                    'bracket_id' => $bracket->id,
                    'round' => 1,
                    'match_number' => $matchNumber,
                    'team1_id' => $teamA,
                    'team2_id' => $teamB,
                    'winner_id' => null,
                    'status' => 'pending',
                ]);
            }

            $matchNumber++;
        }

        // Step 4 - create empty placeholder matches for every later round
        $matchesInRound = $bracketSize / 2;

        for ($round = 2; $round <= $totalRounds; $round++) {
            $matchesInRound = $matchesInRound / 2;

            for ($m = 1; $m <= $matchesInRound; $m++) {
                GameMatch::create([
                    'bracket_id' => $bracket->id,
                    'round' => $round,
                    'match_number' => $m,
                    'team1_id' => null,
                    'team2_id' => null,
                    'winner_id' => null,
                    'status' => 'pending',
                ]);
            }
        }

        // Step 5 - drop round-1 bye teams straight into their round-2 slot
        $this->fillNextRoundSlots($bracket, 2, $advancingFromRound1);
    }

    /**
     * Push a list of advancing team IDs into the first open slots of the
     * given round, in order.
     */
    private function fillNextRoundSlots(Bracket $bracket, int $round, array $advancingTeams): void
    {
        if (count($advancingTeams) === 0) {
            return;
        }

        $matches = GameMatch::where('bracket_id', $bracket->id)
            ->where('round', $round)
            ->orderBy('match_number')
            ->get();

        $index = 0;

        foreach ($matches as $match) {
            if ($index >= count($advancingTeams)) {
                break;
            }

            if ($match->team1_id === null) {
                $match->team1_id = $advancingTeams[$index];
                $match->save();
                $index++;
            } elseif ($match->team2_id === null) {
                $match->team2_id = $advancingTeams[$index];
                $match->save();
                $index++;
            } else {
                // Slot already full (shouldn't normally happen), skip it
                continue;
            }
        }
    }

    /**
     * Move a match's winner into the correct slot of the next round.
     * Called after a result is recorded.
     */
    public function advanceWinner(GameMatch $match): void
    {
        if ($match->winner_id === null) {
            return; // no winner yet (tie, or not played)
        }

        $nextRound = $match->round + 1;
        $nextMatchNumber = (int) ceil($match->match_number / 2);

        $nextMatch = GameMatch::where('bracket_id', $match->bracket_id)
            ->where('round', $nextRound)
            ->where('match_number', $nextMatchNumber)
            ->first();

        if ($nextMatch === null) {
            // No further round exists - this was the championship match
            return;
        }

        // Odd match numbers feed team1 of the next match, even feed team2
        if ($match->match_number % 2 !== 0) {
            $nextMatch->team1_id = $match->winner_id;
        } else {
            $nextMatch->team2_id = $match->winner_id;
        }

        $nextMatch->save();

        // If the fed-into match was already a bye slot (opponent missing
        // by design), it can also be auto-completed - left for the admin
        // to trigger manually in this version to keep behavior predictable.
    }
}

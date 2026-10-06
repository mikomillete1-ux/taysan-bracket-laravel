<?php

namespace App\Services;

use App\Models\GameMatch;
use Carbon\Carbon;

class ScheduleService
{
    /**
     * Check whether a venue is free at the requested date/time.
     * Every match is assumed to take $durationMinutes unless told otherwise.
     */
    public function isVenueAvailable(
        string $venue,
        string $scheduledAt,
        int $durationMinutes = 120,
        ?int $ignoreMatchId = null
    ): bool {
        $requestedStart = Carbon::parse($scheduledAt);
        $requestedEnd = $requestedStart->copy()->addMinutes($durationMinutes);

        $existingMatches = GameMatch::where('venue', $venue)
            ->whereNotNull('scheduled_at')
            ->where('status', '!=', 'completed')
            ->get();

        foreach ($existingMatches as $existing) {
            if ($ignoreMatchId !== null && $existing->id === $ignoreMatchId) {
                continue; // don't compare a match against itself when rescheduling
            }

            $existingStart = Carbon::parse($existing->scheduled_at);
            $existingEnd = $existingStart->copy()->addMinutes($durationMinutes);

            if ($requestedStart->lt($existingEnd) && $requestedEnd->gt($existingStart)) {
                // Time ranges overlap - venue is busy
                return false;
            } else {
                // No overlap with this particular match, keep checking others
                continue;
            }
        }

        return true;
    }

    /**
     * Human-readable reason a schedule request was accepted or rejected -
     * useful for flashing a message back to the barangay admin.
     */
    public function describeAvailability(bool $isAvailable, string $venue, string $scheduledAt): string
    {
        $niceDate = Carbon::parse($scheduledAt)->format('M j, Y g:i A');

        if ($isAvailable) {
            return "{$venue} is free on {$niceDate}.";
        } else {
            return "{$venue} is already booked around {$niceDate}. Please pick another time or venue.";
        }
    }
}

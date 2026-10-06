<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameMatch extends Model
{
    use HasFactory;

    // Table is called "matches"; class is GameMatch to avoid clashing
    // with PHP's reserved "Match" keyword.
    protected $table = 'matches';

    protected $fillable = [
        'bracket_id',
        'round',
        'match_number',
        'team1_id',
        'team2_id',
        'team1_score',
        'team2_score',
        'winner_id',
        'scheduled_at',
        'venue',
        'status',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function bracket()
    {
        return $this->belongsTo(Bracket::class);
    }

    public function team1()
    {
        return $this->belongsTo(Team::class, 'team1_id');
    }

    public function team2()
    {
        return $this->belongsTo(Team::class, 'team2_id');
    }

    public function winner()
    {
        return $this->belongsTo(Team::class, 'winner_id');
    }

    /**
     * Human-readable label for the bracket view.
     * Demonstrates simple if-else branching used across the views.
     */
    public function getStatusLabelAttribute(): string
    {
        if ($this->status === 'pending') {
            return 'Waiting for teams';
        } elseif ($this->status === 'scheduled') {
            return 'Scheduled';
        } elseif ($this->status === 'ongoing') {
            return 'Ongoing';
        } elseif ($this->status === 'completed') {
            return 'Completed';
        } elseif ($this->status === 'tied') {
            return 'Tied - rematch needed';
        } else {
            return 'Skipped (bye)';
        }
    }
}

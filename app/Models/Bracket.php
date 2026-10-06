<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bracket extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'sport_type',
        'format',
        'total_rounds',
        'status',
    ];

    public function matches()
    {
        return $this->hasMany(GameMatch::class);
    }

    public function matchesByRound(int $round)
    {
        return $this->matches()->where('round', $round)->orderBy('match_number')->get();
    }
}

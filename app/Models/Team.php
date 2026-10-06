<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'captain_name',
        'contact_number',
        'purok',
        'logo_path',
    ];

    public function matchesAsTeam1()
    {
        return $this->hasMany(GameMatch::class, 'team1_id');
    }

    public function matchesAsTeam2()
    {
        return $this->hasMany(GameMatch::class, 'team2_id');
    }
}

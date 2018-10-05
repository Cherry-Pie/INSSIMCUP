<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Match extends Model
{
    protected $casts = [
        'week' => 'int',
    ];

    protected $fillable = [
        'home_team_id',
        'away_team_id',
        'home_team_score',
        'away_team_score',
        'week',
    ];

    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function scopeFinished($query)
    {
        return $query->whereNotNull('away_team_score')->whereNotNull('home_team_score');
    }

    public function scopeNotFinished($query)
    {
        return $query->whereNull('away_team_score')->whereNull('home_team_score');
    }

    public function scopeWeek($query, int $week)
    {
        return $query->where('week', $week);
    }

    public function isHomeTeam(Team $team)
    {
        return $this->home_team_id == $team->id;
    }
}

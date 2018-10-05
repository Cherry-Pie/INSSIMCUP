<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Team extends Model
{
    protected $fillable = [
        'title',
    ];

    public function homeMatches()
    {
        return $this->hasMany(Match::class, 'home_team_id');
    }

    public function awayMatches()
    {
        return $this->hasMany(Match::class, 'away_team_id');
    }

    public function matches(): Collection
    {
        return $this->homeMatches->merge($this->awayMatches);
    }

    public function getPointsAttribute(): int
    {
        $wonMatchPoints = 3;
        $drawnMatchPoints = 1;

        $points = 0;
        $points += $this->won * $wonMatchPoints;
        $points += $this->drawn * $drawnMatchPoints;

        return $points;
    }

    public function getPlayedAttribute(): int
    {
        $playedHome = $this->homeMatches->filter(function ($match) {
            return !is_null($match->home_team_score) && !is_null($match->away_team_score);
        })->count();
        $playedAway = $this->awayMatches->filter(function ($match) {
            return !is_null($match->home_team_score) && !is_null($match->away_team_score);
        })->count();

        return $playedHome + $playedAway;
    }

    public function getWonAttribute(): int
    {
        $num = 0;
        $num += $this->homeMatches->filter(function ($match) {
            return $match->home_team_score > $match->away_team_score;
        })->count();
        $num += $this->awayMatches->filter(function ($match) {
            return $match->home_team_score < $match->away_team_score;
        })->count();

        return $num;
    }

    public function getDrawnAttribute(): int
    {
        return $this->matches()->filter(function ($match) {
            $isFinished = !is_null($match->home_team_score) && !is_null($match->away_team_score);

            return $isFinished && $match->home_team_score == $match->away_team_score;
        })->count();
    }

    public function getLostAttribute(): int
    {
        return $this->played - $this->won - $this->drawn;
    }

    public function getGoalDifferenceAttribute(): int
    {
        $scoredGoals = 0;
        $missedGoals = 0;
        /** @var Match $match */
        foreach ($this->matches() as $match) {
            if ($match->isHomeTeam($this)) {
                $scoredGoals += $match->home_team_score;
                $missedGoals += $match->away_team_score;
            } else {
                $scoredGoals += $match->away_team_score;
                $missedGoals += $match->home_team_score;
            }
        }

        return $scoredGoals - $missedGoals;
    }

}

<?php

namespace App\Http\Controllers;

use App\Models\Match;
use App\Models\Team;
use App\Services\Simulation;

class TournamentController extends Controller
{
    /**
     * @var Simulation
     */
    private $simulation;

    public function __construct(Simulation $simulation)
    {
        $this->simulation = $simulation;
    }

    public function prepare()
    {
        Match::truncate();

        $teamsIds = Team::pluck('id');

        $allWeekMatches = $this->simulation->pairTeamsToMatches($teamsIds->toArray());
        $weekNumber = 1;
        foreach ($allWeekMatches as $weekMatches) {
            foreach ($weekMatches as $match) {
                list($homeTeamId, $awayTeamId) = $match;
                Match::create([
                    'home_team_id' => $homeTeamId,
                    'away_team_id' => $awayTeamId,
                    'week'         => $weekNumber,
                ]);
            }
            $weekNumber++;
        }

        return redirect()->back();
    }

}

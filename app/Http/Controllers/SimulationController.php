<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateMatchScoreRequest;
use App\Models\Match;
use App\Models\Team;
use App\Services\Simulation;

class SimulationController extends Controller
{

    /**
     * @var Simulation
     */
    private $simulation;

    public function __construct(Simulation $simulation)
    {
        $this->simulation = $simulation;
    }

    public function simulateNextWeek()
    {
        $week = $this->simulation->getNextWeek();
        $this->simulation->simulateWeek($week);
        $standings = $this->simulation->getStandings($week);

        $matches = Match::with('homeTeam', 'awayTeam')->week($week)->get();
        $teams = Team::get()->sortForStandings();
        $isAllMatchesFinished = !Match::notFinished()->count();

        return response()->json([
            'week'      => $week,
            'matches'   => $matches,
            'standings' => $standings,
            'is_all_matches_finished' => $isAllMatchesFinished,
            'league_table_html' => view('inc.league_table', compact('teams'))->render(),
            'match_results_html' => view('inc.match_results', compact('matches', 'week'))->render(),
            'league_predictions_html' => view('inc.predictions', compact('week', 'teams'))->render(),
        ]);
    }

    public function updateMatch(UpdateMatchScoreRequest $request)
    {
        Match::where('id', $request->get('pk'))->update([
            $request->get('name') => $request->get('value'),
        ]);

        $week = $this->simulation->getCurrentWeek();
        $teams = Team::get()->sortForStandings();

        return response()->json([
            'league_table_html' => view('inc.league_table', compact('teams'))->render(),
            'league_predictions_html' => view('inc.predictions', compact('week', 'teams'))->render(),
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Match;
use App\Models\Team;
use App\Services\Simulation;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * @var Simulation
     */
    private $simulation;

    public function __construct(Simulation $simulation)
    {
        $this->simulation = $simulation;
    }

    public function home()
    {
        $week = $this->simulation->getCurrentWeek();
        $teams = Team::get()->sortForStandings();
        $matches = Match::with('homeTeam', 'awayTeam')->week($week)->get();

        return view('index', compact('teams', 'matches', 'week'));
    }
}

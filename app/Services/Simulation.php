<?php

namespace App\Services;


use App\Helpers\Percentage;
use App\Helpers\Permutations;
use App\Models\Match;
use App\Models\Team;

class Simulation
{
    /**
     * @var Percentage
     */
    private $percentageHelper;

    public function __construct(Percentage $percentageHelper)
    {
        $this->percentageHelper = $percentageHelper;
    }

    public function simulateWeek(int $week)
    {
        $matches = Match::week($week)->get();

        foreach ($matches as $match) {
            list($homeTeamScore, $awayTeamScore) = $this->simulateMatchScore($match->homeTeam, $match->awayTeam);
            $match->update([
                'home_team_score' => $homeTeamScore,
                'away_team_score' => $awayTeamScore,
            ]);
        }
    }

    public function getStandings(int $week): array
    {
        $data = [
            'matches' => [],
            'teams'   => [],
        ];

        $matches = Match::week($week)->get();
        foreach ($matches as $match) {
            $data['matches'][] = [
                'home_team_title' => $match->homeTeam->title,
                'away_team_title' => $match->awayTeam->title,
                'home_team_score' => $match->home_team_score,
                'away_team_score' => $match->away_team_score,
            ];
        }

        $teams = Team::get()->sortForStandings();
        foreach ($teams as $team) {
            $data['teams'][] = [
                'title'  => $team->title,
                'points' => $team->points,
                'played' => $team->played,
                'won'    => $team->won,
                'drawn'  => $team->drawn,
                'lost'   => $team->lost,
            ];
        }

        return $data;
    }

    private function simulateMatchScore(Team $homeTeam, Team $awayTeam): array
    {
        // TODO:
        return [
            rand(0,4),
            rand(0,4),
        ];
    }

    public function pairTeamsToMatches(array $teamsIds): array
    {
        $firstHalf = Permutations::paired($teamsIds);
        $secondHalf = array_map(function($weekMatches) {
            foreach ($weekMatches as &$match) {
                $match = array_reverse($match);
            }
            return $weekMatches;
        }, $firstHalf);

        return array_merge($firstHalf, $secondHalf);
    }

    public function getCurrentWeek(): int
    {
        return (int) Match::finished()->orderBy('week', 'desc')->value('week');
    }

    public function getNextWeek(): int
    {
        $week = $this->getCurrentWeek();

        return $week + 1;
    }

    public function getLeagueWinRate(): array
    {
        $teamsInfo = $this->getTeamsPointsInfo();

        $possibleResults = $this->getAllPossibleResultsForMatches();
        $variations = $this->getAllPossibleResultsForWeeks($possibleResults);

        $variations = $this->calculateTeamPointsForWeekVariations($variations, $teamsInfo);
        $teams = $this->countTeamWinLeague($variations, $teamsInfo);

        $percentage = $this->calculateTeamWinChancesInPercentage($teams);

        return $percentage;
    }

    private function getTeamsPointsInfo(): array
    {
        $info = [];

        $teams = Team::get()->sortForStandings();
        foreach ($teams as $team) {
            $info[$team->id] = [
                'points' => $team->points,
            ];
        }

        return $info;
    }

    private function getAllPossibleResultsForMatches(): array
    {
        $results = [];

        $matchesGroups = Match::with('homeTeam', 'awayTeam')->notFinished()->get()->groupBy('week');
        foreach ($matchesGroups as $week => $matches) {
            $matchType = 'first';
            foreach ($matches as $match) {
                $homeTeam = $match->homeTeam;
                $awayTeam = $match->awayTeam;
                // home win
                $results[$week][$matchType][] = [
                    'ident' => $homeTeam->id .'+'. $awayTeam->id .'-30',
                    'home_points' => 3,
                    'away_points' => 0,
                    'home' => $homeTeam->id,
                    'away' => $awayTeam->id,
                ];
                // draw
                $results[$week][$matchType][] = [
                    'ident' => $homeTeam->id .'+'. $awayTeam->id .'-11',
                    'home_points' => 1,
                    'away_points' => 1,
                    'home' => $homeTeam->id,
                    'away' => $awayTeam->id,
                ];
                // away win
                $results[$week][$matchType][] = [
                    'ident' => $homeTeam->id .'+'. $awayTeam->id .'-03',
                    'home_points' => 0,
                    'away_points' => 3,
                    'home' => $homeTeam->id,
                    'away' => $awayTeam->id,
                ];
                $matchType = 'second';
            }
        }

        return $results;
    }

    private function getAllPossibleResultsForWeeks($possibleResults): array
    {
        $results = [];

        foreach ($possibleResults as $week => $matchesVariations) {
            foreach ($matchesVariations['first'] as $firstMatchVariations) {
                foreach ($matchesVariations['second'] as $secondMatchVariations) {
                    $ident = $firstMatchVariations['ident'] .'_'. $secondMatchVariations['ident'];
                    $results[$week][$ident] = [
                        $firstMatchVariations['home']  => $firstMatchVariations['home_points'],
                        $firstMatchVariations['away']  => $firstMatchVariations['away_points'],
                        $secondMatchVariations['home'] => $secondMatchVariations['home_points'],
                        $secondMatchVariations['away'] => $secondMatchVariations['away_points'],
                    ];
                }
            }
        }

        return $results;
    }

    private function calculateTeamPointsForWeekVariations($variations, $teamsInfo): array
    {
        $variations = cartesian_product($variations);

        $teamPointsPerVariation = $this->sumTeamPointsByWeekVariations($variations, $teamsInfo);
        $teamPointsPerVariation = $this->sortByTeamPoints($teamPointsPerVariation);

        return $teamPointsPerVariation;
    }

    private function sortByTeamPoints($teamPointsPerVariation): array
    {
        array_walk($teamPointsPerVariation, function(&$item, $key) {
            uasort($item, function($a, $b) {
                return $b - $a;
            });
        });

        return $teamPointsPerVariation;
    }

    private function countTeamWinLeague($teamPointsPerVariation, $teamsInfo): array
    {
        $teams = array_combine(
            array_keys($teamsInfo),
            array_fill(0, count($teamsInfo), 0)
        );
        foreach ($teamPointsPerVariation as $variation) {
            $winnerTeam = array_keys($variation)[0];
            $teams[$winnerTeam]++;
        }

        return $teams;
    }

    private function calculateTeamWinChancesInPercentage($teams): array
    {
        $percentage = $this->percentageHelper->data($teams)->get('rounded');
        uasort($percentage, function($a, $b) {
            return $b - $a;
        });

        return $percentage;
    }

    private function sumTeamPointsByWeekVariations($variations, $teamsInfo): array
    {
        $teamPointsPerVariation = [];
        foreach ($variations as $index => $weekVariation) {
            foreach ($teamsInfo as $idTeam => $team) {
                $teamPointsPerVariation[$index][$idTeam] = array_sum(array_column($weekVariation, $idTeam)) + $team['points'];
            }
        }

        return $teamPointsPerVariation;
    }


}
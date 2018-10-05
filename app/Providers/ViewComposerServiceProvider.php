<?php

namespace App\Providers;

use App\Models\Match;
use App\Services\Simulation;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('index', function ($view) {
            $isAllMatchesFinished = !Match::notFinished()->count();

            $view->currentWeekNumber = app(Simulation::class)->getCurrentWeek();
            $view->isAllMatchesFinished = $isAllMatchesFinished;
            $view->maxWeekNumber = Match::orderBy('week', 'desc')->value('week');
        });

        View::composer('inc.predictions', function ($view) {
            $service = app(Simulation::class);
            $currentWeek = $service->getCurrentWeek();
            $displayAfterWeek = config('predictions.show_after_week');
            $shouldDisplayPredictions = $currentWeek >= $displayAfterWeek;
            $isAllMatchesFinished = !Match::notFinished()->count();

            $winRate = [];
            if (!$isAllMatchesFinished && $currentWeek >= $displayAfterWeek) {
                $winRate = $service->getLeagueWinRate();
            }
            $view->winRate = $winRate;
            $view->displayAfterWeek = $displayAfterWeek;
            $view->shouldDisplayPredictions = $shouldDisplayPredictions;
            $view->isAllMatchesFinished = $isAllMatchesFinished;
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}

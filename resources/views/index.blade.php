@extends('layouts.main')

@section('content')


<div class="row">

    <div class="col-4 text-center">
        <h3>League Table</h3>
        <br>

        @include('inc.league_table')

        @if (!$isAllMatchesFinished)
            <a id="play-all-btn" class="btn btn-primary float-left" href="#" role="button">Play All</a>
            <a id="next-week-btn" class="btn btn-primary float-right" href="#" role="button">Next Week</a>
        @endif
    </div>


    <div id="match-results" class="col-4 text-center">
        @include('inc.match_results')
    </div>


    <div id="league-predictions" class="col-4 text-center">
        @include('inc.predictions')
    </div>

</div>


@endsection

@push('scripts')
    <script>
    App.currentWeek = {{ $currentWeekNumber }};
    App.maxWeek = {{ $maxWeekNumber }};
    </script>
@endpush

@if ($isAllMatchesFinished)
    <h3>Press button to start new tournament.</h3>
    <br>
    <a class="btn btn-primary" href="/tournament/prepare" role="button">Prepare New Tournament</a>
@elseif ($shouldDisplayPredictions)

    <h3>{{ $week }}<sup>th</sup> Week Predictions of Championship</h3>
    <br>

    <table class="table table-hover">
        <tbody>
        @foreach ($winRate as $idTeam => $percentage)
            <tr>
                <td>{{ $teams->where('id', $idTeam)->first()->title }}</td>
                <td>%{{ $percentage }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

@else
    <h3>Predictions will be shown after {{ $displayAfterWeek }}<sup>th</sup> Week</h3>
@endif

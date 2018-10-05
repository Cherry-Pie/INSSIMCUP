

<table id="league-table" class="table table-hover">
    <thead>
    <tr>
        <th><abbr title="Position">Pos</abbr></th>
        <th>Team</th>
        <th><abbr title="Points">Pts</abbr></th>
        <th><abbr title="Played">Pld</abbr></th>
        <th><abbr title="Won">W</abbr></th>
        <th><abbr title="Drawn">D</abbr></th>
        <th><abbr title="Lost">L</abbr></th>
        <th><abbr title="Goal Difference">GD</abbr></th>
    </tr>
    </thead>
    <tbody>
    @foreach ($teams as $team)
        <tr>
            <th>{{ $loop->iteration }}</th>
            <td>{{ $team->title }}</td>
            <td>{{ $team->points }}</td>
            <td>{{ $team->played }}</td>
            <td>{{ $team->won }}</td>
            <td>{{ $team->drawn }}</td>
            <td>{{ $team->lost }}</td>
            <td>{{ $team->goal_difference }}</td>
        </tr>
    @endforeach
    </tbody>
</table>


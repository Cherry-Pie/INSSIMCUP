<h3>{{ $week }}<sup>th</sup> Week Match Result</h3>
<br>

<table>
    <tbody>
    @foreach ($matches as $match)
        <tr>
            <td class="text-left">{{ $match->homeTeam->title }}</td>
            <td class="text-center">
                <a href="#" class="home-score" data-name="home_team_score" data-type="text" data-pk="{{ $match->id }}" data-url="/update-match-score" data-title="Enter new score for {{ $match->homeTeam->title }} team">
                    {{ $match->home_team_score }}
                </a>
                    :
                <a href="#" class="away-score" data-name="away_team_score" data-type="text" data-pk="{{ $match->id }}" data-url="/update-match-score" data-title="Enter new score for {{ $match->awayTeam->title }} team">
                    {{ $match->away_team_score }}
                </a>
            </td>
            <td class="text-right">{{ $match->awayTeam->title }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

@extends('layouts.app')

@section('title', $bracket->title.' | Barangay Taysan')

@section('content')
    <h2>{{ $bracket->title }}</h2>
    <p>{{ ucfirst($bracket->sport_type) }} &middot; Single elimination &middot; {{ $bracket->total_rounds }} round(s)</p>

    @foreach ($roundsOfMatches as $roundNumber => $matches)
        <h3>Round {{ $roundNumber }}</h3>
        <table>
            <thead>
                <tr>
                    <th>Match</th>
                    <th>Team 1</th>
                    <th>Team 2</th>
                    <th>Score</th>
                    <th>Status</th>
                    <th>Schedule</th>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($matches as $match)
                    <tr>
                        <td>#{{ $match->match_number }}</td>
                        <td>{{ $match->team1->name ?? 'TBD' }}</td>
                        <td>{{ $match->team2->name ?? 'TBD' }}</td>
                        <td>
                            @if ($match->team1_score !== null)
                                {{ $match->team1_score }} - {{ $match->team2_score }}
                            @else
                                —
                            @endif
                        </td>
                        <td>{{ $match->status_label }}</td>
                        <td>
                            @if ($match->team1_id && $match->team2_id && $match->status !== 'completed')
                                <form method="POST" action="{{ route('matches.schedule', $match) }}">
                                    @csrf
                                    <input type="datetime-local" name="scheduled_at" required
                                           value="{{ $match->scheduled_at ? $match->scheduled_at->format('Y-m-d\TH:i') : '' }}">
                                    <input type="text" name="venue" placeholder="Venue (e.g. Barangay Covered Court)"
                                           value="{{ $match->venue }}" required>
                                    <button type="submit">Save</button>
                                </form>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if ($match->team1_id && $match->team2_id && $match->status !== 'completed')
                                <form method="POST" action="{{ route('matches.result', $match) }}">
                                    @csrf
                                    <input type="number" name="team1_score" min="0" placeholder="T1" style="width:50px;" required>
                                    <input type="number" name="team2_score" min="0" placeholder="T2" style="width:50px;" required>
                                    <button type="submit">Save Result</button>
                                </form>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
@endsection

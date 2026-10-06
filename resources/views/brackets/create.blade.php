@extends('layouts.app')

@section('title', 'Create Bracket | Barangay Taysan')

@section('content')
    <h2>Create a New Tournament Bracket</h2>

    @if ($teams->count() < 2)
        <p>You need at least 2 registered teams before generating a bracket.
           <a href="{{ route('teams.index') }}">Register teams first</a>.</p>
    @else
        <form method="POST" action="{{ route('brackets.store') }}">
            @csrf
            <p>
                <input type="text" name="title" placeholder="e.g. Barangay Taysan Basketball League 2026" required style="width: 100%;">
            </p>
            <p>
                <select name="sport_type" required>
                    <option value="basketball">Basketball</option>
                    <option value="volleyball">Volleyball</option>
                    <option value="sepak takraw">Sepak Takraw</option>
                    <option value="badminton">Badminton</option>
                    <option value="other">Other</option>
                </select>
            </p>

            <p>Select participating teams (order = seeding order):</p>
            @foreach ($teams as $team)
                <label style="display:block; margin-bottom:4px;">
                    <input type="checkbox" name="team_ids[]" value="{{ $team->id }}">
                    {{ $team->name }} @if($team->purok) ({{ $team->purok }}) @endif
                </label>
            @endforeach

            <p><button type="submit">Generate Bracket</button></p>
        </form>
    @endif
@endsection

@extends('layouts.app')

@section('title', 'Teams | Barangay Taysan')

@section('content')
    <h2>Registered Teams</h2>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Captain</th>
                <th>Contact</th>
                <th>Purok</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($teams as $team)
                <tr>
                    <td>{{ $team->name }}</td>
                    <td>{{ $team->captain_name ?? '—' }}</td>
                    <td>{{ $team->contact_number ?? '—' }}</td>
                    <td>{{ $team->purok ?? '—' }}</td>
                    <td>
                        <form class="inline" method="POST" action="{{ route('teams.destroy', $team) }}"
                              onsubmit="return confirm('Remove this team?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Remove</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">No teams registered yet.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Register a Team</h2>
    <form method="POST" action="{{ route('teams.store') }}" enctype="multipart/form-data">
        @csrf
        <p><input type="text" name="name" placeholder="Team name" required></p>
        <p><input type="text" name="captain_name" placeholder="Team captain"></p>
        <p><input type="text" name="contact_number" placeholder="Contact number"></p>
        <p><input type="text" name="purok" placeholder="Purok / sitio"></p>
        <p><input type="file" name="logo" accept="image/*"></p>
        <button type="submit">Register Team</button>
    </form>
@endsection

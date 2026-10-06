<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Barangay Taysan Game Scheduling & Bracketing')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --green: #0b3d2e;
            --gold: #f2c14e;
            --bg: #f6f5f0;
            --text: #1e1e1e;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
        }
        header {
            background: var(--green);
            color: #fff;
            padding: 16px 24px;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        header img { height: 56px; width: 56px; }
        header h1 { font-size: 18px; margin: 0; }
        header p { margin: 2px 0 0; font-size: 13px; color: var(--gold); }
        nav { background: #0e4d3a; padding: 10px 24px; }
        nav a { color: #fff; text-decoration: none; margin-right: 18px; font-size: 14px; }
        nav a:hover { color: var(--gold); }
        main { padding: 24px; max-width: 960px; margin: 0 auto; }
        .status { background: #e4f4e8; border-left: 4px solid var(--green); padding: 10px 14px; margin-bottom: 16px; font-size: 14px; }
        .errors { background: #fbe7e7; border-left: 4px solid #b3261e; padding: 10px 14px; margin-bottom: 16px; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { text-align: left; padding: 8px 10px; border-bottom: 1px solid #ddd; font-size: 14px; }
        th { background: #eee; }
        form.inline { display: inline; }
        input, select, button { font-size: 14px; padding: 6px 8px; }
        button { background: var(--green); color: #fff; border: none; border-radius: 4px; cursor: pointer; padding: 8px 14px; }
        button:hover { background: #0e4d3a; }
    </style>
</head>
<body>
    <header>
        <img src="{{ asset('images/taysan-logo-placeholder.svg') }}" alt="Barangay Taysan logo">
        <div>
            <h1>Barangay Taysan &mdash; Game Scheduling &amp; Automated Bracketing System</h1>
            <p>Legazpi City, Albay</p>
        </div>
    </header>
    <nav>
        <a href="{{ route('teams.index') }}">Teams</a>
        <a href="{{ route('brackets.create') }}">Create Bracket</a>
    </nav>
    <main>
        @if (session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="errors">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>

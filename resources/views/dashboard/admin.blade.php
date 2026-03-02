<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        :root {
            --bg: #0F172A;
            --card: #111827;
            --accent: #22D3EE;
            --accent2: #A78BFA;
            --text: #E5E7EB;
            --muted: #94A3B8;
            --border: #1F2937;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            color: var(--text);
            background:
                radial-gradient(700px 350px at 0% 0%, rgba(34, 211, 238, 0.2), transparent),
                radial-gradient(700px 350px at 100% 100%, rgba(167, 139, 250, 0.2), transparent),
                var(--bg);
        }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 10;
            background: rgba(17, 24, 39, 0.9);
            backdrop-filter: blur(6px);
            border-bottom: 1px solid var(--border);
        }

        .site-header-inner {
            max-width: 1100px;
            margin: 0 auto;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .brand {
            font-weight: 800;
            color: var(--accent);
            letter-spacing: 0.02em;
        }

        .nav {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .nav-link {
            text-decoration: none;
            color: #cbd5e1;
            font-size: 0.88rem;
            font-weight: 700;
            padding: 8px 10px;
            border-radius: 10px;
            border: 1px solid transparent;
        }

        .nav-link.active {
            color: var(--accent);
            background: rgba(34, 211, 238, 0.08);
            border-color: rgba(34, 211, 238, 0.25);
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 24px 16px 32px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            gap: 12px;
        }

        .title {
            margin: 0;
            font-size: 1.8rem;
            font-weight: 800;
        }

        .subtitle {
            margin: 4px 0 0;
            color: var(--muted);
            font-size: 0.95rem;
        }

        .logout-btn {
            border: 1px solid #ef4444;
            color: #fecaca;
            background: #7f1d1d;
            border-radius: 10px;
            padding: 9px 14px;
            font-weight: 700;
            cursor: pointer;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }

        .card {
            background: rgba(17, 24, 39, 0.92);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 14px;
            box-shadow: 0 8px 18px rgba(0, 0, 0, 0.25);
        }

        .label {
            margin: 0 0 8px;
            color: var(--muted);
            font-size: 0.82rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .value {
            margin: 0;
            font-size: 1.6rem;
            font-weight: 800;
        }

        .value.c1 { color: var(--accent); }
        .value.c2 { color: #FDE68A; }
        .value.c3 { color: #86EFAC; }
        .value.c4 { color: var(--accent2); }

        .section {
            margin-top: 16px;
            background: rgba(17, 24, 39, 0.92);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 14px;
        }

        .section h2 {
            margin: 0 0 8px;
            font-size: 1rem;
        }

        .section p {
            margin: 0;
            color: var(--muted);
        }

        @media (max-width: 950px) {
            .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }

        @media (max-width: 560px) {
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="site-header-inner">
            <div class="brand">Auction Grid Battle</div>
            <nav class="nav">
                <a href="{{ route('dashboard') }}" class="nav-link active">Dashboard</a>
                <a href="#" class="nav-link">Characters</a>
                <a href="{{ route('auction') }}" class="nav-link">Auctions</a>
                <a href="#" class="nav-link">Leaderboard</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="topbar">
            <div>
                <h1 class="title">Admin Dashboard</h1>
                <p class="subtitle">Welcome, {{ $user->name }}. You are logged in as system administrator (User ID: {{ $user->id }}).</p>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-btn" type="submit">Logout</button>
            </form>
        </div>

        <div class="grid">
            <div class="card">
                <p class="label">Total Users</p>
                <p class="value c1">{{ $stats['users'] }}</p>
            </div>
            <div class="card">
                <p class="label">Total Teams</p>
                <p class="value c2">{{ $stats['teams'] }}</p>
            </div>
            <div class="card">
                <p class="label">Total Characters</p>
                <p class="value c3">{{ $stats['characters'] }}</p>
            </div>
            <div class="card">
                <p class="label">Character Roles</p>
                <p class="value c4">{{ $stats['character_roles'] }}</p>
            </div>
        </div>

        <div class="section">
            <h2>Admin Controls</h2>
            <p>This is the admin area. You can now connect moderator features here, such as character management, auction controls, battles, and leaderboard management.</p>
        </div>
    </div>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        :root {
            --bg: #EEF2FF;
            --card: #E0E7FF;
            --primary: #7C3AED;
            --secondary: #10B981;
            --gold: #FBBF24;
            --danger: #EF4444;
            --text: #1F2937;
            --muted: #6B7280;
            --grid: #CBD5E1;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            color: var(--text);
            background:
                radial-gradient(800px 450px at 0% 0%, rgba(124, 58, 237, 0.14), transparent),
                radial-gradient(800px 450px at 100% 100%, rgba(16, 185, 129, 0.14), transparent),
                var(--bg);
        }

        .site-header {
            position: sticky;
            top: 0;
            z-index: 10;
            background: rgba(224, 231, 255, 0.9);
            backdrop-filter: blur(6px);
            border-bottom: 1px solid var(--grid);
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
            color: var(--primary);
            letter-spacing: 0.02em;
        }

        .nav {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .nav-link {
            text-decoration: none;
            color: #374151;
            font-size: 0.88rem;
            font-weight: 700;
            padding: 8px 10px;
            border-radius: 10px;
            border: 1px solid transparent;
        }

        .nav-link.active {
            color: var(--primary);
            background: #f5f3ff;
            border-color: rgba(124, 58, 237, 0.2);
        }

        .container {
            max-width: 1100px;
            margin: 28px auto;
            padding: 0 16px;
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
            border: none;
            background: #fff;
            color: var(--danger);
            border-radius: 10px;
            padding: 10px 14px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 14px;
        }

        .card {
            background: var(--card);
            border: 1px solid rgba(124, 58, 237, 0.16);
            border-radius: 14px;
            padding: 14px;
            box-shadow: 0 8px 18px rgba(30, 41, 59, 0.08);
        }

        .label {
            color: var(--muted);
            font-size: 0.82rem;
            margin: 0 0 6px;
        }

        .value {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 800;
        }

        .value.primary { color: var(--primary); }
        .value.secondary { color: var(--secondary); }
        .value.gold { color: #b45309; }

        .battlefield {
            margin-top: 14px;
            background: #F8FAFF;
            border: 1px solid var(--grid);
            border-radius: 14px;
            padding: 12px;
        }

        .battlefield-title {
            margin: 0 0 10px;
            color: var(--muted);
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .board {
            display: grid;
            grid-template-columns: repeat(10, minmax(0, 1fr));
            gap: 4px;
        }

        .cell {
            aspect-ratio: 1 / 1;
            border: 1px solid var(--grid);
            border-radius: 6px;
            background: #fff;
        }

        .zone-top { background: #ede9fe; }
        .zone-bottom { background: #d1fae5; }

        .info-section {
            margin-top: 14px;
        }

        .section-title {
            margin: 0 0 8px;
            color: var(--muted);
            font-size: 0.9rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .roster {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .character-card {
            background: var(--card);
            border: 1px solid rgba(124, 58, 237, 0.16);
            border-radius: 12px;
            padding: 10px;
            box-shadow: 0 4px 10px rgba(30, 41, 59, 0.06);
        }

        .character-name {
            margin: 0 0 4px;
            font-size: 0.92rem;
            font-weight: 700;
            color: var(--primary);
        }

        .character-type {
            margin: 0 0 4px;
            font-size: 0.75rem;
            color: var(--muted);
        }

        .character-stats {
            margin: 0;
            font-size: 0.75rem;
            color: #4b5563;
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .stat-label { color: var(--muted); }
        .stat-value { font-weight: 700; }

        .balance-section {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .balance-card {
            background: var(--card);
            border: 1px solid rgba(124, 58, 237, 0.16);
            border-radius: 12px;
            padding: 12px;
        }

        @media (max-width: 900px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }

        .site-footer {
            max-width: 1100px;
            margin: 18px auto 24px;
            padding: 12px 16px;
            color: var(--muted);
            font-size: 0.85rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="site-header-inner">
            <div class="brand">Auction Grid Battle</div>
            <nav class="nav">
                <a href="{{ route('dashboard') }}" class="nav-link active">Dashboard</a>
                <a href="{{ route('auction') }}" class="nav-link">Auction</a>
                <a href="#" class="nav-link">Deck</a>
                <a href="#" class="nav-link">Leaderboard</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="topbar">
            <div>
                <h1 class="title">User Dashboard</h1>
                <p class="subtitle">Welcome back, {{ $user->name }}. Prepare your team for the next battle.</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-btn" type="submit">Logout</button>
            </form>
        </div>

        <div class="grid">
            <div class="card">
                <p class="label">User Type</p>
                <p class="value primary">{{ $user->role_id }}</p>
            </div>
            <div class="card">
                <p class="label">Team ID</p>
                <p class="value secondary">{{ $user->team_id ?? 'Not Assigned' }}</p>
            </div>
            <div class="card">
                <p class="label">Coins</p>
                <p class="value gold">1200</p>
            </div>
        </div>

        <div class="info-section">
            <p class="section-title">Your Characters</p>
            <div class="roster">
                <div class="character-card">
                    <p class="character-name">Valorian Knight</p>
                    <p class="character-type">Tank</p>
                    <div class="character-stats">
                        <div class="stat-row">
                            <span class="stat-label">HP:</span>
                            <span class="stat-value">280</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">DMG:</span>
                            <span class="stat-value">65</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">DEF:</span>
                            <span class="stat-value">92</span>
                        </div>
                    </div>
                </div>
                <div class="character-card">
                    <p class="character-name">Mystic Sage</p>
                    <p class="character-type">Mage</p>
                    <div class="character-stats">
                        <div class="stat-row">
                            <span class="stat-label">HP:</span>
                            <span class="stat-value">140</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">DMG:</span>
                            <span class="stat-value">185</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">SPD:</span>
                            <span class="stat-value">88</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-section">
            <p class="section-title">Coin Balance</p>
            <div class="balance-section">
                <div class="balance-card">
                    <p class="label">Total Coins</p>
                    <p class="value gold">1200</p>
                </div>
                <div class="balance-card">
                    <p class="label">Spent on Auction</p>
                    <p class="value danger">800</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="site-footer">
        Fantasy season in progress • Auction Grid Battle Dashboard • {{ now()->format('Y') }}
    </footer>
</body>
</html>

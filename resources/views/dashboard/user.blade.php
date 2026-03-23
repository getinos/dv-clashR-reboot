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
                <a href="{{ route('deck') }}" class="nav-link">Deck</a>
                <a href="{{ route('battleground') }}" class="nav-link">Battle Ground</a>
                <a href="#" class="nav-link">Leaderboard</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="topbar">
            <div>
                <h1 class="title">🎮 Team Dashboard</h1>
                <p class="subtitle">Welcome back, {{ $user->name }}. @if($teamInfo) Team: {{ $teamInfo->name }} @endif</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-btn" type="submit">Logout</button>
            </form>
        </div>

        <div class="grid">
            <div class="card">
                <p class="label">Team Name</p>
                <p class="value primary">{{ $teamInfo->name ?? 'No Team' }}</p>
            </div>
            <div class="card">
                <p class="label">Characters Owned</p>
                <p class="value secondary">{{ $characterCount }}</p>
            </div>
            <div class="card">
                <p class="label">Total Investment</p>
                <p class="value gold">{{ number_format($totalSpent) }} 💰</p>
            </div>
        </div>

        @if($teamInfo && $teamInfo->description)
        <div class="card" style="margin-bottom: 14px;">
            <p class="label">Team Description</p>
            <p style="margin: 8px 0 0; color: #4b5563; font-size: 0.95rem;">{{ $teamInfo->description }}</p>
        </div>
        @endif

        <div class="info-section">
            <p class="section-title">👥 Team Members ({{ $teamMembers->count() }})</p>
            <div class="roster">
                @forelse($teamMembers as $member)
                <div class="character-card">
                    <p class="character-name">{{ $member->name }}</p>
                    <p class="character-type">{{ $member->role_name }}</p>
                    <p class="character-stats" style="margin-top: 4px;">
                        @if($member->id === $user->id)
                        <span style="background: #dbeafe; color: #1e40af; padding: 2px 8px; border-radius: 6px; font-size: 0.7rem; font-weight: 700;">You</span>
                        @endif
                    </p>
                </div>
                @empty
                <p style="grid-column: 1/-1; color: var(--muted); text-align: center; padding: 20px;">No team members found.</p>
                @endforelse
            </div>
        </div>

        <div class="info-section">
            <p class="section-title">🃏 Your Deck Characters ({{ $characterCount }})</p>
            @if($deckCharacters->isEmpty())
            <div class="card" style="text-align: center; padding: 40px;">
                <p style="font-size: 3rem; margin-bottom: 12px; opacity: 0.3;">🎴</p>
                <p style="color: var(--muted); margin: 0;">No characters in your deck yet. Head to the auction to start bidding!</p>
            </div>
            @else
            <div class="roster">
                @foreach($deckCharacters as $character)
                <div class="character-card">
                    <p class="character-name">{{ $character->name }}</p>
                    <p class="character-type">{{ $character->role_name ?? 'Unknown' }}</p>
                    <div class="character-stats">
                        <div class="stat-row">
                            <span class="stat-label">❤️ HP:</span>
                            <span class="stat-value">{{ $character->hp }}</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">⚔️ DMG:</span>
                            <span class="stat-value">{{ $character->damage }}</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">💨 SPD:</span>
                            <span class="stat-value">{{ $character->speed }}</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">🎯 RNG:</span>
                            <span class="stat-value">{{ $character->range }}</span>
                        </div>
                        <div class="stat-row">
                            <span class="stat-label">💰 Cost:</span>
                            <span class="stat-value">{{ $character->base_price }}</span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <div class="info-section">
            <p class="section-title">💰 Financial Stats</p>
            <div class="balance-section">
                <div class="balance-card">
                    <p class="label">Total Investment</p>
                    <p class="value gold">{{ number_format($totalSpent) }}</p>
                    <p style="font-size: 0.75rem; color: var(--muted); margin-top: 6px;">Spent on {{ $characterCount }} character{{ $characterCount !== 1 ? 's' : '' }}</p>
                </div>
                <div class="balance-card">
                    <p class="label">Average Cost per Character</p>
                    <p class="value primary">{{ $characterCount > 0 ? number_format($totalSpent / $characterCount, 0) : '0' }}</p>
                    <p style="font-size: 0.75rem; color: var(--muted); margin-top: 6px;">Avg. auction price</p>
                </div>
            </div>
        </div>
    </div>

    <footer class="site-footer">
        Fantasy season in progress • Auction Grid Battle Dashboard • {{ now()->format('Y') }}
    </footer>
</body>
</html>

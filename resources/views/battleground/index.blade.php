<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Battle Ground - Auction Grid Battle</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            min-height: 100vh;
            color: white;
        }

        /* Header */
        .site-header {
            background: rgba(30, 41, 59, 0.95);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
        }

        .site-header-inner {
            max-width: 1600px;
            margin: 0 auto;
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand {
            font-size: 1.4rem;
            font-weight: 900;
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .nav {
            display: flex;
            gap: 8px;
        }

        .nav-link {
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            color: rgba(255, 255, 255, 0.7);
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1);
            color: white;
        }

        .nav-link.active {
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            color: white;
        }

        .logout-btn {
            background: rgba(239, 68, 68, 0.2);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .logout-btn:hover {
            background: rgba(239, 68, 68, 0.3);
        }

        /* Container */
        .container {
            max-width: 1600px;
            margin: 0 auto;
            padding: 32px;
        }

        /* Title Section */
        .title-section {
            text-align: center;
            margin-bottom: 32px;
        }

        .title {
            font-size: 2.5rem;
            font-weight: 900;
            background: linear-gradient(135deg, #f59e0b, #ef4444, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 8px;
        }

        .subtitle {
            color: rgba(255, 255, 255, 0.6);
            font-size: 1rem;
        }

        .team-selector-panel {
            position: absolute;
            top: 96px;
            right: 32px;
            z-index: 50;
            display: flex;
            gap: 12px;
            align-items: center;
            background: rgba(15, 23, 42, 0.85);
            border: 1px solid rgba(245, 158, 11, 0.35);
            padding: 12px 14px;
            border-radius: 12px;
            box-shadow: 0 0 18px rgba(0, 0, 0, 0.35);
        }

        .team-selector-panel label {
            font-size: 0.8rem;
            color: #fcd34d;
            margin-bottom: 4px;
            display: block;
        }

        .team-selector-panel select,
        .team-selector-panel button {
            background: rgba(30, 41, 59, 0.95);
            border: 1px solid rgba(248, 113, 113, 0.35);
            color: #fff;
            border-radius: 8px;
            padding: 6px 10px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .team-selector-panel select {
            min-width: 175px;
        }

        .team-selector-panel button {
            cursor: pointer;
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            border-color: transparent;
        }

        .team-selector-panel button:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        /* Battle Arena */
        .arena-section {
            background: rgba(30, 41, 59, 0.6);
            border-radius: 20px;
            padding: 32px;
            margin-bottom: 32px;
            border: 2px solid rgba(245, 158, 11, 0.3);
            box-shadow: 0 0 40px rgba(245, 158, 11, 0.2);
        }

        .arena-title {
            text-align: center;
            font-size: 1.5rem;
            font-weight: 800;
            color: #fbbf24;
            margin-bottom: 24px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .battle-grid {
            display: grid;
            grid-template-columns: repeat(10, 1fr);
            gap: 8px;
            max-width: 1000px;
            margin: 0 auto;
            background: rgba(15, 23, 42, 0.8);
            padding: 16px;
            border-radius: 16px;
            border: 3px solid rgba(245, 158, 11, 0.4);
        }

        .grid-cell {
            aspect-ratio: 1;
            background: linear-gradient(135deg, #1e293b, #334155);
            border: 2px solid rgba(148, 163, 184, 0.3);
            border-radius: 10px;
            position: relative;
            cursor: pointer;
            transition: all 0.2s;
        }

        /* Highlight deployable zone (controlled by JS via deploy-zone class) */
        .grid-cell.deploy-zone {
            box-shadow: 0 0 14px rgba(245, 158, 11, 0.7);
            border-color: rgba(245, 158, 11, 0.7);
            animation: goldGlow 2s ease-in-out infinite;
        }

        .base-cell {
            position: relative;
            background: linear-gradient(135deg, rgba(245,158,11,0.3), rgba(245,158,11,0.1));
            border-color: rgba(245, 158, 11, 0.9);
        }

        .base-marker {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.4rem;
            font-weight: 900;
            color: rgba(255, 255, 255, 0.9);
            text-shadow: 0 0 10px rgba(245, 158, 11, 0.9);
            pointer-events: none;
        }

        .enemy-base-cell {
            position: relative;
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.25), rgba(220, 38, 38, 0.1));
            border-color: rgba(239, 68, 68, 0.9);
        }

        .enemy-base-marker {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.4rem;
            font-weight: 900;
            color: rgba(255, 255, 255, 0.95);
            text-shadow: 0 0 10px rgba(239, 68, 68, 0.9);
            pointer-events: none;
        }

        @keyframes goldGlow {
            0%, 100% { box-shadow: 0 0 10px rgba(245, 158, 11, 0.6); }
            50% { box-shadow: 0 0 22px rgba(245, 158, 11, 0.85); }
        }

        .grid-cell:hover {
            background: linear-gradient(135deg, #334155, #475569);
            border-color: #fbbf24;
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(251, 191, 36, 0.4);
        }

        .grid-cell.occupied {
            background: linear-gradient(135deg, #065f46, #047857);
            border-color: #10b981;
        }

        .grid-cell.drop-target {
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            border-color: #c084fc;
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.05); opacity: 0.8; }
        }

        /* Character in grid */
        .deployed-character {
            position: absolute;
            inset: 4px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            animation: deployAnim 0.5s ease-out, idleBounce 2s ease-in-out infinite 0.5s;
        }

        @keyframes deployAnim {
            from {
                transform: scale(0) rotate(180deg);
                opacity: 0;
            }
            to {
                transform: scale(1) rotate(0deg);
                opacity: 1;
            }
        }

        @keyframes idleBounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }

        .deployed-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 900;
            font-size: 0.7rem;
            color: white;
            border: 2px solid white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
            overflow: hidden;
        }

        .deployed-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .deployed-name {
            font-size: 0.55rem;
            font-weight: 700;
            color: white;
            margin-top: 2px;
            text-align: center;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.6);
        }

        /* Character Deck */
        .deck-section {
            background: rgba(30, 41, 59, 0.6);
            border-radius: 20px;
            padding: 32px;
            border: 2px solid rgba(59, 130, 246, 0.3);
        }

        .deck-title {
            text-align: center;
            font-size: 1.3rem;
            font-weight: 800;
            color: #60a5fa;
            margin-bottom: 24px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .character-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .character-card {
            background: linear-gradient(145deg, #1e293b, #334155);
            border-radius: 16px;
            padding: 16px;
            border: 2px solid rgba(59, 130, 246, 0.3);
            cursor: grab;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .character-card:hover {
            transform: translateY(-8px);
            border-color: #60a5fa;
            box-shadow: 0 12px 32px rgba(59, 130, 246, 0.4);
        }

        .character-card.dragging {
            opacity: 0.5;
            cursor: grabbing;
        }

        .character-card.deployed {
            opacity: 0.3;
            pointer-events: none;
        }

        .character-image {
            width: 100%;
            height: 140px;
            border-radius: 12px;
            background: linear-gradient(135deg, #475569, #64748b);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
            overflow: hidden;
            position: relative;
        }

        .character-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .character-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, #f59e0b, #ef4444);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            font-weight: 900;
            color: white;
            border: 4px solid white;
        }

        .role-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: rgba(59, 130, 246, 0.9);
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .character-name {
            font-size: 1.1rem;
            font-weight: 800;
            color: white;
            margin-bottom: 8px;
            text-align: center;
        }

        .character-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 6px;
        }

        .stat-item {
            background: rgba(15, 23, 42, 0.6);
            border: 1px solid rgba(148, 163, 184, 0.3);
            border-radius: 8px;
            padding: 6px;
            text-align: center;
        }

        .stat-label {
            font-size: 0.65rem;
            color: rgba(255, 255, 255, 0.6);
            font-weight: 600;
            text-transform: uppercase;
        }

        .stat-value {
            font-size: 0.95rem;
            font-weight: 900;
            color: #fbbf24;
        }

        .empty-deck {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255, 255, 255, 0.4);
        }

        .empty-deck-icon {
            font-size: 4rem;
            margin-bottom: 16px;
            opacity: 0.3;
        }

        /* Admin badge */
        .admin-badge {
            background: rgba(239, 68, 68, 0.2);
            border: 2px solid rgba(239, 68, 68, 0.4);
            color: #fca5a5;
            padding: 8px 16px;
            border-radius: 12px;
            font-weight: 800;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-block;
            margin-bottom: 16px;
        }

        /* Instructions */
        .instructions {
            background: rgba(59, 130, 246, 0.1);
            border: 2px solid rgba(59, 130, 246, 0.3);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 24px;
            color: #93c5fd;
            font-size: 0.9rem;
            text-align: center;
        }

        /* Admin Character Panels */
        .battleground-wrapper {
            display: flex;
            gap: 16px;
            align-items: flex-start;
            margin-bottom: 32px;
        }

        .character-panel {
            flex: 0 0 300px;
            background: rgba(30, 41, 59, 0.7);
            border: 2px solid rgba(148, 163, 184, 0.3);
            border-radius: 16px;
            padding: 16px;
            max-height: 700px;
            overflow-y: auto;
            height: fit-content;
            position: sticky;
            top: 120px;
        }

        .character-panel.team-a {
            border-color: rgba(34, 197, 94, 0.5);
            order: 1;
        }

        .character-panel.team-b {
            border-color: rgba(239, 68, 68, 0.5);
            order: 3;
        }

        .panel-title {
            font-size: 1.1rem;
            font-weight: 800;
            margin-bottom: 16px;
            text-align: center;
            padding-bottom: 12px;
            border-bottom: 2px solid rgba(148, 163, 184, 0.2);
        }

        .panel-title.team-a {
            color: #22c55e;
            border-bottom-color: rgba(34, 197, 94, 0.4);
        }

        .panel-title.team-b {
            color: #ef4444;
            border-bottom-color: rgba(239, 68, 68, 0.4);
        }

        .character-entry {
            background: rgba(15, 23, 42, 0.8);
            border: 1px solid rgba(148, 163, 184, 0.2);
            border-radius: 10px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .character-entry-name {
            font-size: 0.9rem;
            font-weight: 700;
            color: white;
            margin-bottom: 4px;
        }

        .character-entry-role {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.5);
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .hp-bar-container {
            background: rgba(0, 0, 0, 0.4);
            border-radius: 8px;
            height: 20px;
            overflow: hidden;
            border: 1px solid rgba(148, 163, 184, 0.2);
            margin-bottom: 4px;
        }

        .hp-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #ef4444, #fbbf24);
            transition: width 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            font-weight: 700;
            color: white;
        }

        .hp-text {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.6);
            text-align: right;
        }

        .character-stats-mini {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 4px;
            font-size: 0.7rem;
            margin-top: 6px;
        }

        .stat-mini {
            background: rgba(30, 41, 59, 0.6);
            padding: 3px 6px;
            border-radius: 4px;
            text-align: center;
        }

        .stat-mini-label {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.65rem;
        }

        .stat-mini-value {
            color: #fbbf24;
            font-weight: 700;
        }

        .arena-wrapper {
            flex: 1;
            order: 2;
            min-width: 0;
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="site-header-inner">
            <div class="brand">⚔️ Auction Grid Battle</div>
            <nav class="nav">
                <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
                <a href="{{ route('auction') }}" class="nav-link">Auction</a>
                <a href="{{ route('deck') }}" class="nav-link">Deck</a>
                <a href="{{ route('battleground') }}" class="nav-link active">Battle Ground</a>
                <a href="#" class="nav-link">Leaderboard</a>
            </nav>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-btn" type="submit">Logout</button>
            </form>
        </div>
    </header>

    <div class="container">
        <div class="title-section">
            <h1 class="title">⚔️ BATTLE GROUND ⚔️</h1>
            <p class="subtitle">
                @if($isAdmin)
                    Admin View • Watch the battle unfold
                @else
                    {{ $teamInfo->name ?? 'Your Team' }} • Deploy your characters
                @endif
            </p>
        </div>

        @if($isAdmin)
            <div style="text-align: center;">
                <span class="admin-badge">👑 Admin Mode - Read Only</span>
            </div>

            <div class="team-selector-panel">
                <div style="display:flex; flex-direction: column;">
                    <label for="team_a">Team A (Top)</label>
                    <select id="team_a" name="team_a">
                        <option value="">Select Team A</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="display:flex; flex-direction: column;">
                    <label for="team_b">Team B (Bottom)</label>
                    <select id="team_b" name="team_b">
                        <option value="">Select Team B</option>
                        @foreach($teams as $team)
                            <option value="{{ $team->id }}">{{ $team->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button id="assignTeamsBtn" disabled>Assign Teams</button>
            </div>
        @endif

        @if(!$isAdmin && $canViewDeck)
            <div class="instructions">
                💡 <strong>How to Deploy:</strong> Click a character card below, then click a grid tile to deploy. Or drag and drop!
            </div>
        @elseif(!$isAdmin)
            <div class="instructions">
                ⚠️ Your team is not part of the current battle (Team A/Team B). Character deck is hidden.
            </div>
        @endif

        <!-- Battle Arena with Character Panels (Admin Only) -->
        @if($isAdmin)
            <div class="battleground-wrapper">
                <div class="character-panel team-a" id="teamAPanel">
                    <div class="panel-title team-a">🟢 Team A</div>
                    <div id="teamACharacters"></div>
                </div>
        @endif

        <!-- Battle Arena -->
        <div class="arena-section @if($isAdmin) arena-wrapper @endif">
            <h2 class="arena-title">🎯 Battle Arena (10x10)</h2>
            <div class="battle-grid" id="battle-grid">
                @for($y = 0; $y < 10; $y++)
                    @for($x = 0; $x < 10; $x++)
                        @php
                            $isBase = ($x === 4 && $y === 9);
                            $isEnemyBase = ($x === 5 && $y === 0);
                        @endphp

                        <div class="grid-cell {{ $isBase ? 'base-cell' : '' }} {{ $isEnemyBase ? 'enemy-base-cell' : '' }}" data-x="{{ $x }}" data-y="{{ $y }}">
                            @if($isBase)
                                <div class="base-marker">B</div>
                            @elseif($isEnemyBase)
                                <div class="enemy-base-marker">B</div>
                            @endif
                        </div>
                    @endfor
                @endfor
            </div>
            
        @if($isAdmin)
            </div>
            <div class="character-panel team-b" id="teamBPanel">
                <div class="panel-title team-b">🔴 Team B</div>
                <div id="teamBCharacters"></div>
            </div>
        @endif
        </div>


        <!-- Character Deck -->
        @if(!$isAdmin)
            <div class="deck-section">
                <h2 class="deck-title">🃏 Your Character Deck</h2>

                <div class="user-panel-buttons" style="margin-bottom: 12px;">
                    <button id="saveDeploymentBtn" type="button" class="btn-secondary">💾 Save Deployment</button>
                </div>

                @if($canViewDeck)
                    @if($characters->isEmpty())
                        <div class="empty-deck">
                            <div class="empty-deck-icon">🎴</div>
                            <p>No characters in your deck. Visit the auction to purchase characters!</p>
                        </div>
                    @else
                        <div class="character-grid" id="character-deck">
                            @foreach($characters as $character)
                                <div class="character-card" 
                                     draggable="true"
                             data-character-id="{{ $character->id }}"
                             data-character-name="{{ $character->name }}"
                             data-character-role="{{ $character->role_name ?? 'Unknown' }}"
                             data-character-speed="{{ $character->speed ?? 1 }}"
                             data-character-image="{{ $character->image }}">
                            
                            <div class="character-image">
                                @if($character->image)
                                    @php
                                        $imageSrc = $character->image;
                                        if (!str_starts_with($imageSrc, 'http') && !str_starts_with($imageSrc, '/')) {
                                            $imageSrc = '/' . $imageSrc;
                                        }
                                    @endphp
                                    <img src="{{ $imageSrc }}" alt="{{ $character->name }}">
                                @else
                                    <div class="character-avatar">
                                        {{ strtoupper(substr($character->name, 0, 2)) }}
                                    </div>
                                @endif
                                <span class="role-badge">{{ $character->role_name ?? 'Unknown' }}</span>
                            </div>

                            <h3 class="character-name">{{ $character->name }}</h3>

                            <div class="character-stats">
                                <div class="stat-item">
                                    <div class="stat-label">❤️ HP</div>
                                    <div class="stat-value">{{ $character->hp }}</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-label">⚔️ DMG</div>
                                    <div class="stat-value">{{ $character->damage }}</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-label">💨 SPD</div>
                                    <div class="stat-value">{{ $character->speed }}</div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <div class="empty-deck">
                <div class="empty-deck-icon">🚫</div>
                <p>Not assigned to the current battle. You cannot view or deploy characters until your team is Team A or Team B.</p>
            </div>
        @endif
        </div>
        @endif
    </div>

    <script>
        const isAdmin = {{ $isAdmin ? 'true' : 'false' }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const deployUrl = "{{ route('battleground.deploy') }}";
        const stateUrl = "{{ route('battleground.state') }}";

        let currentBattle = @json($currentBattle ?? null);
        const userTeamId = @json($user->team_id ?? null);

        const teamASelect = document.getElementById('team_a');
        const teamBSelect = document.getElementById('team_b');
        const assignTeamsBtn = document.getElementById('assignTeamsBtn');

        function isDeployableCell(x, y) {
            if (!currentBattle || !userTeamId) {
                return false;
            }

            const teamA = Number(currentBattle.team_a_id);
            const teamB = Number(currentBattle.team_b_id);
            const userTeam = Number(userTeamId);

            if (userTeam !== teamA && userTeam !== teamB) {
                return false;
            }

            if (userTeam === teamA) {
                return y >= 0 && y <= 2;
            }

            if (userTeam === teamB) {
                return y >= 7 && y <= 9;
            }

            return false;
        }

        function refreshDeployZones() {
            document.querySelectorAll('.grid-cell').forEach(cell => {
                const x = Number(cell.getAttribute('data-x'));
                const y = Number(cell.getAttribute('data-y'));

                if (isDeployableCell(x, y)) {
                    cell.classList.add('deploy-zone');
                    cell.style.boxShadow = '0 0 14px rgba(245, 158, 11, 0.7)';
                    cell.style.borderColor = 'rgba(245, 158, 11, 0.7)';
                    cell.style.animation = 'goldGlow 2s ease-in-out infinite';
                } else {
                    cell.classList.remove('deploy-zone');
                    cell.style.boxShadow = '';
                    cell.style.borderColor = '';
                    cell.style.animation = '';
                }
            });
        }

        const gridCells = document.querySelectorAll('.grid-cell');

        refreshDeployZones();

        function updateAssignButton() {
            if (!teamASelect || !teamBSelect || !assignTeamsBtn) return;
            const a = teamASelect.value;
            const b = teamBSelect.value;
            assignTeamsBtn.disabled = !(a && b);
        }

        function ensureDistinctTeams(changedSelect) {
            if (!teamASelect || !teamBSelect) return;
            if (teamASelect.value && teamASelect.value === teamBSelect.value) {
                alert('Team A and Team B cannot be the same');
                changedSelect.value = '';
            }
        }

        if (teamASelect && teamBSelect) {
            teamASelect.addEventListener('change', function() {
                ensureDistinctTeams(this);
                updateAssignButton();
            });

            teamBSelect.addEventListener('change', function() {
                ensureDistinctTeams(this);
                updateAssignButton();
            });
        }

        if (assignTeamsBtn) {
            assignTeamsBtn.addEventListener('click', async function() {
                const a = teamASelect?.value;
                const b = teamBSelect?.value;

                if (!a || !b) {
                    alert('Please select both teams');
                    return;
                }

                if (a === b) {
                    alert('Team A and Team B cannot be the same');
                    return;
                }

                try {
                    const response = await fetch('{{ route('battleground.assignTeams') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            team_a_id: a,
                            team_b_id: b,
                        }),
                    });

                    const data = await response.json();
                    if (response.ok) {
                        alert(data.message || 'Teams assigned successfully');
                        if (a && b) {
                            currentBattle = { team_a_id: a, team_b_id: b }; 
                            refreshDeployZones();
                        }
                    } else {
                        alert(data.message || 'Failed to assign teams');
                    }
                } catch (error) {
                    console.error('Assign teams error', error);
                    alert('Failed to assign teams. Check console for details.');
                }
            });
        }

        let selectedCharacter = null;
        let stagedDeployments = []; // track staged deployments until save
        let deployedCharacters = {}; // track deployed positions (server + staged)

        // Grid cells
        const characterCards = document.querySelectorAll('.character-card');

        // Character card click selection (for click-to-deploy)
        characterCards.forEach(card => {
            card.addEventListener('click', () => {
                if (isAdmin) return;

                // Deselect all
                characterCards.forEach(c => c.style.border = '2px solid rgba(59, 130, 246, 0.3)');
                
                // Select this one
                card.style.border = '3px solid #60a5fa';
                selectedCharacter = {
                    id: card.dataset.characterId,
                    name: card.dataset.characterName,
                    role: card.dataset.characterRole,
                    image: card.dataset.characterImage,
                    speed: parseInt(card.dataset.characterSpeed, 10) || 1,
                    cardElement: card
                };
            });

            // Drag start
            card.addEventListener('dragstart', (e) => {
                if (isAdmin) return;
                // Some browsers require dataTransfer to be set for drag/drop to work
                e.dataTransfer.effectAllowed = 'move';
                e.dataTransfer.setData('text/plain', card.dataset.characterId);

                card.classList.add('dragging');
                selectedCharacter = {
                    id: card.dataset.characterId,
                    name: card.dataset.characterName,
                    role: card.dataset.characterRole,
                    image: card.dataset.characterImage,
                    speed: parseInt(card.dataset.characterSpeed, 10) || 1,
                    cardElement: card
                };
            });

            card.addEventListener('dragend', () => {
                card.classList.remove('dragging');
            });
        });

        // Grid cell click deployment
        gridCells.forEach(cell => {
            cell.addEventListener('click', async () => {
                if (isAdmin || !selectedCharacter) return;

                const x = parseInt(cell.dataset.x);
                const y = parseInt(cell.dataset.y);

                // Only allow deploying into permitted zones for your team
                if (!isDeployableCell(x, y)) {
                    alert('Invalid deployment zone for your team.');
                    return;
                }

                // Check if cell is occupied
                if (deployedCharacters[`${x},${y}`]) {
                    return;
                }

                await stageCharacterDeployment(selectedCharacter, x, y);
            });

            // Drag and drop
            cell.addEventListener('dragover', (e) => {
                if (isAdmin) return;
                e.preventDefault();
                cell.classList.add('drop-target');
            });

            cell.addEventListener('dragleave', () => {
                cell.classList.remove('drop-target');
            });

            cell.addEventListener('drop', async (e) => {
                if (isAdmin) return;
                e.preventDefault();
                cell.classList.remove('drop-target');

                const x = parseInt(cell.dataset.x);
                const y = parseInt(cell.dataset.y);

                // Only allow deploying into permitted zones for your team
                if (!isDeployableCell(x, y)) {
                    alert('Invalid deployment zone for your team.');
                    return;
                }

                if (selectedCharacter && !deployedCharacters[`${x},${y}`]) {
                    await stageCharacterDeployment(selectedCharacter, x, y);
                }
            });
        });

        async function stageCharacterDeployment(character, x, y) {
            // stage the deployment locally; it is not persisted until saved.
            console.log('📝 Staging character deployment (local):', character.name, 'at', x, y);

            deployedCharacters[`${x},${y}`] = character.id;
            placeCharacterOnGrid(character, x, y);
            stagedDeployments.push({
                character_id: character.id,
                grid_x: x,
                grid_y: y,
            });

            // mark as deployed in the UI and clear selection
            character.cardElement.classList.add('deployed');
            characterCards.forEach(c => c.style.border = '2px solid rgba(59, 130, 246, 0.3)');
            selectedCharacter = null;
        }

        async function saveStagedDeployments() {
            if (!stagedDeployments.length) {
                alert('No deployments staged to save.');
                return;
            }

            const payload = stagedDeployments.slice(); // copy
            let successCount = 0;
            let failure = null;

            for (const deployment of payload) {
                try {
                    const response = await fetch(deployUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({
                            character_id: deployment.character_id,
                            grid_x: deployment.grid_x,
                            grid_y: deployment.grid_y,
                        }),
                    });

                    const data = await response.json();
                    if (response.ok) {
                        successCount += 1;
                    } else {
                        failure = data.message || 'Failed to save deployment';
                        break;
                    }
                } catch (err) {
                    failure = err.message || 'Network error saving deployment';
                    break;
                }
            }

            if (failure) {
                alert(`⚠️ Deployment save failed: ${failure}`);
                return;
            }

            stagedDeployments = [];
            localStorage.removeItem('battleground_saved_deployment');

            await loadBattlegroundState();
            alert(`✅ ${successCount} deployment(s) saved to server.`);
        }

        function placeCharacterOnGrid(character, x, y) {
            const cell = document.querySelector(`[data-x="${x}"][data-y="${y}"]`);
            if (!cell) return;

            // Clear any existing character in this cell so the latest
            // server state always wins.
            cell.classList.add('occupied');
            const existing = cell.querySelector('.deployed-character');
            if (existing) {
                cell.removeChild(existing);
            }
            deployedCharacters[`${x},${y}`] = character.id;

            // Normalize image path
            let imageSrc = character.image;
            if (imageSrc && imageSrc !== 'null' && imageSrc !== '') {
                if (!imageSrc.startsWith('http') && !imageSrc.startsWith('/')) {
                    imageSrc = '/' + imageSrc;
                }
            } else {
                imageSrc = null;
            }

            const avatar = document.createElement('div');
            avatar.className = 'deployed-character';
            avatar.innerHTML = `
                <div class="deployed-avatar">
                    ${imageSrc 
                        ? `<img src="${imageSrc}" alt="${character.name}">` 
                        : character.name.substring(0, 2).toUpperCase()
                    }
                </div>
                <div class="deployed-name">${character.name}</div>
            `;
            cell.appendChild(avatar);
        }

        // Load deployments so every player sees the same board.
        // Can be called on load and on a polling interval as a fallback
        // when websockets are not available.
        async function loadBattlegroundState() {
            try {
                const response = await fetch(stateUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    cache: 'no-store',
                });

                if (!response.ok) return;

                const data = await response.json();
                if (!data.deployments) return;

                // Clear current board state before re-applying
                Object.keys(deployedCharacters).forEach(key => {
                    const [x, y] = key.split(',').map(Number);
                    const cell = document.querySelector(`[data-x="${x}"][data-y="${y}"]`);
                    if (cell) {
                        cell.classList.remove('occupied');
                        const avatar = cell.querySelector('.deployed-character');
                        if (avatar) cell.removeChild(avatar);
                    }
                });
                deployedCharacters = {};

                data.deployments.forEach(item => {
                    const c = item.character || {};
                    const character = {
                        id: c.id || item.character_id,
                        name: c.name || 'Unknown',
                        role: c.role_name || 'Unknown',
                        image: c.image || null,
                        speed: c.speed || 1,
                    };
                    placeCharacterOnGrid(character, item.grid_x, item.grid_y);
                });
            } catch (e) {
                console.warn('⚠️ Failed to load initial battleground state', e);
            }
        }

        // Live updates toggle: set true to enable WebSocket + polling updates.
        const battlegroundLiveUpdatesEnabled = true;

        // WebSocket listener + polling fallback
        window.addEventListener('load', () => {
            // First, hydrate from current server state
            loadBattlegroundState();

            if (battlegroundLiveUpdatesEnabled) {
                // Then subscribe for live updates
                if (window.Echo) {
                    try {
                        console.log('🔌 Connecting to battleground channel...');
                        window.Echo.channel('battleground')
                            .listen('.character.deployed', (event) => {
                                console.log('✅ Character deployed event received:', event);
                                const character = {
                                    id: event.character_id,
                                    name: event.character_name,
                                    role: event.role,
                                    image: event.image,
                                    speed: event.speed || 1,
                                };
                                placeCharacterOnGrid(character, event.grid_x, event.grid_y);
                            });
                        console.log('✅ Battleground channel connected');
                    } catch (e) {
                        console.error('❌ WebSocket connection failed:', e);
                    }
                } else {
                    console.warn('⚠️ window.Echo is not available');
                }

                // Fallback: poll the battleground state every 2 seconds so that
                // other players' deployments appear even if websockets fail.
                setInterval(loadBattlegroundState, 2000);
            } else {
                console.log('ℹ️ Live battleground updates are disabled. Page remains static after initial load.');
            }
        });

        // Movement timer: update character positions every second
        async function updateCharacterPositions() {
            try {
                const response = await fetch('{{ route('battleground.updatePositions') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) return;

                const data = await response.json();
                if (!data.updated_positions || data.updated_positions.length === 0) return;

                // Log attack events
                if (data.attack_log && data.attack_log.length > 0) {
                    console.log('⚔️ Attacks:', data.attack_log);
                    data.attack_log.forEach(a => {
                        console.log(`  ${a.attacker_id} attacked ${a.target_id} for ${a.damage} damage (target HP: ${a.target_hp})`);
                    });
                }

                // Clear current board state
                Object.keys(deployedCharacters).forEach(key => {
                    const [x, y] = key.split(',').map(Number);
                    const cell = document.querySelector(`[data-x="${x}"][data-y="${y}"]`);
                    if (cell) {
                        cell.classList.remove('occupied');
                        const avatar = cell.querySelector('.deployed-character');
                        if (avatar) cell.removeChild(avatar);
                    }
                });
                deployedCharacters = {};

                // Redraw all positions from server
                data.updated_positions.forEach(pos => {
                    // Skip dead characters
                    if (pos.status === 'dead') return;

                    deployedCharacters[`${pos.grid_x},${pos.grid_y}`] = pos.character_id;
                    
                    const cell = document.querySelector(`[data-x="${pos.grid_x}"][data-y="${pos.grid_y}"]`);
                    if (!cell) return;

                    cell.classList.add('occupied');
                    
                    // Create placeholder character object for rendering
                    const character = {
                        id: pos.character_id,
                        name: 'Char ' + pos.character_id,
                    };

                    let imageSrc = null;
                    const avatar = document.createElement('div');
                    avatar.className = 'deployed-character';
                    avatar.innerHTML = `
                        <div class="deployed-avatar">
                            C${pos.character_id}
                        </div>
                        <div class="deployed-name">Moving</div>
                    `;
                    cell.appendChild(avatar);
                });

                console.log('✅ Positions updated:', data.moved, 'moved,', data.attacked, 'attacked');
            } catch (e) {
                console.warn('⚠️ Failed to update character positions', e);
            }
        }

        // Start movement tick timer after page loads
        window.addEventListener('load', () => {
            setTimeout(() => {
                setInterval(updateCharacterPositions, 1000);
            }, 2000);
        });

        // Admin character panel display
        async function updateCharacterPanels() {
            if (!isAdmin) return;

            try {
                const response = await fetch(stateUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    cache: 'no-store',
                });

                if (!response.ok) return;
                const data = await response.json();
                if (!data.deployments) return;

                const teamADeps = data.deployments.filter(d => Number(currentBattle?.team_a_id) === Number(d.team_id));
                const teamBDeps = data.deployments.filter(d => Number(currentBattle?.team_b_id) === Number(d.team_id));

                const teamAHtml = teamADeps.map(dep => {
                    const c = dep.character || {};
                    const maxHp = c.hp || 100;
                    const currentHp = dep.current_hp || maxHp;
                    const hpPercent = Math.max(0, (currentHp / maxHp) * 100);
                    const isDead = dep.status === 'dead';
                    
                    return `
                        <div class="character-entry" style="${isDead ? 'opacity: 0.5;' : ''}">
                            <div class="character-entry-name">${c.name || 'Unknown'}</div>
                            <div class="character-entry-role">${c.role_name || 'N/A'}</div>
                            <div class="hp-bar-container">
                                <div class="hp-bar-fill" style="width: ${hpPercent}%; background: ${isDead ? '#6b7280' : 'linear-gradient(90deg, #ef4444, #fbbf24)'};">
                                    ${Math.round(hpPercent)}%
                                </div>
                            </div>
                            <div class="hp-text">${currentHp}/${maxHp} HP ${isDead ? '💀' : ''}</div>
                            <div class="character-stats-mini">
                                <div class="stat-mini">
                                    <div class="stat-mini-label">DMG</div>
                                    <div class="stat-mini-value">${c.damage || 0}</div>
                                </div>
                                <div class="stat-mini">
                                    <div class="stat-mini-label">RNG</div>
                                    <div class="stat-mini-value">${c.range || 1}</div>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

                const teamBHtml = teamBDeps.map(dep => {
                    const c = dep.character || {};
                    const maxHp = c.hp || 100;
                    const currentHp = dep.current_hp || maxHp;
                    const hpPercent = Math.max(0, (currentHp / maxHp) * 100);
                    const isDead = dep.status === 'dead';
                    
                    return `
                        <div class="character-entry" style="${isDead ? 'opacity: 0.5;' : ''}">
                            <div class="character-entry-name">${c.name || 'Unknown'}</div>
                            <div class="character-entry-role">${c.role_name || 'N/A'}</div>
                            <div class="hp-bar-container">
                                <div class="hp-bar-fill" style="width: ${hpPercent}%; background: ${isDead ? '#6b7280' : 'linear-gradient(90deg, #ef4444, #fbbf24)'};">
                                    ${Math.round(hpPercent)}%
                                </div>
                            </div>
                            <div class="hp-text">${currentHp}/${maxHp} HP ${isDead ? '💀' : ''}</div>
                            <div class="character-stats-mini">
                                <div class="stat-mini">
                                    <div class="stat-mini-label">DMG</div>
                                    <div class="stat-mini-value">${c.damage || 0}</div>
                                </div>
                                <div class="stat-mini">
                                    <div class="stat-mini-label">RNG</div>
                                    <div class="stat-mini-value">${c.range || 1}</div>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');

                const teamAPanel = document.getElementById('teamACharacters');
                const teamBPanel = document.getElementById('teamBCharacters');

                if (teamAPanel) teamAPanel.innerHTML = teamAHtml || '<p style="text-align: center; color: rgba(255,255,255,0.4); padding: 20px;">No characters deployed</p>';
                if (teamBPanel) teamBPanel.innerHTML = teamBHtml || '<p style="text-align: center; color: rgba(255,255,255,0.4); padding: 20px;">No characters deployed</p>';
            } catch (e) {
                console.warn('⚠️ Failed to update character panels', e);
            }
        }

        // Update panels on initial load and every 2 seconds
        window.addEventListener('load', () => {
            if (isAdmin) {
                updateCharacterPanels();
                setInterval(updateCharacterPanels, 2000);
            }
        });

        // Save deployment button
        document.addEventListener('DOMContentLoaded', () => {
            const saveBtn = document.getElementById('saveDeploymentBtn');
            if (!saveBtn) return;

            saveBtn.addEventListener('click', async () => {
                await saveStagedDeployments();
            });
        });
    </script>

    @vite('resources/js/app.js')
</body>
</html>

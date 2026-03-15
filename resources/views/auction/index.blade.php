<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Auction</title>
    <style>
        :root {
            --bg: #EEF2FF;
            --card: #ffffff;
            --primary: #7C3AED;
            --text: #1F2937;
            --muted: #6B7280;
            --border: #CBD5E1;
            --danger: #EF4444;
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
            margin: 24px auto;
            padding: 0 16px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
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

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 16px;
            box-shadow: 0 8px 18px rgba(30, 41, 59, 0.08);
        }

        .label {
            margin: 0 0 8px;
            color: var(--muted);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .value {
            margin: 0;
            font-size: 1.35rem;
            font-weight: 800;
            color: var(--primary);
        }

        .status-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 14px;
        }

        .status-pill {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 0.03em;
            text-transform: uppercase;
        }

        .status-active {
            color: #166534;
            background: #dcfce7;
            border: 1px solid #86efac;
        }

        .status-inactive {
            color: #7f1d1d;
            background: #fee2e2;
            border: 1px solid #fca5a5;
        }

        .status-bid {
            color: #1d4ed8;
            background: #dbeafe;
            border: 1px solid #93c5fd;
        }

        .start-btn {
            border: none;
            background: var(--primary);
            color: #fff;
            border-radius: 10px;
            padding: 10px 14px;
            font-weight: 700;
            cursor: pointer;
        }

        .start-btn[disabled] {
            opacity: 0.55;
            cursor: not-allowed;
        }

        .bottom-actions {
            margin-top: 16px;
            display: flex;
            justify-content: flex-end;
        }

        .bid-btn {
            border: none;
            background: #2563eb;
            color: #fff;
            border-radius: 10px;
            padding: 10px 16px;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
        }

        .hidden {
            display: none;
        }

        /* character card styles copied from the user dashboard view */
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

        /* ── Auction Dashboard ─────────────────────────────── */
        .dash-wrap {
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 50%, #6366f1 100%);
            border-radius: 24px;
            padding: 3px;
            box-shadow: 0 20px 60px rgba(124,58,237,.25), 0 4px 16px rgba(0,0,0,.06);
            margin-top: 24px;
        }

        .dash-inner {
            background: #fafaf8;
            border-radius: 22px;
            padding: 24px 28px;
        }

        .dash-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 20px;
            padding-bottom: 16px;
            border-bottom: 2px dashed #e9d5ff;
        }

        .dash-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 900;
            color: #7c3aed;
            letter-spacing: -0.01em;
        }

        .live-badge {
            display: flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            color: #fff;
            border-radius: 999px;
            padding: 5px 14px;
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        .live-badge::before {
            content: '';
            width: 7px;
            height: 7px;
            background: #4ade80;
            border-radius: 50%;
            animation: pulse-dot 1.4s ease-in-out infinite;
        }

        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: .5; transform: scale(1.35); }
        }

        /* Hero card layout */
        .hero-card {
            display: grid;
            grid-template-columns: 240px 1fr;
            gap: 28px;
            align-items: start;
            background: linear-gradient(135deg, #f5f3ff 0%, #ede9fe 40%, #faf5ff 100%);
            border-radius: 18px;
            padding: 22px;
            transition: box-shadow .25s;
        }

        .hero-card:hover {
            box-shadow: 0 12px 36px rgba(124,58,237,.14);
        }

        @media (max-width: 680px) {
            .hero-card { grid-template-columns: 1fr; }
        }

        .hero-img-wrap {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            background: #ede9fe;
            box-shadow: 0 8px 24px rgba(124,58,237,.18);
            aspect-ratio: 3/4;
        }

        .hero-img-wrap img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .hero-img-role {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background: rgba(124,58,237,.9);
            backdrop-filter: blur(6px);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .06em;
            padding: 4px 10px;
            border-radius: 999px;
        }

        /* Right panel */
        .hero-info { display: flex; flex-direction: column; gap: 14px; }

        .hero-name {
            margin: 0;
            font-size: 1.85rem;
            font-weight: 900;
            color: #4c1d95;
            line-height: 1.1;
            letter-spacing: -0.02em;
        }

        .hero-desc {
            margin: 0;
            font-size: 0.875rem;
            color: #6b7280;
            line-height: 1.55;
        }

        /* Stats grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .stat-chip {
            background: #fff;
            border: 1px solid #e9d5ff;
            border-radius: 12px;
            padding: 10px 12px;
            display: flex;
            flex-direction: column;
            gap: 2px;
            box-shadow: 0 2px 8px rgba(0,0,0,.04);
            transition: transform .2s;
        }

        .stat-chip:hover { transform: translateY(-2px); }

        .stat-chip-label {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #9ca3af;
        }

        .stat-chip-value {
            font-size: 1.1rem;
            font-weight: 900;
            color: #7c3aed;
        }

        /* Ability box */
        .ability-box {
            background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);
            border-radius: 14px;
            padding: 14px 16px;
            color: #fff;
        }

        .ability-label {
            font-size: 0.65rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .08em;
            opacity: .75;
            margin: 0 0 4px;
        }

        .ability-text {
            margin: 0;
            font-size: 0.9rem;
            font-weight: 700;
            line-height: 1.4;
        }

        /* Price tag */
        .price-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fef9c3;
            border: 2px solid #fde047;
            border-radius: 12px;
            padding: 8px 14px;
            font-size: 1rem;
            font-weight: 900;
            color: #854d0e;
            align-self: flex-start;
        }

        /* Slider controls */
        .slider-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 16px;
            margin-top: 22px;
            padding-top: 20px;
            border-top: 2px dashed #e9d5ff;
        }

        .slider-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: none;
            border-radius: 14px;
            padding: 12px 22px;
            font-size: 0.875rem;
            font-weight: 800;
            cursor: pointer;
            transition: transform .18s, box-shadow .18s;
            letter-spacing: .01em;
        }

        .slider-btn:hover { transform: translateY(-3px); }
        .slider-btn:active { transform: translateY(0); }

        .slider-btn-prev {
            background: #fff;
            color: #7c3aed;
            border: 2px solid #c4b5fd;
            box-shadow: 0 4px 14px rgba(124,58,237,.1);
        }

        .slider-btn-prev:hover {
            background: #f5f3ff;
            box-shadow: 0 8px 20px rgba(124,58,237,.18);
        }

        .slider-btn-next {
            background: linear-gradient(135deg, #7c3aed, #a855f7);
            color: #fff;
            box-shadow: 0 4px 18px rgba(124,58,237,.35);
        }

        .slider-btn-next:hover {
            box-shadow: 0 8px 28px rgba(124,58,237,.48);
        }

        .slider-arrow {
            font-size: 1.1rem;
        }

        .slider-divider {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #d8b4fe;
        }

        /* Bid countdown timer */
        .bid-timer-wrap {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #fef3c7, #fde68a);
            border: 2px solid #f59e0b;
            border-radius: 12px;
            padding: 5px 14px;
            font-weight: 900;
            margin-left: 12px;
            animation: timerPulse 1s ease-in-out infinite;
        }

        .bid-timer-num {
            font-size: 1.5rem;
            color: #92400e;
            min-width: 1.6rem;
            text-align: center;
            line-height: 1;
        }

        .bid-timer-label {
            font-size: 0.7rem;
            font-weight: 800;
            color: #b45309;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        @keyframes timerPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.04); }
        }

        .bid-timer-wrap.danger {
            background: linear-gradient(135deg, #fee2e2, #fca5a5);
            border-color: #ef4444;
            animation: timerPulse .35s ease-in-out infinite;
        }

        .bid-timer-wrap.danger .bid-timer-num,
        .bid-timer-wrap.danger .bid-timer-label { color: #b91c1c; }

        /* Sell character button */
        .sell-btn {
            background: linear-gradient(135deg, #16a34a, #22c55e);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 8px 18px;
            font-size: 0.85rem;
            font-weight: 800;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(22,163,74,.35);
            transition: transform .18s, box-shadow .18s;
        }

        .sell-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 22px rgba(22,163,74,.45);
        }

        .sell-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="site-header-inner">
            <div class="brand">Auction Grid Battle</div>
            <nav class="nav">
                <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
                <a href="{{ route('auction') }}" class="nav-link active">Auction</a>
                <a href="{{ route('deck') }}" class="nav-link">Deck</a>
                <a href="{{ route('battleground') }}" class="nav-link">Battle Ground</a>
                <a href="#" class="nav-link">Leaderboard</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="topbar">
            <div>
                <h1 class="title">Auction</h1>
                <p class="subtitle">Live auction page for both admin and team users.</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-btn" type="submit">Logout</button>
            </form>
        </div>

        <div class="card">
            <div class="status-row">
                <div>
                    <p class="label">Auction Status</p>
                    <span id="auction-status" class="status-pill {{ $isActive ? 'status-active' : 'status-inactive' }}">
                        {{ $isActive ? 'Active' : 'Inactive' }}
                    </span>
                    <span id="bid-status" class="status-pill {{ $isBidActive ? 'status-bid' : 'status-inactive' }}" style="margin-left:8px;">
                        {{ $isBidActive ? 'Bid Open' : 'Bid Closed' }}
                    </span>
                    <span id="bid-timer-wrap" class="bid-timer-wrap hidden">
                        ⏱ <span id="bid-timer-num" class="bid-timer-num">10</span><span class="bid-timer-label">sec</span>
                    </span>
                </div>

                @if ($isAdmin)
                    <div style="display:flex;gap:8px;">
                        <button id="start-auction-btn" class="start-btn" type="button">
                            {{ $isActive ? 'Close Auction' : 'Start Auction' }}
                        </button>
                        <button id="start-bid-btn" class="start-btn" type="button" {{ (!$isActive || $isBidActive) ? 'disabled' : '' }}>
                            Start Bid
                        </button>
                        <button id="close-bid-btn" class="start-btn" type="button" {{ $isBidActive ? '' : 'disabled' }}>
                            Close Bid
                        </button>
                        <button id="sell-character-btn" class="sell-btn {{ (!$isActive || $isBidActive) ? 'hidden' : '' }}" type="button">
                            🏷️ Sell Character
                        </button>
                    </div>
                @endif
            </div>

            <p class="label">Current Lot</p>
            <p id="current-lot" class="value">{{ $isActive ? 'Auction is live now' : 'No active auction yet' }}</p>
            <p id="status-current-bid" class="value hidden">Current bid: <span id="status-price">—</span></p>
            <p id="status-last-team" class="value hidden">Last bid by: <span id="status-team-name">—</span></p>

            @if (!$isAdmin)
                <div class="bottom-actions">
                    <button id="user-bid-btn" class="bid-btn {{ $isBidActive ? '' : 'hidden' }}" type="button">Bid Now</button>
                </div>
            @endif
        </div>

        <div id="auction-dashboard" class="{{ $isActive ? '' : 'hidden' }}">
            <div class="dash-wrap">
                <div class="dash-inner">

                    {{-- Header --}}
                    <div class="dash-header">
                        <h2 class="dash-title">🎮 Live Auction Stage</h2>
                        <span class="live-badge">Live Now</span>
                    </div>

                    {{-- Hero card --}}
                    <div id="auction-character-card" class="hero-card">

                        {{-- Left: image --}}
                        <div class="hero-img-wrap">
                            <img id="character-image"
                                 src="https://placehold.co/360x480/ede9fe/6d28d9?text=No+Image"
                                 alt="Character image" />
                            <span id="char-role-pill" class="hero-img-role">Unknown</span>
                        </div>

                        {{-- Right: all data --}}
                        <div class="hero-info">
                            <div>
                                <h3 id="char-name" class="hero-name">No character selected</h3>
                                <p id="char-description" class="hero-desc">Start the auction to reveal the first hero.</p>
                            </div>

                            {{-- Stats grid --}}
                            <div class="stats-grid">
                                <div class="stat-chip">
                                    <span class="stat-chip-label">❤️ HP</span>
                                    <span id="char-hp" class="stat-chip-value">—</span>
                                </div>
                                <div class="stat-chip">
                                    <span class="stat-chip-label">⚔️ Damage</span>
                                    <span id="char-damage" class="stat-chip-value">—</span>
                                </div>
                                <div class="stat-chip">
                                    <span class="stat-chip-label">💨 Speed</span>
                                    <span id="char-speed" class="stat-chip-value">—</span>
                                </div>
                                <div class="stat-chip">
                                    <span class="stat-chip-label">🎯 Range</span>
                                    <span id="char-range" class="stat-chip-value">—</span>
                                </div>
                                <div class="stat-chip">
                                    <span class="stat-chip-label">⏱ Cooldown</span>
                                    <span id="char-cooldown" class="stat-chip-value">—</span>
                                </div>
                                <div class="price-tag">
                                    <span>💰</span>
                                    <span id="char-price">—</span>
                                    <span style="font-size:.7rem;font-weight:700;opacity:.7">coins</span>
                                </div>
                                <p id="char-last-team" class="text-sm" style="margin-top:6px;color:#444;">Last bid: —</p>
                            </div>

                            {{-- Ability --}}
                            <div class="ability-box">
                                <p class="ability-label">✨ Special Ability</p>
                                <p id="char-ability" class="ability-text">—</p>
                            </div>
                        </div>
                    </div>

                    {{-- Slider controls (admin only) --}}
                    @if ($isAdmin)
                        <div class="slider-controls">
                            <button id="prev-character-btn" type="button" class="slider-btn slider-btn-prev">
                                <span class="slider-arrow">◀</span>
                                <span>Previous</span>
                            </button>
                            <span class="slider-divider"></span>
                            <span class="slider-divider"></span>
                            <span class="slider-divider"></span>
                            <button id="next-character-btn" type="button" class="slider-btn slider-btn-next">
                                <span>Next Hero</span>
                                <span class="slider-arrow">▶</span>
                            </button>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    <script>
        const statusEl = document.getElementById('auction-status');
        const lotEl = document.getElementById('current-lot');
        const startBtn = document.getElementById('start-auction-btn');
        const startBidBtn = document.getElementById('start-bid-btn');
        const closeBidBtn = document.getElementById('close-bid-btn');
        const bidStatusEl = document.getElementById('bid-status');
        const userBidBtn = document.getElementById('user-bid-btn');
        const sellCharacterBtn = document.getElementById('sell-character-btn');
        const timerWrapEl = document.getElementById('bid-timer-wrap');
        const timerNumEl = document.getElementById('bid-timer-num');
        const dashboardEl = document.getElementById('auction-dashboard');
        const prevCharacterBtn = document.getElementById('prev-character-btn');
        const nextCharacterBtn = document.getElementById('next-character-btn');

        const charImageEl = document.getElementById('character-image');
        const charNameEl = document.getElementById('char-name');
        const charRolePillEl = document.getElementById('char-role-pill');
        const charDescriptionEl = document.getElementById('char-description');
        const charHpEl = document.getElementById('char-hp');
        const charDamageEl = document.getElementById('char-damage');
        const charSpeedEl = document.getElementById('char-speed');
        const charRangeEl = document.getElementById('char-range');
        const charCooldownEl = document.getElementById('char-cooldown');
        const charPriceEl = document.getElementById('char-price');
        const charLastTeamEl = document.getElementById('char-last-team');
        const statusPriceEl = document.getElementById('status-price');
        const statusTeamEl = document.getElementById('status-team-name');
        const statusBidEl = document.getElementById('status-current-bid');
        const statusLastTeamEl = document.getElementById('status-last-team');
        const charAbilityEl = document.getElementById('char-ability');

        const startUrl = "{{ route('auction.start') }}";
        const nextCharacterUrl = "{{ route('auction.character.next') }}";
        const prevCharacterUrl = "{{ route('auction.character.prev') }}";
        const bidUrl = "{{ route('auction.bid') }}";
        const startBidUrl = "{{ route('auction.startBid') }}";
        const closeBidUrl = "{{ route('auction.closeBid') }}";
        const sellUrl = "{{ route('auction.sell') }}";
        const statusUrl = "{{ route('auction.status') }}";
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const isAdmin = {{ $isAdmin ? 'true' : 'false' }};

        let currentCharacter = @json($currentCharacter);

        // ── Bid countdown timer (single global tick so numbers always update) ──
        const BID_DURATION = 90; // 90 seconds per bid phase
        let bidEndTimeMs = null; // when bid phase ends (timestamp); null = no countdown

        const stopBidTimer = () => {
            bidEndTimeMs = null;
            if (timerWrapEl) { timerWrapEl.classList.add('hidden'); timerWrapEl.classList.remove('danger'); }
            if (timerNumEl) timerNumEl.textContent = BID_DURATION;
        };

        const startBidTimer = () => {
            bidEndTimeMs = Date.now() + BID_DURATION * 1000;
            if (timerWrapEl) timerWrapEl.classList.remove('hidden', 'danger');
            if (timerNumEl) timerNumEl.textContent = BID_DURATION;
        };

        const resetBidTimer = () => {
            bidEndTimeMs = Date.now() + BID_DURATION * 1000;
            if (timerWrapEl) timerWrapEl.classList.remove('hidden', 'danger');
        };

        // Single 1-second tick: update countdown display and handle expiry
        setInterval(() => {
            if (bidEndTimeMs == null) return;
            const remaining = Math.max(0, Math.ceil((bidEndTimeMs - Date.now()) / 1000));
            if (timerNumEl) timerNumEl.textContent = remaining;
            if (timerWrapEl) {
                timerWrapEl.classList.toggle('danger', remaining <= 2);
            }
            if (remaining <= 0) {
                stopBidTimer();
                if (isAdmin) {
                    fetch(closeBidUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'X-Requested-With': 'XMLHttpRequest' },
                        body: JSON.stringify({}),
                    }).then(resp => resp.ok ? resp.json() : null).then(d => {
                        if (d) setStatusUI(!!d.active, false);
                    }).catch(() => {});
                }
            }
        }, 1000);
        // ────────────────────────────────────────────────────────────

        const normalizeImageUrl = (image) => {
            if (!image) {
                return 'https://placehold.co/320x320/ede9fe/6d28d9?text=No+Image';
            }

            if (image.startsWith('http://') || image.startsWith('https://') || image.startsWith('/')) {
                return image;
            }

            if (image.startsWith('storage/') || image.startsWith('charac_img/')) {
                return `/${image}`;
            }

            return `/storage/${image}`;
        };

        const parseAbility = (abilities) => {
            if (!abilities) return '—';

            if (typeof abilities === 'object') {
                if (abilities.special_ability) return abilities.special_ability;
                return JSON.stringify(abilities);
            }

            if (typeof abilities === 'string') {
                try {
                    const parsed = JSON.parse(abilities);
                    if (parsed && typeof parsed === 'object' && parsed.special_ability) {
                        return parsed.special_ability;
                    }
                    if (typeof parsed === 'string') {
                        return parsed;
                    }
                } catch (e) {
                    // plain string fallback
                }

                return abilities;
            }

            return '—';
        };

        const setDashboardVisible = (isVisible) => {
            if (dashboardEl) {
                dashboardEl.classList.toggle('hidden', !isVisible);
            }
        };

        const updateCurrentLotText = (isActive) => {
            if (!isActive) {
                lotEl.textContent = 'No active auction yet';
                return;
            }

            lotEl.textContent = currentCharacter?.name
                ? `Now auctioning: ${currentCharacter.name}`
                : 'Auction is live now';
        };

        const renderCharacter = (character) => {
            currentCharacter = character || null;

            if (!charNameEl) return;

            if (!currentCharacter) {
                charImageEl.src = normalizeImageUrl('');
                charNameEl.textContent = 'No character selected';
                charRolePillEl.textContent = 'Unknown';
                charDescriptionEl.textContent = 'Start the auction to reveal the first hero.';
                charHpEl.textContent = '—';
                charDamageEl.textContent = '—';
                charSpeedEl.textContent = '—';
                charRangeEl.textContent = '—';
                charCooldownEl.textContent = '—';
                charPriceEl.textContent = '—';
                if (charLastTeamEl) charLastTeamEl.textContent = 'Last bid: —';
                if (statusBidEl) statusBidEl.classList.add('hidden');
                if (statusLastTeamEl) statusLastTeamEl.classList.add('hidden');
                charAbilityEl.textContent = '—';
                return;
            }

            charImageEl.src = normalizeImageUrl(currentCharacter.image || '');
            charNameEl.textContent = currentCharacter.name || 'Unknown Hero';
            charRolePillEl.textContent = currentCharacter.role || 'Unknown';
            charDescriptionEl.textContent = currentCharacter.description || 'No description available.';
            charHpEl.textContent = currentCharacter.hp ?? '—';
            charDamageEl.textContent = currentCharacter.damage ?? '—';
            charSpeedEl.textContent = currentCharacter.speed ?? '—';
            charRangeEl.textContent = currentCharacter.range ?? '—';
            charCooldownEl.textContent = currentCharacter.cooldown ?? '—';
            charPriceEl.textContent = currentCharacter.base_price ?? '—';
            if (charLastTeamEl) {
                if (currentCharacter.last_team && currentCharacter.last_team.name) {
                    charLastTeamEl.textContent = `Last bid: ${currentCharacter.last_team.name}`;
                } else {
                    charLastTeamEl.textContent = 'Last bid: —';
                }
            }
            // status bar
            if (statusPriceEl) {
                statusPriceEl.textContent = currentCharacter.base_price ?? '—';
            }
            if (statusTeamEl) {
                statusTeamEl.textContent = currentCharacter.last_team?.name ?? '—';
            }
            if (statusBidEl) {
                statusBidEl.classList.toggle('hidden', !currentCharacter);
            }
            if (statusLastTeamEl) {
                statusLastTeamEl.classList.toggle('hidden', !currentCharacter);
            }
            charAbilityEl.textContent = parseAbility(currentCharacter.abilities);
        };

        const setStatusUI = (isActive, isBidActive) => {
            statusEl.textContent = isActive ? 'Active' : 'Inactive';
            statusEl.classList.toggle('status-active', isActive);
            statusEl.classList.toggle('status-inactive', !isActive);

            bidStatusEl.textContent = isBidActive ? 'Bid Open' : 'Bid Closed';
            bidStatusEl.classList.toggle('status-bid', isBidActive);
            bidStatusEl.classList.toggle('status-inactive', !isBidActive);

            if (startBtn) {
                startBtn.disabled = false;
                startBtn.textContent = isActive ? 'Close Auction' : 'Start Auction';
            }

            if (startBidBtn) {
                startBidBtn.disabled = !isActive || isBidActive;
            }

            if (closeBidBtn) {
                closeBidBtn.disabled = !isBidActive;
            }

            if (userBidBtn) {
                userBidBtn.classList.toggle('hidden', !isBidActive);
            }

            // Timer: start when bid opens (only if not already counting), stop when bid closes
            if (isBidActive) {
                if (bidEndTimeMs == null) startBidTimer();
            } else {
                stopBidTimer();
            }

            // Sell Character button: admin only, shown when bid is closed and auction active
            if (sellCharacterBtn) {
                sellCharacterBtn.classList.toggle('hidden', !isActive || isBidActive);
            }

            // keep status bar values in sync
            if (isActive && currentCharacter) {
                if (statusPriceEl) statusPriceEl.textContent = currentCharacter.base_price ?? '—';
                if (statusTeamEl) statusTeamEl.textContent = currentCharacter.last_team?.name ?? '—';
                if (statusBidEl) statusBidEl.classList.remove('hidden');
                if (statusLastTeamEl) statusLastTeamEl.classList.remove('hidden');
            } else {
                if (statusBidEl) statusBidEl.classList.add('hidden');
                if (statusLastTeamEl) statusLastTeamEl.classList.add('hidden');
            }
            setDashboardVisible(isActive);
            updateCurrentLotText(isActive);
        };

        if (startBtn) {
            startBtn.addEventListener('click', async () => {
                try {
                    const response = await fetch(startUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({}),
                    });

                    if (!response.ok) return;

                    const data = await response.json();
                    setStatusUI(!!data.active, !!data.bid_active);

                    if (data.active) {
                        renderCharacter(data.current_character || currentCharacter);
                    } else {
                        renderCharacter(null);
                    }
                } catch (e) {
                    // silent fail to keep UI usable
                }
            });
        }

        const changeCharacter = async (url) => {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({}),
                });

                if (!response.ok) return;

                const data = await response.json();
                setStatusUI(!!data.active, !!data.bid_active);
                renderCharacter(data.current_character || currentCharacter);
            } catch (e) {
                // silent fail to keep UI usable
            }
        };

        if (isAdmin && prevCharacterBtn) {
            prevCharacterBtn.addEventListener('click', async () => {
                await changeCharacter(prevCharacterUrl);
            });
        }

        if (isAdmin && nextCharacterBtn) {
            nextCharacterBtn.addEventListener('click', async () => {
                await changeCharacter(nextCharacterUrl);
            });
        }

        if (userBidBtn) {
            userBidBtn.addEventListener('click', async () => {
                try {
                    // optimistic UI update so bid feels instant
                    if (charPriceEl) {
                        const curr = parseInt(charPriceEl.textContent) || 0;
                        const newVal = curr + 100;
                        charPriceEl.textContent = newVal;
                        if (currentCharacter) currentCharacter.base_price = newVal;
                    }
                    userBidBtn.disabled = true;
                    userBidBtn.textContent = 'Bidding...';
                    const response = await fetch(bidUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({}),
                    });
                    if (response.ok) {
                        const data = await response.json();
                        if (charPriceEl && data.new_price !== undefined) {
                            charPriceEl.textContent = data.new_price;
                            if (currentCharacter) currentCharacter.base_price = data.new_price;
                        }
                        if (charLastTeamEl && data.team_name) {
                            charLastTeamEl.textContent = `Last bid: ${data.team_name}`;
                        }
                    }
                } catch (e) {
                    // silent fail
                } finally {
                    userBidBtn.disabled = false;
                    userBidBtn.textContent = 'Bid Now 💰';
                }
            });
        }

        if (sellCharacterBtn) {
            sellCharacterBtn.addEventListener('click', async () => {
                try {
                    sellCharacterBtn.disabled = true;
                    sellCharacterBtn.textContent = 'Selling...';
                    const response = await fetch(sellUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({}),
                    });
                    const data = await response.json();
                    if (response.ok) {
                        sellCharacterBtn.textContent = '✅ Sold!';
                        setTimeout(() => {
                            sellCharacterBtn.textContent = '🏷️ Sell Character';
                            sellCharacterBtn.disabled = false;
                        }, 2500);
                    } else {
                        alert(data.message || 'Could not sell character');
                        sellCharacterBtn.textContent = '🏷️ Sell Character';
                        sellCharacterBtn.disabled = false;
                    }
                } catch (e) {
                    sellCharacterBtn.textContent = '🏷️ Sell Character';
                    sellCharacterBtn.disabled = false;
                }
            });
        }

        if (startBidBtn) {
            startBidBtn.addEventListener('click', async () => {
                try {
                    const response = await fetch(startBidUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({}),
                    });

                    if (!response.ok) return;

                    const data = await response.json();
                    setStatusUI(!!data.active, !!data.bid_active);
                } catch (e) {
                    // silent fail to keep UI usable
                }
            });
        }

        if (closeBidBtn) {
            closeBidBtn.addEventListener('click', async () => {
                try {
                    const response = await fetch(closeBidUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({}),
                    });

                    if (!response.ok) return;

                    const data = await response.json();
                    setStatusUI(!!data.active, !!data.bid_active);
                } catch (e) {
                    // silent fail to keep UI usable
                }
            });
        }

        renderCharacter(currentCharacter);
        setStatusUI({{ $isActive ? 'true' : 'false' }}, {{ $isBidActive ? 'true' : 'false' }});

        window.addEventListener('load', () => {
            if (window.Echo) {
                try {
                    window.Echo.channel('auction')
                        .listen('.status.updated', (event) => {
                            setStatusUI(!!event.active, !!event.bid_active);
                        })
                        .listen('.auction.started', (event) => {
                            setStatusUI(true, !!event.bid_active);
                            renderCharacter(event.character || currentCharacter);
                        })
                        .listen('.character.changed', (event) => {
                            if (event.character) {
                                renderCharacter(event.character);
                                updateCurrentLotText(true);
                            }
                        })
                        .listen('.auction.closed', () => {
                            setStatusUI(false, false);
                            renderCharacter(null);
                        })
                        .listen('.bid.placed', (event) => {
                            if (charPriceEl && currentCharacter && event.character_id === currentCharacter.id) {
                                charPriceEl.textContent = event.new_price;
                                currentCharacter.base_price = event.new_price;
                                if (charLastTeamEl && event.team_name) {
                                    charLastTeamEl.textContent = `Last bid: ${event.team_name}`;
                                }
                                if (statusPriceEl) statusPriceEl.textContent = event.new_price;
                                if (statusTeamEl && event.team_name) statusTeamEl.textContent = event.team_name;
                            }
                            resetBidTimer(); // reset countdown on every new bid
                        });
                } catch (e) {
                    console.warn('Echo channel subscription failed, falling back to polling.', e);
                }
            } else {
                console.warn('window.Echo is not available, falling back to polling.');
            }
        });

        const pollStatus = async () => {
            try {
                const response = await fetch(statusUrl, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    cache: 'no-store',
                });

                if (!response.ok) return;

                const data = await response.json();
                setStatusUI(!!data.active, !!data.bid_active);

                if (data.active) {
                    renderCharacter(data.current_character || currentCharacter);
                } else {
                    renderCharacter(null);
                }
            } catch (e) {
                // ignore polling errors
            }
        };

        setInterval(pollStatus, 2000);
    </script>
    @vite('resources/js/app.js')
</body>
</html>

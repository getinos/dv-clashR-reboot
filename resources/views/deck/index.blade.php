<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Deck - Auction Grid Battle</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding-bottom: 50px;
        }

        /* Header */
        .site-header {
            background: rgba(255, 255, 255, 0.97);
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .site-header-inner {
            max-width: 1400px;
            margin: 0 auto;
            padding: 16px 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand {
            font-size: 1.4rem;
            font-weight: 900;
            background: linear-gradient(135deg, #667eea, #764ba2);
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
            color: #4a5568;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s;
        }

        .nav-link:hover {
            background: #f7fafc;
            color: #667eea;
        }

        .nav-link.active {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        /* Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 40px 32px;
        }

        /* Header section */
        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
        }

        .title {
            font-size: 2.2rem;
            font-weight: 900;
            color: white;
            margin-bottom: 4px;
        }

        .subtitle {
            color: rgba(255, 255, 255, 0.85);
            font-size: 1rem;
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
        }

        /* Empty state */
        .empty-state {
            background: white;
            border-radius: 16px;
            padding: 80px 40px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .empty-state-icon {
            font-size: 5rem;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .empty-state h3 {
            font-size: 1.5rem;
            color: #2d3748;
            margin-bottom: 12px;
        }

        .empty-state p {
            color: #718096;
            font-size: 1rem;
        }

        /* Cards grid */
        .deck-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 32px;
            perspective: 1000px;
        }

        /* Trump card styling */
        .trump-card {
            position: relative;
            background: linear-gradient(145deg, #ffffff, #f0f0f0);
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            border: 3px solid #e0e0e0;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
            height: 480px;
            display: flex;
            flex-direction: column;
        }

        .trump-card:hover {
            transform: translateY(-8px) rotateY(5deg);
            box-shadow: 0 16px 48px rgba(0, 0, 0, 0.2);
            border-color: #667eea;
        }

        /* Card corner badges */
        .card-corner {
            position: absolute;
            font-size: 1.8rem;
            font-weight: 900;
            color: #667eea;
            line-height: 1;
        }

        .card-corner-tl {
            top: 16px;
            left: 16px;
        }

        .card-corner-br {
            bottom: 16px;
            right: 16px;
            transform: rotate(180deg);
        }

        /* Character image */
        .card-image-wrap {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px 0;
            position: relative;
            overflow: hidden;
            border-radius: 12px;
            background: linear-gradient(135deg, #f5f3ff, #ede9fe);
        }

        .card-image {
            max-width: 100%;
            max-height: 220px;
            object-fit: contain;
            filter: drop-shadow(0 4px 12px rgba(0, 0, 0, 0.15));
        }

        /* Role badge */
        .card-role-badge {
            position: absolute;
            top: 12px;
            right: 12px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        /* Character name */
        .card-name {
            font-size: 1.3rem;
            font-weight: 900;
            text-align: center;
            color: #2d3748;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.02em;
        }

        /* Stats row */
        .card-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-bottom: 12px;
        }

        .card-stat {
            background: linear-gradient(135deg, #f7fafc, #edf2f7);
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 8px;
            text-align: center;
        }

        .card-stat-label {
            font-size: 0.65rem;
            font-weight: 700;
            color: #718096;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 4px;
        }

        .card-stat-value {
            font-size: 1.1rem;
            font-weight: 900;
            color: #2d3748;
        }

        /* Description */
        .card-description {
            font-size: 0.8rem;
            color: #4a5568;
            text-align: center;
            line-height: 1.4;
            margin-top: auto;
            padding-top: 12px;
            border-top: 2px dashed #e2e8f0;
        }

        /* Ornamental corners */
        .card-ornament {
            position: absolute;
            width: 40px;
            height: 40px;
            border: 3px solid #667eea;
            opacity: 0.3;
        }

        .card-ornament-tl {
            top: 8px;
            left: 8px;
            border-right: none;
            border-bottom: none;
            border-radius: 8px 0 0 0;
        }

        .card-ornament-tr {
            top: 8px;
            right: 8px;
            border-left: none;
            border-bottom: none;
            border-radius: 0 8px 0 0;
        }

        .card-ornament-bl {
            bottom: 8px;
            left: 8px;
            border-right: none;
            border-top: none;
            border-radius: 0 0 0 8px;
        }

        .card-ornament-br {
            bottom: 8px;
            right: 8px;
            border-left: none;
            border-top: none;
            border-radius: 0 0 8px 0;
        }
    </style>
</head>
<body>
    <header class="site-header">
        <div class="site-header-inner">
            <div class="brand">Auction Grid Battle</div>
            <nav class="nav">
                <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
                <a href="{{ route('auction') }}" class="nav-link">Auction</a>
                <a href="{{ route('deck') }}" class="nav-link active">Deck</a>
                <a href="{{ route('battleground') }}" class="nav-link">Battle Ground</a>
                <a href="#" class="nav-link">Leaderboard</a>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="topbar">
            <div>
                <h1 class="title">🃏 My Deck</h1>
                <p class="subtitle">Your collection of purchased characters</p>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="logout-btn" type="submit">Logout</button>
            </form>
        </div>

        @if ($characters->isEmpty())
            <div class="empty-state">
                <div class="empty-state-icon">🎴</div>
                <h3>Your Deck is Empty</h3>
                <p>You haven't purchased any characters yet. Head to the auction to start bidding!</p>
            </div>
        @else
            <div class="deck-grid">
                @foreach ($characters as $character)
                    <div class="trump-card">
                        {{-- Ornamental corners --}}
                        <div class="card-ornament card-ornament-tl"></div>
                        <div class="card-ornament card-ornament-tr"></div>
                        <div class="card-ornament card-ornament-bl"></div>
                        <div class="card-ornament card-ornament-br"></div>

                        {{-- Corner symbols --}}
                        <div class="card-corner card-corner-tl">♠</div>
                        <div class="card-corner card-corner-br">♠</div>

                        {{-- Character image with role badge --}}
                        <div class="card-image-wrap">
                            @if ($character->image)
                                @php
                                    $imageSrc = $character->image;
                                    if (!str_starts_with($imageSrc, 'http') && !str_starts_with($imageSrc, '/')) {
                                        $imageSrc = '/' . $imageSrc;
                                    }
                                @endphp
                                <img src="{{ $imageSrc }}" alt="{{ $character->name }}" class="card-image">
                            @else
                                <img src="https://placehold.co/240x240/ede9fe/6d28d9?text=No+Image" alt="{{ $character->name }}" class="card-image">
                            @endif
                            <span class="card-role-badge">{{ $character->role_name ?? 'Unknown' }}</span>
                        </div>

                        {{-- Character name --}}
                        <h3 class="card-name">{{ $character->name }}</h3>

                        {{-- Stats grid --}}
                        <div class="card-stats">
                            <div class="card-stat">
                                <div class="card-stat-label">❤️ HP</div>
                                <div class="card-stat-value">{{ $character->hp }}</div>
                            </div>
                            <div class="card-stat">
                                <div class="card-stat-label">⚔️ DMG</div>
                                <div class="card-stat-value">{{ $character->damage }}</div>
                            </div>
                            <div class="card-stat">
                                <div class="card-stat-label">💨 SPD</div>
                                <div class="card-stat-value">{{ $character->speed }}</div>
                            </div>
                            <div class="card-stat">
                                <div class="card-stat-label">🎯 RNG</div>
                                <div class="card-stat-value">{{ $character->range }}</div>
                            </div>
                            <div class="card-stat">
                                <div class="card-stat-label">⏱ CD</div>
                                <div class="card-stat-value">{{ $character->cooldown }}</div>
                            </div>
                            <div class="card-stat">
                                <div class="card-stat-label">💰 PRC</div>
                                <div class="card-stat-value">{{ $character->base_price }}</div>
                            </div>
                        </div>

                        {{-- Description --}}
                        <p class="card-description">{{ Str::limit($character->description, 80) }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @vite('resources/js/app.js')
</body>
</html>

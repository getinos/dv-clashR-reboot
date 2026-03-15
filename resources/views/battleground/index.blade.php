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
            grid-template-columns: repeat(8, 1fr);
            gap: 8px;
            max-width: 800px;
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
        @endif

        @if(!$isAdmin)
            <div class="instructions">
                💡 <strong>How to Deploy:</strong> Click a character card below, then click a grid tile to deploy. Or drag and drop!
            </div>
        @endif

        <!-- Battle Arena -->
        <div class="arena-section">
            <h2 class="arena-title">🎯 Battle Arena (8x8)</h2>
            <div class="battle-grid" id="battle-grid">
                @for($y = 0; $y < 8; $y++)
                    @for($x = 0; $x < 8; $x++)
                        <div class="grid-cell" data-x="{{ $x }}" data-y="{{ $y }}"></div>
                    @endfor
                @endfor
            </div>
        </div>

        <!-- Character Deck -->
        @if(!$isAdmin)
        <div class="deck-section">
            <h2 class="deck-title">🃏 Your Character Deck</h2>
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
        </div>
        @endif
    </div>

    <script>
        const isAdmin = {{ $isAdmin ? 'true' : 'false' }};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const deployUrl = "{{ route('battleground.deploy') }}";

        let selectedCharacter = null;
        let deployedCharacters = {}; // track deployed positions

        // Grid cells
        const gridCells = document.querySelectorAll('.grid-cell');
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
                    cardElement: card
                };
            });

            // Drag start
            card.addEventListener('dragstart', (e) => {
                if (isAdmin) return;
                card.classList.add('dragging');
                selectedCharacter = {
                    id: card.dataset.characterId,
                    name: card.dataset.characterName,
                    role: card.dataset.characterRole,
                    image: card.dataset.characterImage,
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

                // Check if cell is occupied
                if (deployedCharacters[`${x},${y}`]) {
                    return;
                }

                await deployCharacter(selectedCharacter, x, y);
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

                if (selectedCharacter && !deployedCharacters[`${x},${y}`]) {
                    await deployCharacter(selectedCharacter, x, y);
                }
            });
        });

        async function deployCharacter(character, x, y) {
            try {
                console.log('🚀 Deploying character:', character.name, 'to position', x, y);
                const response = await fetch(deployUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        character_id: character.id,
                        grid_x: x,
                        grid_y: y,
                    }),
                });

                const data = await response.json();
                console.log('📡 Server response:', data);

                if (response.ok) {
                    // Mark character card as deployed
                    character.cardElement.classList.add('deployed');
                    
                    // Place on grid locally (WebSocket will sync to others)
                    placeCharacterOnGrid(character, x, y);

                    // Clear selection
                    characterCards.forEach(c => c.style.border = '2px solid rgba(59, 130, 246, 0.3)');
                    selectedCharacter = null;
                } else {
                    console.error('❌ Deployment failed:', data);
                    alert(data.message || 'Deployment failed');
                }
            } catch (error) {
                console.error('❌ Deployment error:', error);
                alert('Failed to deploy character. Check console for details.');
            }
        }

        function placeCharacterOnGrid(character, x, y) {
            const cell = document.querySelector(`[data-x="${x}"][data-y="${y}"]`);
            if (!cell || deployedCharacters[`${x},${y}`]) return;

            cell.classList.add('occupied');
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

        // WebSocket listener
        window.addEventListener('load', () => {
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
        });
    </script>

    @vite('resources/js/app.js')
</body>
</html>

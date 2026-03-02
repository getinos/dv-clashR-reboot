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
    </style>
</head>
<body>
    <header class="site-header">
        <div class="site-header-inner">
            <div class="brand">Auction Grid Battle</div>
            <nav class="nav">
                <a href="{{ route('dashboard') }}" class="nav-link">Dashboard</a>
                <a href="{{ route('auction') }}" class="nav-link active">Auction</a>
                <a href="#" class="nav-link">Deck</a>
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
                </div>

                @if ($isAdmin)
                    <div style="display:flex;gap:8px;">
                        <button id="start-auction-btn" class="start-btn" type="button" {{ $isActive ? 'disabled' : '' }}>
                            Start Auction
                        </button>
                        <button id="start-bid-btn" class="start-btn" type="button" {{ (!$isActive || $isBidActive) ? 'disabled' : '' }}>
                            Start Bid
                        </button>
                        <button id="close-bid-btn" class="start-btn" type="button" {{ $isBidActive ? '' : 'disabled' }}>
                            Close Bid
                        </button>
                    </div>
                @endif
            </div>

            <p class="label">Current Lot</p>
            <p id="current-lot" class="value">{{ $isActive ? 'Auction is live now' : 'No active auction yet' }}</p>

            @if (!$isAdmin)
                <div class="bottom-actions">
                    <button id="user-bid-btn" class="bid-btn {{ $isBidActive ? '' : 'hidden' }}" type="button">Bid Now</button>
                </div>
            @endif
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
        const statusUrl = "{{ route('auction.status') }}";
        const startUrl = "{{ route('auction.start') }}";
        const startBidUrl = "{{ route('auction.startBid') }}";
        const closeBidUrl = "{{ route('auction.closeBid') }}";
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        const setStatusUI = (isActive, isBidActive) => {
            statusEl.textContent = isActive ? 'Active' : 'Inactive';
            statusEl.classList.toggle('status-active', isActive);
            statusEl.classList.toggle('status-inactive', !isActive);
            lotEl.textContent = isActive ? 'Auction is live now' : 'No active auction yet';

            bidStatusEl.textContent = isBidActive ? 'Bid Open' : 'Bid Closed';
            bidStatusEl.classList.toggle('status-bid', isBidActive);
            bidStatusEl.classList.toggle('status-inactive', !isBidActive);

            if (startBtn) {
                startBtn.disabled = isActive;
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
        };

        const fetchStatus = async () => {
            try {
                const response = await fetch(`${statusUrl}?t=${Date.now()}`, {
                    cache: 'no-store',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Cache-Control': 'no-cache',
                        'Pragma': 'no-cache',
                    },
                });
                if (!response.ok) return;
                const data = await response.json();
                setStatusUI(!!data.active, !!data.bid_active);
            } catch (e) {
                // silent fail to keep UI usable
            }
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
                } catch (e) {
                    // silent fail to keep UI usable
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

        // user page keeps checking status via AJAX
        setStatusUI({{ $isActive ? 'true' : 'false' }}, {{ $isBidActive ? 'true' : 'false' }});
        fetchStatus();
        setInterval(fetchStatus, 2000);
    </script>
</body>
</html>

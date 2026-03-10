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

        /* SLIDER */

.slider-card{
text-align:center;
}

.slider{
display:flex;
align-items:center;
justify-content:center;
gap:20px;
}

.slide-btn{
border:none;
background:var(--primary);
color:white;
font-size:20px;
width:40px;
height:40px;
border-radius:50%;
cursor:pointer;
}

.fighter-card{
width:220px;
background:#f9fafb;
border-radius:12px;
padding:15px;
box-shadow:0 5px 15px rgba(0,0,0,0.1);
}

.fighter-img{
width:100%;
height:180px;
object-fit:cover;
border-radius:10px;
margin-bottom:10px;
}

/*.fighter-stats{
text-align:left;
font-size:14px;
}

.fighter-stats p{
margin:4px 0;
 }*/

.slider-section{
margin-top:20px;
padding-top:15px;
border-top:1px solid var(--border);
}

.slider-title{
margin-bottom:12px;
font-size:1.2rem;
font-weight:700;
}

.slider{
display:flex;
align-items:center;
justify-content:center;
gap:20px;
}

.slide-btn{
border:none;
background:var(--primary);
color:white;
width:36px;
height:36px;
border-radius:50%;
cursor:pointer;
font-size:18px;
}

.fighter-card{
width:220px;
background:#f9fafb;
border-radius:12px;
padding:15px;
text-align:center;
box-shadow:0 6px 16px rgba(0,0,0,0.08);
}

.fighter-img{
width:100%;
height:170px;
object-fit:cover;
border-radius:10px;
margin-bottom:8px;
}

.fighter-stats{
display:flex;
flex-direction:column;
gap:8px;
margin-top:10px;
}

.stat-top{
display:flex;
justify-content:space-between;
font-size:13px;
font-weight:600;
}

.bar{
height:8px;
background:#e5e7eb;
border-radius:20px;
overflow:hidden;
}

.fill{
height:100%;
transition:width .4s ease;
}

.hp{background:#22c55e;}
.damage{background:#ef4444;}
.range{background:#3b82f6;}
.cooldown{background:#f59e0b;}

.role{
margin-top:8px;
font-weight:600;
color:#374151;
}
    /* AUCTION 3 COLUMN LAYOUT */

.auction-layout{
display:grid;
grid-template-columns: 1fr 1.2fr 1fr;
align-items:center;
gap:40px;
margin-top:20px;
position:relative;
}

/* IMAGE PANEL */

.character-image-panel{
text-align:center;
}

.character-large-img{
width:260px;
height:320px;
object-fit:cover;
border-radius:14px;
box-shadow:0 10px 25px rgba(0,0,0,0.15);
}

.character-name{
margin-top:10px;
font-size:1.5rem;
font-weight:800;
}

.role{
margin-top:6px;
font-weight:600;
color:#374151;
}

/* STATS PANEL */

.character-stats-panel{
display:flex;
flex-direction:column;
gap:22px;
}

.big-stat{
width:100%;
}

.stat-head{
display:flex;
justify-content:space-between;
font-weight:700;
font-size:18px;
margin-bottom:6px;
}

.big-bar{
height:16px;
background:#e5e7eb;
border-radius:20px;
overflow:hidden;
}

.fill{
height:100%;
transition:width .4s ease;
}

/* TEAM DASHBOARD */

.team-dashboard{
background:#f9fafb;
border-radius:14px;
padding:16px;
box-shadow:0 6px 16px rgba(0,0,0,0.08);
}

.dashboard-title{
margin-top:0;
margin-bottom:10px;
font-size:18px;
font-weight:800;
}

.team-balance{
font-size:16px;
font-weight:700;
margin-bottom:12px;
color:#7C3AED;
}

.team-characters{
display:flex;
flex-direction:column;
gap:10px;
}

.team-char{
display:flex;
align-items:center;
gap:8px;
background:white;
padding:6px;
border-radius:8px;
border:1px solid #e5e7eb;
}

.team-char img{
width:35px;
height:35px;
border-radius:6px;
object-fit:cover;
}

/* SLIDER BUTTON POSITION */

.left-btn{
position:absolute;
left:-40px;
top:50%;
transform:translateY(-50%);
}

.right-btn{
position:absolute;
right:-40px;
top:50%;
transform:translateY(-50%);
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



    <!-- Character Auction Panel -->
<div class="slider-section">

<h3 class="slider-title">Auction Character</h3>

<div class="auction-layout">

    

    <!-- LEFT : CHARACTER IMAGE -->
    <div class="character-image-panel">

    <img id="cardImage" src="/images/fighter1.png" class="character-large-img">

    <h2 id="cardName" class="character-name">Blaze Knight</h2>

    <p class="role">Role: <span id="role">Fighter</span></p>

    @if ($isAdmin)
    <div class="slider-controls">
        <button class="slide-btn" onclick="prevSlide()">⬅</button>
        <button class="slide-btn" onclick="nextSlide()">➡</button>
    </div>
    @endif

</div>


    <!-- CENTER : CHARACTER STATS -->
    <div class="character-stats-panel">

        <div class="big-stat">
            <div class="stat-head">
                <span>HP</span>
                <span id="hpValue">90</span>
            </div>
            <div class="big-bar">
                <div id="hpBar" class="fill hp"></div>
            </div>
        </div>

        <div class="big-stat">
            <div class="stat-head">
                <span>Damage</span>
                <span id="damageValue">80</span>
            </div>
            <div class="big-bar">
                <div id="damageBar" class="fill damage"></div>
            </div>
        </div>

        <div class="big-stat">
            <div class="stat-head">
                <span>Range</span>
                <span id="rangeValue">60</span>
            </div>
            <div class="big-bar">
                <div id="rangeBar" class="fill range"></div>
            </div>
        </div>

        <div class="big-stat">
            <div class="stat-head">
                <span>Cooldown</span>
                <span id="cooldownValue">40</span>
            </div>
            <div class="big-bar">
                <div id="cooldownBar" class="fill cooldown"></div>
            </div>
        </div>

    </div>


    <!-- RIGHT : TEAM DASHBOARD -->
    <div class="team-dashboard">

        <h3 class="dashboard-title">Your Team</h3>

        <div class="team-balance">
            Balance: <span id="teamBalance">5000</span> Coins
        </div>

        <div class="team-characters" id="teamCharacters">

            <div class="team-char">
                <img src="/images/fighter1.png">
                <span>Blaze Knight</span>
            </div>

        </div>

    </div>

    
</div>

</div>


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

        /* Fighter Slider */

const fighters = [

{
name:"Blaze Knight",
img:"/images/fighter1.png",
hp:90,
damage:80,
range:60,
cooldown:40,
role:"Fighter"
},

{
name:"Shadow Assassin",
img:"/images/fighter2.png",
hp:70,
damage:95,
range:40,
cooldown:60,
role:"Assassin"
},

{
name:"Storm Archer",
img:"/images/fighter3.png",
hp:75,
damage:70,
range:90,
cooldown:50,
role:"Ranger"
},

{
name:"Iron Guardian",
img:"/images/fighter4.png",
hp:100,
damage:55,
range:35,
cooldown:65,
role:"Tank"
}

];

let currentFighter = 0;

function updateFighter(){

const fighter = fighters[currentFighter];

document.getElementById("cardImage").src = fighter.img;
document.getElementById("cardName").textContent = fighter.name;

document.getElementById("hpValue").textContent = fighter.hp;
document.getElementById("damageValue").textContent = fighter.damage;
document.getElementById("rangeValue").textContent = fighter.range;
document.getElementById("cooldownValue").textContent = fighter.cooldown;

document.getElementById("role").textContent = fighter.role;

document.getElementById("hpBar").style.width = fighter.hp + "%";
document.getElementById("damageBar").style.width = fighter.damage + "%";
document.getElementById("rangeBar").style.width = fighter.range + "%";
document.getElementById("cooldownBar").style.width = fighter.cooldown + "%";

}

function nextSlide(){

currentFighter++;

if(currentFighter >= fighters.length){
currentFighter = 0;
}

updateFighter();

}

function prevSlide(){

currentFighter--;

if(currentFighter < 0){
currentFighter = fighters.length - 1;
}

updateFighter();

}

updateFighter();
    </script>
</body>
</html>

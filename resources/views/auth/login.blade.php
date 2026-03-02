<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter the Arena</title>
    <style>
        :root {
            --bg: #EEF2FF;
            --panel: #E0E7FF;
            --primary: #7C3AED;
            --secondary: #10B981;
            --gold: #FBBF24;
            --danger: #EF4444;
            --text: #1F2937;
            --muted: #6B7280;
            --input: #F8FAFF;
            --border: #CBD5E1;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            color: var(--text);
            background:
                radial-gradient(1000px 500px at 10% -10%, rgba(124, 58, 237, 0.18), transparent),
                radial-gradient(800px 420px at 100% 100%, rgba(16, 185, 129, 0.12), transparent),
                var(--bg);
            display: grid;
            place-items: center;
            padding: 24px;
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background-image:
                linear-gradient(rgba(203, 213, 225, 0.65) 1px, transparent 1px),
                linear-gradient(90deg, rgba(203, 213, 225, 0.65) 1px, transparent 1px);
            background-size: 30px 30px;
            opacity: 0.22;
        }

        .login-shell {
            width: 100%;
            max-width: 450px;
        }

        .card {
            background: linear-gradient(180deg, rgba(224, 231, 255, 0.98) 0%, rgba(238, 242, 255, 0.98) 100%);
            border: 1px solid rgba(124, 58, 237, 0.2);
            border-radius: 16px;
            box-shadow: 0 16px 34px rgba(99, 102, 241, 0.15), 0 0 0 1px rgba(124, 58, 237, 0.06) inset;
            overflow: hidden;
            position: relative;
            z-index: 1;
        }

        .card-header {
            padding: 22px 24px 16px;
            border-bottom: 1px solid rgba(203, 213, 225, 0.9);
        }

        .title {
            margin: 0;
            font-size: 1.55rem;
            font-weight: 800;
            letter-spacing: 0.02em;
        }

        .subtitle {
            margin: 8px 0 0;
            color: var(--muted);
            font-size: 0.92rem;
        }

        .status-row {
            margin-top: 12px;
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            border: 1px solid transparent;
        }

        .pill-auction {
            background: rgba(251, 191, 36, 0.2);
            border-color: rgba(251, 191, 36, 0.6);
            color: #92400e;
        }

        .pill-combat {
            background: rgba(239, 68, 68, 0.14);
            border-color: rgba(239, 68, 68, 0.45);
            color: #991b1b;
        }

        .accent {
            color: var(--gold);
        }

        .card-body {
            padding: 22px 24px 24px;
        }

        .error-box {
            margin-bottom: 14px;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #7f1d1d;
            border-radius: 10px;
            padding: 10px 12px;
            font-size: 0.9rem;
        }

        .error-box ul {
            margin: 0;
            padding-left: 18px;
        }

        .field {
            margin-bottom: 14px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-size: 0.88rem;
            color: #374151;
            font-weight: 600;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            background: var(--input);
            border: 1px solid var(--border);
            border-radius: 10px;
            color: var(--text);
            padding: 11px 12px;
            font-size: 0.95rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.18);
        }

        .options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 6px 0 16px;
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #374151;
            font-size: 0.88rem;
        }

        .remember input {
            accent-color: var(--secondary);
        }

        .forgot {
            color: var(--primary);
            text-decoration: none;
            font-size: 0.84rem;
        }

        .forgot:hover { color: #6d28d9; }

        .btn {
            width: 100%;
            border: 0;
            border-radius: 10px;
            background: linear-gradient(90deg, #7C3AED 0%, #8B5CF6 60%, #10B981 100%);
            color: #fff;
            padding: 12px;
            font-size: 0.95rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 8px 20px rgba(124, 58, 237, 0.28);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 26px rgba(16, 185, 129, 0.24);
        }

        .footer-link {
            text-align: center;
            margin-top: 14px;
            font-size: 0.88rem;
            color: #4b5563;
        }

        .footer-link a {
            color: var(--gold);
            text-decoration: none;
            font-weight: 700;
        }

        .footer-link a:hover { color: #d97706; }
    </style>
</head>
<body>
    <div class="login-shell">
        <div class="card">
            <div class="card-header">
                <h2 class="title">Enter the <span class="accent">Battle Arena</span></h2>
                <p class="subtitle">Command your squad. Bid, deploy, and conquer.</p>
                <div class="status-row">
                    <span class="pill pill-auction">Live Auction Ready</span>
                    <span class="pill pill-combat">Combat Queue Active</span>
                </div>
            </div>
            <div class="card-body">

                @if ($errors->any())
                    <div class="error-box">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="field">
                        <label for="email">War Council Email</label>
                        <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}">
                    </div>

                    <div class="field">
                        <label for="password">Battle Key</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required>
                    </div>

                    <div class="options">
                        <div class="remember">
                            <input id="remember" name="remember" type="checkbox">
                            <label for="remember" style="margin:0;">Remember commander</label>
                        </div>

                        <a href="#" class="forgot">Forgot battle key?</a>
                    </div>

                    <button type="submit" class="btn">Launch Into Battle</button>

                    <div class="footer-link">
                        New guild? <a href="{{ route('register') }}">Create your team</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

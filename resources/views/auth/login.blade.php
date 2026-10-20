<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | {{ config('app.name', 'Laravel') }} Admin</title>

    <style>
        :root {
            --bg: #07100d;
            --panel: rgba(255, 255, 255, 0.04);
            --line: rgba(255, 255, 255, 0.1);
            --text: #e9f3ee;
            --muted: #8fa89b;
            --accent: #2ee59d;
            --danger: #ff7b7b;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 32px 20px;
            color: var(--text);
            font-family: 'Segoe UI', system-ui, -apple-system, Tahoma, sans-serif;
            background:
                radial-gradient(900px 500px at 85% 10%, rgba(46, 229, 157, 0.14), transparent 60%),
                radial-gradient(700px 400px at 5% 95%, rgba(15, 157, 102, 0.12), transparent 60%),
                var(--bg);
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(var(--line) 1px, transparent 1px),
                linear-gradient(90deg, var(--line) 1px, transparent 1px);
            background-size: 48px 48px;
            mask-image: radial-gradient(ellipse at center, #000 20%, transparent 75%);
            -webkit-mask-image: radial-gradient(ellipse at center, #000 20%, transparent 75%);
            opacity: 0.5;
            pointer-events: none;
        }

        .card {
            position: relative;
            width: 100%;
            max-width: 430px;
            padding: 40px 34px 34px;
            border: 1px solid var(--line);
            border-radius: 26px;
            background: linear-gradient(160deg, rgba(255, 255, 255, .07), rgba(255, 255, 255, .02));
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .logo {
            width: 60px;
            height: 60px;
            margin: 0 auto 18px;
            display: grid;
            place-items: center;
            border-radius: 18px;
            border: 1px solid rgba(46, 229, 157, 0.35);
            background: rgba(46, 229, 157, 0.08);
            box-shadow: 0 0 30px rgba(46, 229, 157, 0.15);
        }

        h1 {
            margin: 0 0 6px;
            text-align: center;
            font-size: 1.7rem;
            font-weight: 700;
            letter-spacing: -0.01em;
        }

        .sub {
            margin: 0 0 28px;
            text-align: center;
            color: var(--muted);
            font-size: 0.95rem;
        }

        .alert {
            margin-bottom: 18px;
            padding: 12px 14px;
            border-radius: 12px;
            font-size: 0.9rem;
            color: var(--accent);
            background: rgba(46, 229, 157, 0.08);
            border: 1px solid rgba(46, 229, 157, 0.25);
        }

        .field {
            margin-bottom: 18px;
        }

        label.lbl {
            display: block;
            margin-bottom: 7px;
            font-size: 0.85rem;
            color: var(--muted);
        }

        .input {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 14px;
            border: 1px solid var(--line);
            border-radius: 12px;
            background: var(--panel);
            transition: border-color .2s, box-shadow .2s;
        }

        .input:focus-within {
            border-color: rgba(46, 229, 157, 0.6);
            box-shadow: 0 0 0 4px rgba(46, 229, 157, 0.1);
        }

        .input.has-error {
            border-color: rgba(255, 123, 123, 0.6);
        }

        .input svg {
            flex: none;
            color: var(--muted);
        }

        .input input {
            flex: 1;
            min-width: 0;
            padding: 14px 0;
            border: 0;
            outline: 0;
            background: transparent;
            color: var(--text);
            font-size: 1rem;
            font-family: inherit;
        }

        .input input::placeholder {
            color: rgba(143, 168, 155, 0.6);
        }

        .toggle {
            border: 0;
            padding: 4px;
            background: none;
            color: var(--muted);
            cursor: pointer;
            display: grid;
        }

        .toggle:hover {
            color: var(--text);
        }

        .err {
            margin-top: 6px;
            font-size: 0.82rem;
            color: var(--danger);
        }

        .row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 4px 0 24px;
            font-size: 0.88rem;
        }

        .check {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--muted);
            cursor: pointer;
        }

        .check input {
            accent-color: var(--accent);
            width: 16px;
            height: 16px;
        }

        .row a {
            color: var(--muted);
            text-decoration: none;
            transition: color .2s;
        }

        .row a:hover {
            color: var(--accent);
        }

        .btn {
            width: 100%;
            padding: 14px;
            border: 0;
            border-radius: 12px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
            font-family: inherit;
            color: #04130c;
            background: linear-gradient(135deg, var(--accent), #6af0bb);
            box-shadow: 0 8px 24px rgba(46, 229, 157, 0.25);
            transition: transform .2s, box-shadow .2s;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(46, 229, 157, 0.38);
        }

        footer {
            position: absolute;
            bottom: 18px;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 0.82rem;
            color: var(--muted);
        }

        @media (max-width: 480px) {
            .card {
                padding: 32px 22px 26px;
            }
        }
    </style>
</head>

<body>
    <main class="card">
        <div class="logo" aria-hidden="true">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="#2ee59d" stroke-width="1.6"
                stroke-linecap="round" stroke-linejoin="round">
                <path
                    d="M4 8V6a2 2 0 0 1 2-2h2M16 4h2a2 2 0 0 1 2 2v2M20 16v2a2 2 0 0 1-2 2h-2M8 20H6a2 2 0 0 1-2-2v-2" />
                <path d="M9 10v1M15 10v1M12 10v4h-1M9 16c1.5 1.2 4.5 1.2 6 0" />
            </svg>
        </div>

        <h1>Welcome back</h1>
        <p class="sub">Sign in to {{ config('app.name', 'Laravel') }} Admin</p>

        @if (session('status'))
            <div class="alert">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="field">
                <label class="lbl" for="email">Email address</label>
                <div class="input @error('email') has-error @enderror">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="5" width="18" height="14" rx="2" />
                        <path d="m3 7 9 6 9-6" />
                    </svg>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                        autocomplete="username" placeholder="you@example.com">
                </div>
                @error('email')
                    <div class="err">{{ $message }}</div>
                @enderror
            </div>

            <div class="field">
                <label class="lbl" for="password">Password</label>
                <div class="input @error('password') has-error @enderror">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="4" y="11" width="16" height="9" rx="2" />
                        <path d="M8 11V8a4 4 0 0 1 8 0v3" />
                    </svg>
                    <input type="password" id="password" name="password" required autocomplete="current-password"
                        placeholder="Enter your password">
                    <button type="button" class="toggle" id="togglePassword" aria-label="Show password">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </button>
                </div>
                @error('password')
                    <div class="err">{{ $message }}</div>
                @enderror
            </div>

            <div class="row">
                <label class="check">
                    <input type="checkbox" id="remember_me" name="remember"> Remember me
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}">Forgot password?</a>
                @endif
            </div>

            <button type="submit" class="btn">Sign in</button>
        </form>
    </main>

    <footer>
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </footer>

    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            var input = document.getElementById('password');
            input.type = input.type === 'password' ? 'text' : 'password';
        });
    </script>
</body>

</html>

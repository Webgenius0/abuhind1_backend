<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }} | Admin</title>

    <style>
        :root {
            --bg: #07100d;
            --panel: rgba(255, 255, 255, 0.04);
            --line: rgba(255, 255, 255, 0.09);
            --text: #e9f3ee;
            --muted: #8fa89b;
            --accent: #2ee59d;
            --accent-dark: #0f9d66;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 32px 20px 72px;
            color: var(--text);
            font-family: 'Segoe UI', system-ui, -apple-system, Tahoma, sans-serif;
            background:
                radial-gradient(900px 500px at 85% 10%, rgba(46, 229, 157, 0.14), transparent 60%),
                radial-gradient(700px 400px at 5% 95%, rgba(15, 157, 102, 0.12), transparent 60%),
                var(--bg);
            position: relative;
            overflow-x: hidden;
        }

        /* faint grid */
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

        .wrap {
            position: relative;
            width: 100%;
            max-width: 980px;
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 48px;
            align-items: center;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border: 1px solid var(--line);
            background: var(--panel);
            border-radius: 999px;
            font-size: 0.8rem;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .badge i {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent);
            box-shadow: 0 0 10px var(--accent);
        }

        h1 {
            margin: 22px 0 14px;
            font-size: clamp(2.2rem, 5vw, 3.6rem);
            line-height: 1.05;
            letter-spacing: -0.02em;
            font-weight: 700;
        }

        h1 span {
            background: linear-gradient(90deg, var(--accent), #8af3c8);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .lead {
            margin: 0 0 30px;
            max-width: 440px;
            font-size: 1.05rem;
            line-height: 1.65;
            color: var(--muted);
        }

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 13px 26px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.98rem;
            text-decoration: none;
            transition: transform .2s, box-shadow .2s, background .2s;
        }

        .btn-primary {
            color: #04130c;
            background: linear-gradient(135deg, var(--accent), #6af0bb);
            box-shadow: 0 8px 24px rgba(46, 229, 157, 0.25);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 30px rgba(46, 229, 157, 0.38);
        }

        .features {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 34px;
        }

        .chip {
            padding: 8px 14px;
            border-radius: 10px;
            border: 1px solid var(--line);
            background: var(--panel);
            font-size: 0.85rem;
            color: var(--muted);
        }

        .chip b {
            color: var(--text);
            font-weight: 600;
        }

        /* face scan visual */
        .scan {
            position: relative;
            justify-self: center;
            width: min(100%, 340px);
            padding: 20px;
            border: 1px solid var(--line);
            border-radius: 28px;
            background: linear-gradient(160deg, rgba(255, 255, 255, .06), rgba(255, 255, 255, .02));
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.45);
        }

        .scan svg {
            display: block;
            width: 100%;
            height: auto;
        }

        .scan-line {
            animation: sweep 3.6s ease-in-out infinite;
        }

        @keyframes sweep {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(330px);
            }
        }

        .dot {
            animation: pulse 2.4s ease-in-out infinite;
        }

        .dot:nth-child(2n) {
            animation-delay: .6s;
        }

        .dot:nth-child(3n) {
            animation-delay: 1.2s;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: .45;
            }

            50% {
                opacity: 1;
            }
        }

        .readout {
            display: flex;
            justify-content: space-between;
            margin-top: 14px;
            font-size: 0.78rem;
            letter-spacing: 0.06em;
            color: var(--muted);
            font-variant-numeric: tabular-nums;
        }

        .readout b {
            color: var(--accent);
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

        @media (max-width: 820px) {
            .wrap {
                grid-template-columns: 1fr;
                gap: 36px;
                text-align: center;
            }

            .lead {
                margin-left: auto;
                margin-right: auto;
            }

            .actions,
            .features {
                justify-content: center;
            }

            .scan {
                order: -1;
                width: min(100%, 260px);
            }

            @keyframes sweep {

                0%,
                100% {
                    transform: translateY(0);
                }

                50% {
                    transform: translateY(330px);
                }
            }
        }

        @media (prefers-reduced-motion: reduce) {

            .scan-line,
            .dot {
                animation: none;
            }
        }
    </style>
</head>

<body>
    <main class="wrap">
        <section>
            <span class="badge"><i></i> Admin Panel</span>

            <h1>{{ config('app.name', 'Laravel') }}<br><span>Control Center</span></h1>

            <p class="lead">
                Manage face scans, premium activation codes, users and app content
                from one secure place.
            </p>

            <div class="actions">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('admin/dashboard') }}" class="btn btn-primary">Open Dashboard &rarr;</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">Sign in to Admin &rarr;</a>
                    @endauth
                @endif
            </div>

            <div class="features">
                <span class="chip"><b>Face Scans</b> &middot; AI analysis</span>
                <span class="chip"><b>Premium</b> &middot; activation codes</span>
                <span class="chip"><b>Users</b> &middot; content</span>
            </div>
        </section>

        <aside class="scan" aria-hidden="true">
            <svg viewBox="0 0 320 400" fill="none">
                <defs>
                    <clipPath id="faceClip">
                        <path
                            d="M160 30 C95 30 62 85 62 160 C62 250 100 330 160 372 C220 330 258 250 258 160 C258 85 225 30 160 30 Z" />
                    </clipPath>
                    <linearGradient id="beam" x1="0" y1="0" x2="0" y2="1">
                        <stop offset="0" stop-color="#2ee59d" stop-opacity="0" />
                        <stop offset="1" stop-color="#2ee59d" stop-opacity=".55" />
                    </linearGradient>
                </defs>

                <!-- face outline -->
                <path
                    d="M160 30 C95 30 62 85 62 160 C62 250 100 330 160 372 C220 330 258 250 258 160 C258 85 225 30 160 30 Z"
                    stroke="#2ee59d" stroke-opacity=".7" stroke-width="1.5" />

                <!-- mesh -->
                <g stroke="#2ee59d" stroke-opacity=".28" stroke-width="1">
                    <path
                        d="M118 165 L202 165 M118 165 L160 235 L202 165 M118 165 L100 250 L160 305 L220 250 L202 165 M100 250 L160 235 L220 250 M125 290 L160 305 L195 290 M160 100 L118 165 M160 100 L202 165 M80 160 L118 165 M240 160 L202 165" />
                </g>

                <!-- features -->
                <g stroke="#e9f3ee" stroke-opacity=".75" stroke-width="1.6" stroke-linecap="round">
                    <path d="M100 148 Q118 138 136 148" />
                    <path d="M184 148 Q202 138 220 148" />
                    <path d="M160 170 L160 232 Q150 240 160 242 Q170 240 160 232" />
                    <path d="M125 290 Q160 312 195 290" />
                </g>
                <ellipse cx="118" cy="165" rx="11" ry="5" stroke="#e9f3ee"
                    stroke-opacity=".75" stroke-width="1.6" />
                <ellipse cx="202" cy="165" rx="11" ry="5" stroke="#e9f3ee"
                    stroke-opacity=".75" stroke-width="1.6" />

                <!-- landmark dots -->
                <g fill="#2ee59d">
                    <circle class="dot" cx="118" cy="165" r="3" />
                    <circle class="dot" cx="202" cy="165" r="3" />
                    <circle class="dot" cx="160" cy="100" r="3" />
                    <circle class="dot" cx="160" cy="235" r="3" />
                    <circle class="dot" cx="100" cy="250" r="3" />
                    <circle class="dot" cx="220" cy="250" r="3" />
                    <circle class="dot" cx="160" cy="305" r="3" />
                    <circle class="dot" cx="125" cy="290" r="3" />
                    <circle class="dot" cx="195" cy="290" r="3" />
                    <circle class="dot" cx="80" cy="160" r="3" />
                    <circle class="dot" cx="240" cy="160" r="3" />
                </g>

                <!-- scan beam -->
                <g clip-path="url(#faceClip)">
                    <g class="scan-line">
                        <rect x="50" y="30" width="220" height="40" fill="url(#beam)" />
                        <rect x="50" y="68" width="220" height="2" fill="#2ee59d" />
                    </g>
                </g>
            </svg>

            <div class="readout">
                <span>ANALYZING&hellip;</span>
                <span>HARMONY <b>8.4</b></span>
            </div>
        </aside>
    </main>

    <footer>
        &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
    </footer>
</body>

</html>

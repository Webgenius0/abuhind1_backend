<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: sans-serif;
            text-align: center;
            padding: 40px;
        }

        .logo {
            height: 48px;
            margin-bottom: 12px;
        }

        .app-name {
            font-size: 18px;
            font-weight: bold;
            color: #222;
        }

        .date {
            font-size: 12px;
            color: #888;
            margin-bottom: 20px;
        }

        .score {
            font-size: 48px;
            font-weight: bold;
            margin: 20px 0;
        }

        .metrics {
            font-size: 14px;
            color: #444;
        }
    </style>
</head>

<body>
    <img class="logo" src="{{ public_path('images/logo.png') }}" alt="Logo">
    <div class="app-name">{{ config('app.name') }}</div>
    <div class="date">{{ $card->created_at->format('M d, Y H:i') }}</div>

    <h2>Face Analysis Card</h2>
    <div style="display: flex; justify-content: center; gap: 20px; margin: 20px 0;">
        <div>
            <img src="{{ Storage::disk('public')->path($scan->front_image_path) }}"
                style="width: 220px; height: 220px; object-fit: cover; border-radius: 8px;">
            <p style="font-size: 12px; color: #888;">Front</p>
        </div>

        @if ($scan->side_image_path)
            <div>
                <img src="{{ Storage::disk('public')->path($scan->side_image_path) }}"
                    style="width: 220px; height: 220px; object-fit: cover; border-radius: 8px;">
                <p style="font-size: 12px; color: #888;">Side</p>
            </div>
        @endif
    </div>
    <div class="score">{{ $card->overall }}/10</div>
    <p class="metrics">
        Harmony: {{ $card->harmony }} &nbsp;|&nbsp;
        Features: {{ $card->features }} &nbsp;|&nbsp;
        Angularity: {{ $card->angularity }} &nbsp;|&nbsp;
        Dimorphism: {{ $card->dimorphism }}
    </p>
</body>

</html>

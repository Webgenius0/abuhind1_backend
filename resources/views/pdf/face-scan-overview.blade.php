<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: sans-serif;
            padding: 40px;
            color: #222;
        }

        h2 {
            text-align: center;
            margin-bottom: 4px;
        }

        .date {
            text-align: center;
            font-size: 12px;
            color: #888;
            margin-bottom: 24px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }

        table td {
            padding: 6px 10px;
            border-bottom: 1px solid #eee;
            font-size: 13px;
        }

        table td:first-child {
            font-weight: bold;
            width: 45%;
        }

        h3 {
            font-size: 15px;
            margin-bottom: 8px;
        }

        ul {
            font-size: 13px;
            padding-left: 18px;
        }

        .summary {
            font-size: 13px;
            line-height: 1.5;
        }
    </style>
</head>

<body>
    <h2>{{ config('app.name') }} — Face Analysis Overview</h2>
    <div class="date">{{ $scan->created_at->format('M d, Y H:i') }}</div>

    <table>
        <tr>
            <td>Harmony Score</td>
            <td>{{ $scan->harmony_score }}</td>
        </tr>
        <tr>
            <td>Symmetry</td>
            <td>{{ $scan->symmetry }}</td>
        </tr>
        <tr>
            <td>Jawline</td>
            <td>{{ $scan->jawline }}</td>
        </tr>
        <tr>
            <td>Cheekbones</td>
            <td>{{ $scan->cheekbones }}</td>
        </tr>
        <tr>
            <td>Nose</td>
            <td>{{ $scan->nose }}</td>
        </tr>
        <tr>
            <td>Skin Quality</td>
            <td>{{ $scan->skin_quality }}</td>
        </tr>
        <tr>
            <td>Angularity</td>
            <td>{{ $scan->angularity }}</td>
        </tr>
        <tr>
            <td>Dimorphism</td>
            <td>{{ $scan->dimorphism }}</td>
        </tr>
        <tr>
            <td>Facial Width/Height Ratio</td>
            <td>{{ $scan->facial_width_height_ratio }}</td>
        </tr>
    </table>

    @if (!empty($scan->key_strengths))
        <h3>Key Strengths</h3>
        <ul>
            @foreach ($scan->key_strengths as $strength)
                <li>{{ $strength['label'] ?? '' }}: {{ $strength['score'] ?? '' }}</li>
            @endforeach
        </ul>
    @endif

    @if (!empty($scan->regions))
        <h3>Region Breakdown</h3>
        <ul>
            @foreach ($scan->regions as $region)
                <li>{{ $region['name'] ?? '' }}: {{ $region['score'] ?? '' }} — {{ $region['note'] ?? '' }}</li>
            @endforeach
        </ul>
    @endif

    @if ($scan->summary)
        <h3>Summary</h3>
        <p class="summary">{{ $scan->summary }}</p>
    @endif
</body>

</html>

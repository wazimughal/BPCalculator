<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Blood Pressure Category Calculator</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 700px;
            margin: 40px auto;
        }

        .error {
            color: #b91c1c;
        }

        .result {
            margin-top: 20px;
        }

        label {
            display: block;
            margin-top: 10px;
        }

        input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
        }

        button {
            margin-top: 15px;
            padding: 8px 16px;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            font-size: 14px;
        }

        th {
            background-color: #f3f4f6;
            text-align: left;
        }

        .pagination {
            margin-top: 10px;
            display: flex;
            justify-content: space-between;
        }

        .pagination a {
            text-decoration: none;
            padding: 6px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 13px;
        }
    </style>
</head>

<body>
    <h1>Blood Pressure Category Calculator</h1>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="error">
            <strong>Please fix the following problems:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('bp.calculate') }}">
        @csrf

        <label for="systolic">Systolic (70-190 mmHg)</label>
        <input type="number" name="systolic" id="systolic" min="70" max="190"
            value="{{ old('systolic', $systolic) }}" required>

        <label for="diastolic">Diastolic (40-100 mmHg)</label>
        <input type="number" name="diastolic" id="diastolic" min="40" max="100"
            value="{{ old('diastolic', $diastolic) }}" required>

        <button type="submit">Calculate Category</button>
    </form>

    {{-- Colored result box --}}
    @if ($category)
        <div class="result"
            style="background-color: {{ $color }}; color: white; padding: 15px; border-radius: 8px;">
            <h2>Result</h2>
            <p>
                Reading: <strong>{{ $systolic }} / {{ $diastolic }}</strong> mmHg
            </p>
            <p style="font-size: 20px;">
                Category: <strong>{{ $category }}</strong>
            </p>

            @if (!empty($advice))
                <p style="margin-top: 10px; font-size: 14px;">
                    <strong>Advice:</strong> {{ $advice }}
                </p>
            @endif
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <img src="{{ asset('images/bp-graph.jpg') }}" alt="Blood Pressure Chart"
                style="max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 8px;">
        </div>
    @endif

    @if (true)
        <!-- ($category)-->
        <!--<div style="text-align: center; margin-top: 20px;">
            <img src="{{ asset('images/bp-graph.jpg') }}" alt="Blood Pressure Chart"
                style="max-width: 100%; height: auto; border: 1px solid #ddd; border-radius: 8px;">
        </div>-->
    @endif

    {{-- Visible telemetry: recent calculations with pagination --}}
    @if (!empty($historyItems))
        <h2>Recent Calculations (Telemetry)</h2>
        <table>
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Systolic</th>
                    <th>Diastolic</th>
                    <th>Category</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($historyItems as $row)
                    <tr>
                        <td>{{ $row['timestamp'] }}</td>
                        <td>{{ $row['systolic'] }}</td>
                        <td>{{ $row['diastolic'] }}</td>
                        <td>{{ $row['category'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pagination">
            <div>
                @if ($hasPrevious)
                    <a href="{{ route('bp.form', ['page' => $page - 1]) }}">← Previous</a>
                @endif
            </div>
            <div>
                @if ($hasNext)
                    <a href="{{ route('bp.form', ['page' => $page + 1]) }}">Next →</a>
                @endif
            </div>
        </div>
    @endif
</body>

</html>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Blood Pressure Category Calculator</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Very simple styling just to make it readable --}}
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 40px auto;
        }

        .error {
            color: #b91c1c;
        }

        .result {
            margin-top: 20px;
            padding: 10px;
            border: 1px solid #ddd;
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

        <label for="systolic">Systolic (70–190 mmHg)</label>
        <input type="number" name="systolic" id="systolic" min="70" max="190"
            value="{{ old('systolic', $systolic ?? '') }}" required>

        <label for="diastolic">Diastolic (40–100 mmHg)</label>
        <input type="number" name="diastolic" id="diastolic" min="40" max="100"
            value="{{ old('diastolic', $diastolic ?? '') }}" required>

        <button type="submit">Calculate Category</button>
    </form>

    @isset($category)
        <div class="result">
            <h2>Result</h2>
            <p>
                Reading: <strong>{{ $systolic }} / {{ $diastolic }}</strong> mmHg
            </p>
            <p>
                Category: <strong>{{ $category }}</strong>
            </p>
        </div>
    @endisset
</body>

</html>

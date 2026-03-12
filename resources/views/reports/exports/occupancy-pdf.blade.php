<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Occupancy Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 20px; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f3f4f6; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Occupancy Snapshot – {{ $tenant->name }}</h1>
    <p>Last {{ $days }} days</p>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Units Total</th>
                <th>Units Occupied</th>
                <th>Occupancy Rate</th>
                <th>Rent Roll</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    <td>{{ \Illuminate\Support\Carbon::parse($row['date'])->format('Y-m-d') }}</td>
                    <td>{{ $row['units_total'] }}</td>
                    <td>{{ $row['units_occupied'] }}</td>
                    <td>{{ number_format($row['occupancy_rate'], 2) }}%</td>
                    <td>{{ number_format(($row['rent_roll_cents'] ?? 0)/100, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

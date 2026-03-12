<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Pipeline Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 20px; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background: #f3f4f6; font-weight: bold; }
    </style>
</head>
<body>
    <h1>Pipeline Snapshot – {{ $tenant->name }}</h1>
    <p>Last {{ $days }} days</p>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>New Leads</th>
                <th>In Progress</th>
                <th>Visited</th>
                <th>Viewings (Scheduled/Completed)</th>
                <th>Leases Started</th>
                <th>Leases Active</th>
                <th>Lead→Viewing %</th>
                <th>Lead→Lease %</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                <tr>
                    <td>{{ \Illuminate\Support\Carbon::parse($row['date'])->format('Y-m-d') }}</td>
                    <td>{{ $row['leads_new'] }}</td>
                    <td>{{ $row['leads_in_progress'] }}</td>
                    <td>{{ $row['leads_visited'] }}</td>
                    <td>{{ $row['viewings_scheduled'] }} / {{ $row['viewings_completed'] }}</td>
                    <td>{{ $row['leases_started'] }}</td>
                    <td>{{ $row['leases_active'] }}</td>
                    <td>{{ number_format($row['lead_to_viewing_rate'], 2) }}%</td>
                    <td>{{ number_format($row['lead_to_lease_rate'], 2) }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>

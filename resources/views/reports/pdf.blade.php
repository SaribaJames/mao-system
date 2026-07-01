<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>MAO Guinobatan — Summary Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #2D7A2D; padding-bottom: 10px; }
        .header h1 { color: #2D7A2D; font-size: 18px; margin: 0; }
        .header p { color: #666; margin: 3px 0; font-size: 11px; }
        .section { margin-bottom: 20px; }
        .section-title { background: #2D7A2D; color: white; padding: 5px 10px; font-size: 13px; font-weight: bold; margin-bottom: 8px; }
        .stats-grid { display: table; width: 100%; border-collapse: collapse; }
        .stat-box { display: table-cell; width: 25%; border: 1px solid #ddd; padding: 10px; text-align: center; }
        .stat-number { font-size: 22px; font-weight: bold; color: #2D7A2D; }
        .stat-label { font-size: 10px; color: #666; margin-top: 2px; }
        table { width: 100%; border-collapse: collapse; margin-top: 5px; }
        th { background: #f0f0f0; padding: 6px 8px; text-align: left; font-size: 11px; border: 1px solid #ddd; }
        td { padding: 5px 8px; border: 1px solid #ddd; font-size: 11px; }
        tr:nth-child(even) { background: #f9f9f9; }
        .footer { text-align: center; margin-top: 30px; font-size: 10px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
        .badge { padding: 2px 6px; border-radius: 10px; font-size: 10px; }
        .badge-green { background: #d4edda; color: #155724; }
        .badge-yellow { background: #fff3cd; color: #856404; }
        .badge-red { background: #f8d7da; color: #721c24; }
        .badge-blue { background: #cce5ff; color: #004085; }
    </style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <h1>Municipal Agriculture Office — Guinobatan, Albay</h1>
    <p>Digital Farmer Records and Service Management System</p>
    <p>Summary Report | Generated: {{ date('F d, Y h:i A') }}</p>
</div>

{{-- Overall Stats --}}
<div class="section">
    <div class="section-title">OVERVIEW</div>
    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-number">{{ $totalFarmers }}</div>
            <div class="stat-label">Total Farmers<br>{{ $activeFarmers }} Active</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">{{ $totalRequests }}</div>
            <div class="stat-label">Total Requests<br>{{ $pendingRequests }} Pending</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">{{ $totalServices }}</div>
            <div class="stat-label">Total Services<br>{{ $completedServices }} Completed</div>
        </div>
        <div class="stat-box">
            <div class="stat-number">{{ number_format($remainingStock, 0) }}</div>
            <div class="stat-label">Available Stocks<br>{{ number_format($releasedStock, 0) }} Released</div>
        </div>
    </div>
</div>

{{-- Farmers by Barangay --}}
<div class="section">
    <div class="section-title">FARMERS BY BARANGAY (Top 10)</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Barangay</th>
                <th>No. of Farmers</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
            @foreach($farmersByBarangay as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->barangay?->name ?? 'Unknown' }}</td>
                <td>{{ $item->total }}</td>
                <td>{{ $totalFarmers > 0 ? number_format(($item->total / $totalFarmers) * 100, 1) : 0 }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Farmers by Sex --}}
<div class="section">
    <div class="section-title">FARMERS BY SEX</div>
    <table>
        <thead>
            <tr><th>Sex</th><th>Count</th><th>Percentage</th></tr>
        </thead>
        <tbody>
            @foreach($farmersBySex as $item)
            <tr>
                <td>{{ ucfirst($item->sex) }}</td>
                <td>{{ $item->total }}</td>
                <td>{{ $totalFarmers > 0 ? number_format(($item->total / $totalFarmers) * 100, 1) : 0 }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Farmers by Livelihood --}}
<div class="section">
    <div class="section-title">FARMERS BY MAIN LIVELIHOOD</div>
    <table>
        <thead>
            <tr><th>Livelihood</th><th>Count</th><th>Percentage</th></tr>
        </thead>
        <tbody>
            @foreach($farmersByLivelihood as $item)
            <tr>
                <td>{{ ucfirst(str_replace('_', ' ', $item->main_livelihood)) }}</td>
                <td>{{ $item->total }}</td>
                <td>{{ $totalFarmers > 0 ? number_format(($item->total / $totalFarmers) * 100, 1) : 0 }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- Request Status --}}
<div class="section">
    <div class="section-title">REQUESTS SUMMARY</div>
    <table>
        <thead>
            <tr><th>Status</th><th>Count</th><th>Percentage</th></tr>
        </thead>
        <tbody>
            <tr><td>Pending</td><td>{{ $pendingRequests }}</td><td>{{ $totalRequests > 0 ? number_format(($pendingRequests / $totalRequests) * 100, 1) : 0 }}%</td></tr>
            <tr><td>Approved</td><td>{{ $approvedRequests }}</td><td>{{ $totalRequests > 0 ? number_format(($approvedRequests / $totalRequests) * 100, 1) : 0 }}%</td></tr>
            <tr><td>Completed</td><td>{{ $completedRequests }}</td><td>{{ $totalRequests > 0 ? number_format(($completedRequests / $totalRequests) * 100, 1) : 0 }}%</td></tr>
            <tr><td>Rejected</td><td>{{ $rejectedRequests }}</td><td>{{ $totalRequests > 0 ? number_format(($rejectedRequests / $totalRequests) * 100, 1) : 0 }}%</td></tr>
        </tbody>
    </table>
</div>

{{-- Services by Type --}}
<div class="section">
    <div class="section-title">SERVICES BY TYPE</div>
    <table>
        <thead>
            <tr><th>Service Type</th><th>Count</th><th>Percentage</th></tr>
        </thead>
        <tbody>
            @foreach($servicesByType as $item)
            <tr>
                <td>{{ ucfirst(str_replace('_', ' ', $item->service_type)) }}</td>
                <td>{{ $item->total }}</td>
                <td>{{ $totalServices > 0 ? number_format(($item->total / $totalServices) * 100, 1) : 0 }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="footer">
    <p>Municipal Agriculture Office — Guinobatan, Albay | Digital Farmer Records and Service Management System</p>
    <p>This report was automatically generated by the system on {{ date('F d, Y') }}</p>
</div>

</body>
</html>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>MAO Guinobatan — Service Records Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; color: #333; margin: 0; padding: 15px; }
        .header { text-align: center; margin-bottom: 15px; border-bottom: 2px solid #2D7A2D; padding-bottom: 8px; }
        .header h1 { color: #2D7A2D; font-size: 16px; margin: 0; }
        .header p { color: #666; margin: 2px 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #2D7A2D; color: white; padding: 5px 6px; text-align: left; font-size: 10px; }
        td { padding: 4px 6px; border-bottom: 1px solid #eee; font-size: 10px; }
        tr:nth-child(even) { background: #f9f9f9; }
        .footer { text-align: center; margin-top: 20px; font-size: 9px; color: #999; }
    </style>
</head>
<body>

<div class="header">
    <h1>Municipal Agriculture Office — Guinobatan, Albay</h1>
    <p>Service Records Report | Generated: {{ date('F d, Y h:i A') }}</p>
    <p>Total Services: {{ $services->count() }}</p>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Service No.</th>
            <th>Farmer</th>
            <th>Barangay</th>
            <th>Service Type</th>
            <th>Items Provided</th>
            <th>Quantity</th>
            <th>Processed By</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($services as $index => $svc)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $svc->service_number }}</td>
            <td>{{ $svc->farmer->first_name }} {{ $svc->farmer->surname }}</td>
            <td>{{ $svc->farmer->barangay?->name ?? '—' }}</td>
            <td>{{ ucfirst(str_replace('_', ' ', $svc->service_type)) }}</td>
            <td>{{ $svc->items_provided ?? '—' }}</td>
            <td>{{ $svc->quantity ? $svc->quantity . ' ' . $svc->quantity_unit : '—' }}</td>
            <td>{{ $svc->processedBy?->name ?? '—' }}</td>
            <td>{{ ucfirst($svc->status) }}</td>
            <td>{{ $svc->service_date->format('M d, Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    <p>Municipal Agriculture Office — Guinobatan, Albay | Generated on {{ date('F d, Y') }}</p>
</div>

</body>
</html>
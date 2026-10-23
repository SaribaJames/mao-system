<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>MAO Guinobatan — Requests Report</title>
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
    <p>Requests Management Report | Generated: {{ date('F d, Y h:i A') }}</p>
    <p>Total Requests: {{ $requests->count() }}</p>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Request No.</th>
            <th>Farmer</th>
            <th>Barangay</th>
            <th>Request Type</th>
            <th>Item/Service</th>
            <th>Quantity</th>
            <th>Status</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($requests as $index => $req)
        @php
            $labels = [
                'seeds_distribution'   => 'Seeds Distribution',
                'fertilizer_request'   => 'Fertilizer Request',
                'pesticide_request'    => 'Pesticide Request',
                'equipment_request'    => 'Equipment Request',
                'training_seminar'     => 'Training/Seminar',
                'technical_assistance' => 'Technical Assistance',
                'financial_assistance' => 'Financial Assistance',
                'others'               => 'Others',
            ];
        @endphp
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $req->request_number }}</td>
            <td>{{ $req->farmer->first_name }} {{ $req->farmer->surname }}</td>
            <td>{{ $req->farmer->barangay?->name ?? '—' }}</td>
            <td>{{ $labels[$req->request_type] ?? $req->request_type }}</td>
            <td>{{ $req->item_service }}</td>
            <td>{{ $req->quantity ? $req->quantity . ' ' . $req->quantity_unit : '—' }}</td>
            <td>{{ ucfirst($req->status) }}</td>
            <td>{{ \Carbon\Carbon::parse($req->created_at)->format('M d, Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    <p>Municipal Agriculture Office — Guinobatan, Albay | Generated on {{ date('F d, Y') }}</p>
</div>

</body>
</html>
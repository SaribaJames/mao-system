<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>MAO Guinobatan — Farmers List</title>
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
    <p>Farmers Registry List | Generated: {{ date('F d, Y h:i A') }}</p>
    <p>Total Farmers: {{ $farmers->count() }}</p>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Reference No.</th>
            <th>Full Name</th>
            <th>Sex</th>
            <th>Barangay</th>
            <th>Contact</th>
            <th>Livelihood</th>
            <th>Land Area</th>
            <th>Status</th>
            <th>Date Registered</th>
        </tr>
    </thead>
    <tbody>
        @foreach($farmers as $index => $farmer)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $farmer->reference_number }}</td>
            <td>{{ $farmer->first_name }} {{ $farmer->surname }}</td>
            <td>{{ ucfirst($farmer->sex) }}</td>
            <td>{{ $farmer->barangay?->name ?? '—' }}</td>
            <td>{{ $farmer->mobile_number ?? '—' }}</td>
            <td>{{ ucfirst(str_replace('_', ' ', $farmer->main_livelihood ?? '—')) }}</td>
            <td>{{ $farmer->land_area_hectares ? $farmer->land_area_hectares . ' ha' : '—' }}</td>
            <td>{{ ucfirst($farmer->status) }}</td>
            <td>{{ $farmer->created_at->format('M d, Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    <p>Municipal Agriculture Office — Guinobatan, Albay | Generated on {{ date('F d, Y') }}</p>
</div>

</body>
</html>
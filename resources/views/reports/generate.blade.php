<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>MAO Statement Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; margin: 0; padding: 25px; }

        .header { text-align: center; margin-bottom: 20px; }
        .header img { width: 60px; height: 60px; }
        .header h1 { font-size: 14px; margin: 5px 0 2px; text-transform: uppercase; }
        .header h2 { font-size: 12px; margin: 2px 0; }
        .header p { font-size: 10px; color: #666; margin: 2px 0; }
        .header .title { font-size: 13px; font-weight: bold; margin-top: 10px; text-decoration: underline; text-transform: uppercase; }

        .divider { border-top: 2px solid #2D7A2D; margin: 10px 0; }
        .thin-divider { border-top: 1px solid #ccc; margin: 8px 0; }

        .section { margin-bottom: 15px; }
        .section-title { font-size: 11px; font-weight: bold; text-transform: uppercase; color: #2D7A2D; border-bottom: 1px solid #2D7A2D; padding-bottom: 3px; margin-bottom: 8px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        th { background: #2D7A2D; color: white; padding: 5px 8px; text-align: left; font-size: 10px; }
        td { padding: 4px 8px; border-bottom: 1px solid #eee; font-size: 10px; }
        tr:nth-child(even) { background: #f9f9f9; }

        .summary-box { border: 1px solid #ddd; padding: 8px 12px; margin-bottom: 8px; border-radius: 4px; }
        .summary-row { display: table; width: 100%; margin-bottom: 4px; }
        .summary-label { display: table-cell; width: 70%; font-size: 10px; }
        .summary-value { display: table-cell; width: 30%; font-weight: bold; font-size: 10px; text-align: right; }

        .highlight { color: #2D7A2D; font-weight: bold; }
        .warning { color: #F59E0B; font-weight: bold; }
        .danger { color: #EF4444; font-weight: bold; }

        .signature-area { margin-top: 50px; }
        .signature-table { width: 100%; }
        .signature-cell { width: 33%; text-align: center; padding: 0 15px; vertical-align: bottom; }
        .signature-line { border-top: 1px solid #333; padding-top: 5px; font-size: 10px; margin-top: 40px; }
        .signature-name { font-weight: bold; font-size: 10px; }
        .signature-title { font-size: 9px; color: #666; }

        .footer { text-align: center; margin-top: 20px; font-size: 9px; color: #999; border-top: 1px solid #ddd; padding-top: 8px; }

        .note { font-size: 9px; color: #888; font-style: italic; margin-top: 5px; }
    </style>
</head>
<body>

{{-- Header --}}
<div class="header">
    <h1>Republic of the Philippines</h1>
    <h1>Province of Albay — Municipality of Guinobatan</h1>
    <h2>Municipal Agriculture Office</h2>
    <p>Poblacion, Guinobatan, Albay</p>
    <p>Email: mao.guinobatan.albay@gmail.com</p>
    <div class="divider"></div>
    <div class="title">Monthly Accomplishment Report</div>
    <p style="margin-top:5px;">For the Month of {{ $month }}</p>
    <p>Date Generated: {{ date('F d, Y') }}</p>
</div>

{{-- I. Overview --}}
<div class="section">
    <div class="section-title">I. Overview of Accomplishments</div>
    <div class="summary-box">
        <div class="summary-row">
            <div class="summary-label">Total Registered Farmers (Cumulative)</div>
            <div class="summary-value highlight">{{ $totalFarmers }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Active Farmers</div>
            <div class="summary-value highlight">{{ $activeFarmers }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Total Requests Received</div>
            <div class="summary-value">{{ $totalRequests }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Requests Completed</div>
            <div class="summary-value highlight">{{ $completedRequests }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Requests Pending</div>
            <div class="summary-value warning">{{ $pendingRequests }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Requests Rejected</div>
            <div class="summary-value danger">{{ $rejectedRequests }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Total Services Rendered</div>
            <div class="summary-value highlight">{{ $totalServices }}</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Services Completed</div>
            <div class="summary-value highlight">{{ $completedServices }}</div>
        </div>
    </div>
</div>

{{-- II. Farmer Statistics --}}
<div class="section">
    <div class="section-title">II. Farmer Statistics</div>

    <p style="font-size:10px; margin-bottom:5px;"><strong>A. Distribution by Sex</strong></p>
    <table>
        <thead>
            <tr><th>Sex</th><th>Number of Farmers</th><th>Percentage</th></tr>
        </thead>
        <tbody>
            @foreach($farmersBySex as $item)
            <tr>
                <td>{{ ucfirst($item->sex) }}</td>
                <td>{{ $item->total }}</td>
                <td>{{ $totalFarmers > 0 ? number_format(($item->total / $totalFarmers) * 100, 1) : 0 }}%</td>
            </tr>
            @endforeach
            <tr style="font-weight:bold; background:#f0f0f0;">
                <td>TOTAL</td>
                <td>{{ $totalFarmers }}</td>
                <td>100%</td>
            </tr>
        </tbody>
    </table>

    <p style="font-size:10px; margin-bottom:5px;"><strong>B. Distribution by Main Livelihood</strong></p>
    <table>
        <thead>
            <tr><th>Livelihood</th><th>Number of Farmers</th><th>Percentage</th></tr>
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

    <p style="font-size:10px; margin-bottom:5px;"><strong>C. Top Barangays by Number of Registered Farmers</strong></p>
    <table>
        <thead>
            <tr><th>#</th><th>Barangay</th><th>No. of Farmers</th><th>Percentage</th></tr>
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

{{-- III. Services Rendered --}}
<div class="section">
    <div class="section-title">III. Services Rendered</div>
    <table>
        <thead>
            <tr><th>Service Type</th><th>Number of Services</th><th>Percentage</th></tr>
        </thead>
        <tbody>
            @foreach($servicesByType as $item)
            <tr>
                <td>{{ ucfirst(str_replace('_', ' ', $item->service_type)) }}</td>
                <td>{{ $item->total }}</td>
                <td>{{ $totalServices > 0 ? number_format(($item->total / $totalServices) * 100, 1) : 0 }}%</td>
            </tr>
            @endforeach
            @if($servicesByType->isEmpty())
            <tr><td colspan="3" style="text-align:center; color:#999;">No services recorded this period.</td></tr>
            @endif
        </tbody>
    </table>
</div>

{{-- IV. Stock Inventory --}}
<div class="section">
    <div class="section-title">IV. Stock Inventory Summary</div>
    <div class="summary-box">
        <div class="summary-row">
            <div class="summary-label">Total Stocks Added</div>
            <div class="summary-value">{{ number_format($totalStock, 0) }} units</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Total Stocks Released/Distributed</div>
            <div class="summary-value highlight">{{ number_format($releasedStock, 0) }} units</div>
        </div>
        <div class="summary-row">
            <div class="summary-label">Remaining Stocks Available</div>
            <div class="summary-value highlight">{{ number_format($remainingStock, 0) }} units</div>
        </div>
    </div>

    @if($stockByCategory->count() > 0)
    <table>
        <thead>
            <tr><th>Category</th><th>Remaining Stock</th></tr>
        </thead>
        <tbody>
            @foreach($stockByCategory as $item)
            <tr>
                <td>{{ ucfirst($item->category) }}</td>
                <td>{{ number_format($item->total, 0) }} units</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

{{-- V. Remarks --}}
<div class="section">
    <div class="section-title">V. Remarks and Recommendations</div>
    <div style="border: 1px solid #ddd; padding: 10px; min-height: 60px; border-radius: 4px;">
        <p class="note">[ This section is for manual remarks and recommendations by the Municipal Agriculturist ]</p>
        <br><br><br>
    </div>
</div>

{{-- Signature Area --}}
<div class="signature-area">
    <table class="signature-table">
        <tr>
            <td class="signature-cell">
                <div class="signature-line">
                    <div class="signature-name">Prepared by:</div>
                    <br>
                    <div class="signature-name">_______________________</div>
                    <div class="signature-title">Agricultural Technician</div>
                    <div class="signature-title">MAO Guinobatan</div>
                </div>
            </td>
            <td class="signature-cell">
                <div class="signature-line">
                    <div class="signature-name">Reviewed by:</div>
                    <br>
                    <div class="signature-name">_______________________</div>
                    <div class="signature-title">Municipal Agriculturist</div>
                    <div class="signature-title">MAO Guinobatan</div>
                </div>
            </td>
            <td class="signature-cell">
                <div class="signature-line">
                    <div class="signature-name">Noted by:</div>
                    <br>
                    <div class="signature-name">_______________________</div>
                    <div class="signature-title">Municipal Mayor</div>
                    <div class="signature-title">Guinobatan, Albay</div>
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="footer">
    <p>Municipal Agriculture Office — Guinobatan, Albay | Digital Farmer Records and Service Management System</p>
    <p>This is a system-generated draft report. Subject to review and approval by the Municipal Agriculturist.</p>
    <p>Generated on {{ date('F d, Y h:i A') }}</p>
</div>

</body>
</html>
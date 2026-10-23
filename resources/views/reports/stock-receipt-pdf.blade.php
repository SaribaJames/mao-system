<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>MAO Guinobatan — Goods Received Report #{{ $transaction->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #333; margin: 0; padding: 25px; }
        .header { text-align: center; margin-bottom: 18px; border-bottom: 2px solid #2D7A2D; padding-bottom: 10px; }
        .header h1 { color: #2D7A2D; font-size: 17px; margin: 0; }
        .header h2 { color: #333; font-size: 14px; margin: 6px 0 0; letter-spacing: 1px; }
        .header p { color: #666; margin: 2px 0; font-size: 10px; }

        .meta-table { width: 100%; margin-bottom: 16px; border-collapse: collapse; }
        .meta-table td { padding: 5px 8px; font-size: 11px; vertical-align: top; }
        .meta-table td.label { color: #666; width: 150px; font-weight: bold; }

        table.items { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        table.items th { background: #2D7A2D; color: white; padding: 7px 8px; text-align: left; font-size: 11px; }
        table.items td { padding: 7px 8px; border-bottom: 1px solid #eee; font-size: 11px; }

        .notes-box { border: 1px solid #ddd; border-radius: 4px; padding: 10px; margin-bottom: 30px; min-height: 40px; font-size: 10px; color: #444; }

        .signatures { width: 100%; margin-top: 40px; border-collapse: collapse; }
        .signatures td { width: 50%; text-align: center; padding-top: 4px; }
        .sig-line { border-top: 1px solid #333; margin: 40px 20px 4px 20px; }
        .sig-name { font-weight: bold; font-size: 11px; }
        .sig-role { font-size: 9px; color: #666; }

        .footer { text-align: center; margin-top: 25px; font-size: 9px; color: #999; }
    </style>
</head>
<body>

<div class="header">
    <h1>Municipal Agriculture Office — Guinobatan, Albay</h1>
    <h2>GOODS RECEIVED REPORT</h2>
    <p>Report No. GR-{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }} | Generated: {{ date('F d, Y h:i A') }}</p>
</div>

<table class="meta-table">
    <tr>
        <td class="label">Received From:</td>
        <td>{{ $transaction->partner_name ?? '—' }}</td>
        <td class="label">Reference / DR No.:</td>
        <td>{{ $transaction->reference_number ?? '—' }}</td>
    </tr>
    <tr>
        <td class="label">Date Received:</td>
        <td>{{ $transaction->received_date ? $transaction->received_date->format('F d, Y') : $transaction->created_at->format('F d, Y') }}</td>
        <td class="label">Logged By:</td>
        <td>{{ $transaction->processedBy?->name ?? '—' }}</td>
    </tr>
</table>

<table class="items">
    <thead>
        <tr>
            <th>Item</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Unit</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>{{ $transaction->stock->item_name }}</td>
            <td>{{ ucfirst($transaction->stock->category) }}</td>
            <td>{{ number_format($transaction->quantity, 2) }}</td>
            <td>{{ $transaction->stock->unit }}</td>
        </tr>
    </tbody>
</table>

<div class="notes-box">
    <strong>Notes:</strong> {{ $transaction->notes ?: '—' }}
</div>

<p style="font-size: 10px; color: #444; margin-bottom: 30px;">
    This is to certify that the Municipal Agriculture Office — Guinobatan, Albay has received the above-listed
    resources from the partner/source named above, for distribution to qualified beneficiaries under the Office's
    assistance programs.
</p>

<table class="signatures">
    <tr>
        <td>
            <div class="sig-line"></div>
            <div class="sig-name">{{ $transaction->processedBy?->name ?? '________________________' }}</div>
            <div class="sig-role">Received By (MAO Staff)</div>
        </td>
        <td>
            <div class="sig-line"></div>
            <div class="sig-name">&nbsp;</div>
            <div class="sig-role">Authorized By (MAO Administrator)</div>
        </td>
    </tr>
</table>

<div class="footer">
    <p>Municipal Agriculture Office — Guinobatan, Albay | This document is system-generated and serves as proof of receipt.</p>
</div>

</body>
</html>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Distribution List — {{ $activity->name }}</title>
<style>
    * { box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 18px 22px; }
    .head { text-align: center; line-height: 1.35; margin-bottom: 10px; }
    .head .rep { font-size: 10px; }
    .head .office { font-size: 11px; font-weight: bold; text-transform: uppercase; }
    .title { text-align: center; font-size: 13px; font-weight: bold; text-transform: uppercase; margin: 10px 0 2px; }
    .sub { text-align: center; font-size: 10px; margin-bottom: 10px; }
    .meta { width: 100%; font-size: 10px; margin-bottom: 8px; }
    .meta td { padding: 1px 0; }
    table.list { width: 100%; border-collapse: collapse; }
    table.list th, table.list td { border: 1px solid #000; padding: 4px 5px; font-size: 9.5px; }
    table.list th { background: #eee; text-align: center; font-weight: bold; }
    td.c { text-align: center; }
    td.sig { height: 22px; }
    .totals { margin-top: 10px; font-size: 10px; }
    .sign { margin-top: 26px; width: 100%; font-size: 10px; }
    .sign td { padding-top: 26px; }
    .line { border-top: 1px solid #000; display: inline-block; min-width: 190px; padding-top: 2px; }
</style>
</head>
<body>

<div class="head">
    <div class="rep">Republic of the Philippines</div>
    <div class="rep">Province of Albay</div>
    <div class="rep">Municipality of Guinobatan</div>
    <div class="office">Office of the Municipal Agricultural Services</div>
</div>

<div class="title">Distribution / Utilization List</div>
<div class="sub">{{ $activity->program->name }}</div>

<table class="meta">
    <tr>
        <td><strong>Activity:</strong> {{ $activity->name }}</td>
        <td style="text-align:right;"><strong>Date Printed:</strong> {{ now()->format('F d, Y') }}</td>
    </tr>
</table>

<table class="list">
    <thead>
        <tr>
            <th style="width:26px;">NO.</th>
            <th>NAME OF RECIPIENT</th>
            <th>ADDRESS / BARANGAY</th>
            <th style="width:34px;">AGE</th>
            <th style="width:34px;">SEX</th>
            @foreach($stockItems as $stock)
                <th>{{ $stock->item_name }}<br><span style="font-weight:normal;">({{ $stock->unit }})</span></th>
            @endforeach
            <th style="width:150px;">SIGNATURE</th>
        </tr>
    </thead>
    <tbody>
        @forelse($activity->recipients as $i => $r)
            <tr>
                <td class="c">{{ $i + 1 }}</td>
                <td>{{ $r->farmer_name }}</td>
                <td>{{ trim(($r->address ? $r->address . ', ' : '') . ($r->barangay?->name ?? '')) ?: '—' }}</td>
                <td class="c">{{ $r->age ?: '' }}</td>
                <td class="c">{{ $r->sex ?: '' }}</td>
                @foreach($stockItems as $stock)
                    <td class="c">{{ ($r->quantities[$stock->id] ?? 0) > 0 ? $r->quantities[$stock->id] : '' }}</td>
                @endforeach
                <td class="sig"></td>
            </tr>
        @empty
            {{-- No farmers recorded yet: print blank rows so the list can be
                 filled in by hand during the actual distribution. --}}
            @for($i = 1; $i <= 20; $i++)
                <tr>
                    <td class="c">{{ $i }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    @foreach($stockItems as $stock)
                        <td></td>
                    @endforeach
                    <td class="sig"></td>
                </tr>
            @endfor
        @endforelse
    </tbody>
</table>

@if($activity->recipients->count() > 0)
    @php $totals = $activity->distributedTotals(); @endphp
    <div class="totals">
        <strong>Total Recipients:</strong> {{ $activity->recipients->count() }}
        @foreach($totals as $t)
            @if($t['qty'] > 0)
                &nbsp;&nbsp;|&nbsp;&nbsp; <strong>{{ $t['name'] }}:</strong> {{ $t['qty'] }} {{ $t['unit'] }}
            @endif
        @endforeach
    </div>
@endif

<table class="sign">
    <tr>
        <td>Prepared by:<br><br><span class="line">{{ $activity->program->assignedUser->name ?? '' }}</span><br>Program Coordinator</td>
        <td style="text-align:right;">Certified correct:<br><br><span class="line">&nbsp;</span><br>Municipal Agriculturist</td>
    </tr>
</table>

</body>
</html>

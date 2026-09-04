<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #111; }
h1 { font-size: 18px; margin-bottom: 4px; }
.subtitle { color: #666; font-size: 11px; margin-bottom: 20px; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; }
th, td { border: 1px solid #ccc; padding: 6px 8px; text-align: left; vertical-align: top; word-wrap: break-word; overflow-wrap: break-word; }
table { width: 100%; border-collapse: collapse; margin-top: 10px; table-layout: fixed; }
th:nth-child(1) { width: 12%; }
th:nth-child(2) { width: 14%; }
th:nth-child(3) { width: 16%; }
th:nth-child(4) { width: 16%; }
th:nth-child(5) { width: 14%; }
th:nth-child(6) { width: 12%; }
th { background: #f3f4f6; font-size: 10px; }
td { font-size: 8px; }
th { background: #f3f4f6; font-size: 8px; }
.overview { margin-bottom: 20px; line-height: 1.5; }
</style>
</head>
<body>

<h1>{{ $program->name }} - Annual Accomplishment and Planning Report</h1>
<p class="subtitle">Coordinator: {{ $program->coordinator_name }} &nbsp;|&nbsp; Generated: {{ now()->format('F d, Y') }}</p>

@if($program->description)
<div class="overview">
    <strong>Program Overview</strong><br>
    {{ $program->description }}
</div>
@endif

@if($chartUrl)
<div style="text-align:center; margin-bottom: 20px;">
    <img src="{{ $chartUrl }}" style="width: 400px;">
</div>
@endif

@php
    $allYears = [];
    foreach ($program->activities as $a) {
        foreach (($a->budget_breakdown ?? []) as $year => $amt) {
            $allYears[$year] = true;
        }
    }
    $allYears = array_keys($allYears);
    sort($allYears);
@endphp

<table>
    <thead>
        <tr>
            <th>Activity / Project</th>
            <th>Performance Achieved</th>
            <th>Challenges Encountered</th>
            <th>Proposed Intervention</th>
            <th>Target Performance</th>
            <th>Expenditure Item</th>
            @foreach($allYears as $year)
            <th>{{ $year }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @forelse($program->activities as $activity)
        <tr>
            <td>{{ $activity->name }}</td>
            <td>{{ $activity->performance_achieved ?: '-' }}</td>
            <td>{{ $activity->challenges_encountered ?: '-' }}</td>
            <td>{{ $activity->proposed_intervention ?: '-' }}</td>
            <td>{{ $activity->target_performance ?: '-' }}</td>
            <td>{{ $activity->expenditure_item ?: '-' }}</td>
            @foreach($allYears as $year)
            <td>{{ isset($activity->budget_breakdown[$year]) ? 'PHP ' . number_format($activity->budget_breakdown[$year], 2) : '-' }}</td>
            @endforeach
        </tr>
        @empty
        <tr><td colspan="{{ 6 + count($allYears) }}">No activities recorded.</td></tr>
        @endforelse
    </tbody>
    @if(count($allYears) > 0)
    <tfoot>
        <tr>
            <td colspan="6" style="text-align:right;"><strong>Total Budget</strong></td>
            @foreach($allYears as $year)
            @php
                $yearTotal = 0;
                foreach ($program->activities as $a) {
                    $yearTotal += floatval($a->budget_breakdown[$year] ?? 0);
                }
            @endphp
            <td><strong>PHP {{ number_format($yearTotal, 2) }}</strong></td>
            @endforeach
        </tr>
    </tfoot>
    @endif
</table>

@if(!empty($resourceTotals))
<h1 style="font-size: 14px; margin-top: 24px;">Resources Distributed</h1>
<p class="subtitle">Totals released to farmers under this program, from each activity's distribution list.</p>
<table>
    <thead>
        <tr>
            <th style="width: 30%;">Item</th>
            <th style="width: 20%;">Total Quantity</th>
            <th style="width: 50%;">Unit</th>
        </tr>
    </thead>
    <tbody>
        @foreach($resourceTotals as $resource)
            <tr>
                <td>{{ $resource['name'] }}</td>
                <td>{{ number_format($resource['qty'], 2) }}</td>
                <td>{{ $resource['unit'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@php
    $distActivities = $program->activities->filter(fn($a) => $a->recipients->count() > 0);
    $totalRecipients = $distActivities->sum(fn($a) => $a->recipients->count());
@endphp
@if($distActivities->count() > 0)
<p style="font-size: 9px; color: #666; margin-top: 6px;">
    From {{ $distActivities->count() }} activity distribution list(s) covering {{ $totalRecipients }} farmer record(s):
    {{ $distActivities->pluck('name')->join(', ') }}
</p>
@endif
@endif

</body>
</html>
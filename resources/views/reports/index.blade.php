@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Reports & Analytics</h2>
        <p class="text-gray-500 text-sm mt-1">Overview of MAO Guinobatan operations and statistics</p>
    </div>
    <div class="flex gap-2">
        {{-- Export Dropdown --}}
        <div class="relative" id="exportDropdown">
            <button onclick="document.getElementById('exportMenu').classList.toggle('hidden')"
                    class="bg-primary hover:bg-primary-dark text-white text-sm font-semibold px-4 py-2 rounded-md flex items-center gap-2 transition">
                <i class="fa-solid fa-download"></i> Export PDF
                <i class="fa-solid fa-chevron-down text-xs"></i>
            </button>
            <div id="exportMenu" class="hidden absolute right-0 mt-1 w-56 bg-white rounded-md shadow-lg border border-gray-100 z-50">
                <a href="{{ route('reports.export.pdf') }}"
                   class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-100">
                    <i class="fa-solid fa-file-pdf text-red-500"></i> Summary Report
                </a>
                <a href="{{ route('reports.export.farmers') }}"
                   class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-100">
                    <i class="fa-solid fa-file-pdf text-red-500"></i> Farmers List
                </a>
                <a href="{{ route('reports.export.requests') }}"
                   class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 border-b border-gray-100">
                    <i class="fa-solid fa-file-pdf text-red-500"></i> Requests Report
                </a>
                <a href="{{ route('reports.export.services') }}"
                   class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                    <i class="fa-solid fa-file-pdf text-red-500"></i> Service Records
                </a>
            </div>
        </div>
        {{-- Print Button --}}
        <button onclick="window.print()"
                class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-semibold px-4 py-2 rounded-md flex items-center gap-2 transition">
            <i class="fa-solid fa-print"></i> Print
        </button>
    </div>
</div>

{{-- Top Stat Cards --}}
<div class="grid grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs text-gray-500">Total Farmers</p>
            <div class="bg-green-100 p-1.5 rounded-lg">
                <i class="fa-solid fa-person text-primary text-sm"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-800">{{ $totalFarmers }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $activeFarmers }} active</p>
    </div>
    <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs text-gray-500">Total Requests</p>
            <div class="bg-blue-100 p-1.5 rounded-lg">
                <i class="fa-solid fa-file-lines text-blue-500 text-sm"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-800">{{ $totalRequests }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $pendingRequests }} pending</p>
    </div>
    <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs text-gray-500">Total Services</p>
            <div class="bg-yellow-100 p-1.5 rounded-lg">
                <i class="fa-solid fa-clipboard-list text-yellow-500 text-sm"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-800">{{ $totalServices }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ $completedServices }} completed</p>
    </div>
    <div class="bg-white rounded-lg p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-2">
            <p class="text-xs text-gray-500">Available Stocks</p>
            <div class="bg-orange-100 p-1.5 rounded-lg">
                <i class="fa-solid fa-boxes-stacked text-orange-500 text-sm"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($remainingStock, 0) }}</p>
        <p class="text-xs text-gray-400 mt-1">{{ number_format($releasedStock, 0) }} released</p>
    </div>
</div>

{{-- Charts Row 1 --}}
<div class="grid grid-cols-2 gap-4 mb-4">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
        <h3 class="text-base font-semibold text-gray-800 mb-1">Monthly Farmer Registrations</h3>
        <p class="text-xs text-gray-400 mb-4">{{ now()->year }} — Total registrations per month</p>
        <canvas id="registrationChart" height="200"></canvas>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
        <h3 class="text-base font-semibold text-gray-800 mb-1">Monthly Service Records</h3>
        <p class="text-xs text-gray-400 mb-4">{{ now()->year }} — Services rendered per month</p>
        <canvas id="serviceChart" height="200"></canvas>
    </div>
</div>

{{-- Charts Row 2 --}}
<div class="grid grid-cols-3 gap-4 mb-4">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
        <h3 class="text-base font-semibold text-gray-800 mb-1">Farmers by Sex</h3>
        <p class="text-xs text-gray-400 mb-4">Gender distribution</p>
        <canvas id="sexChart" height="200"></canvas>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
        <h3 class="text-base font-semibold text-gray-800 mb-1">Farmers by Livelihood</h3>
        <p class="text-xs text-gray-400 mb-4">Main livelihood type</p>
        <canvas id="livelihoodChart" height="200"></canvas>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
        <h3 class="text-base font-semibold text-gray-800 mb-1">Request Status</h3>
        <p class="text-xs text-gray-400 mb-4">Distribution by status</p>
        <canvas id="requestStatusChart" height="200"></canvas>
    </div>
</div>

{{-- Charts Row 3 --}}
<div class="grid grid-cols-2 gap-4 mb-4">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
        <h3 class="text-base font-semibold text-gray-800 mb-1">Remaining Stock by Category</h3>
        <p class="text-xs text-gray-400 mb-4">Available units per category</p>
        <canvas id="stockChart" height="200"></canvas>
    </div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
        <h3 class="text-base font-semibold text-gray-800 mb-1">Services by Type</h3>
        <p class="text-xs text-gray-400 mb-4">Number of services per type</p>
        <canvas id="serviceTypeChart" height="200"></canvas>
    </div>
</div>

{{-- Top Barangays Table --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 mb-4">
    <h3 class="text-base font-semibold text-gray-800 mb-4">Top 10 Barangays by Registered Farmers</h3>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">#</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Barangay</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">No. of Farmers</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Percentage</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($farmersByBarangay as $index => $item)
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 text-gray-500">{{ $index + 1 }}</td>
                <td class="px-4 py-3 font-medium text-gray-800">{{ $item->barangay?->name ?? 'Unknown' }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $item->total }}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-gray-100 rounded-full h-2">
                            <div class="bg-primary h-2 rounded-full"
                                 style="width: {{ $totalFarmers > 0 ? ($item->total / $totalFarmers) * 100 : 0 }}%">
                            </div>
                        </div>
                        <span class="text-xs text-gray-500">
                            {{ $totalFarmers > 0 ? number_format(($item->total / $totalFarmers) * 100, 1) : 0 }}%
                        </span>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-4 py-8 text-center text-gray-400">No data available.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Request Types Table --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
    <h3 class="text-base font-semibold text-gray-800 mb-4">Requests by Type</h3>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Request Type</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Total</th>
                <th class="text-left px-4 py-3 text-gray-600 font-medium">Percentage</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($requestsByType as $item)
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
            <tr class="hover:bg-gray-50">
                <td class="px-4 py-3 font-medium text-gray-800">{{ $labels[$item->request_type] ?? $item->request_type }}</td>
                <td class="px-4 py-3 text-gray-600">{{ $item->total }}</td>
                <td class="px-4 py-3">
                    <div class="flex items-center gap-2">
                        <div class="flex-1 bg-gray-100 rounded-full h-2">
                            <div class="bg-blue-400 h-2 rounded-full"
                                 style="width: {{ $totalRequests > 0 ? ($item->total / $totalRequests) * 100 : 0 }}%">
                            </div>
                        </div>
                        <span class="text-xs text-gray-500">
                            {{ $totalRequests > 0 ? number_format(($item->total / $totalRequests) * 100, 1) : 0 }}%
                        </span>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="3" class="px-4 py-8 text-center text-gray-400">No data available.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const primaryColor = '#2D7A2D';
    const months       = @json($months);
    const greenPalette = ['#2D7A2D','#4CAF50','#81C784','#A5D6A7','#C8E6C9','#1B5E20','#388E3C','#43A047','#66BB6A','#E8F5E9'];
    const bluePalette  = ['#1565C0','#1976D2','#1E88E5','#2196F3','#42A5F5','#64B5F6','#90CAF9','#BBDEFB'];

    new Chart(document.getElementById('registrationChart'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: 'Farmer Registrations',
                data: @json($registrationData),
                borderColor: primaryColor,
                backgroundColor: 'rgba(45,122,45,0.1)',
                tension: 0.4, fill: true,
                pointBackgroundColor: primaryColor,
            }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });

    new Chart(document.getElementById('serviceChart'), {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{ label: 'Services Rendered', data: @json($serviceData), backgroundColor: 'rgba(45,122,45,0.7)', borderColor: primaryColor, borderWidth: 1, borderRadius: 4 }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });

    new Chart(document.getElementById('sexChart'), {
        type: 'doughnut',
        data: {
            labels: @json($farmersBySex->pluck('sex')->map(fn($s) => ucfirst($s))),
            datasets: [{ data: @json($farmersBySex->pluck('total')), backgroundColor: ['#1976D2', '#E91E63'], borderWidth: 2 }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    new Chart(document.getElementById('livelihoodChart'), {
        type: 'pie',
        data: {
            labels: @json($farmersByLivelihood->pluck('main_livelihood')->map(fn($l) => ucfirst(str_replace('_', ' ', $l)))),
            datasets: [{ data: @json($farmersByLivelihood->pluck('total')), backgroundColor: greenPalette, borderWidth: 2 }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    new Chart(document.getElementById('requestStatusChart'), {
        type: 'doughnut',
        data: {
            labels: ['Pending', 'Approved', 'Completed', 'Rejected'],
            datasets: [{ data: [{{ $pendingRequests }}, {{ $approvedRequests }}, {{ $completedRequests }}, {{ $rejectedRequests }}], backgroundColor: ['#F59E0B', '#3B82F6', '#10B981', '#EF4444'], borderWidth: 2 }]
        },
        options: { responsive: true, plugins: { legend: { position: 'bottom' } } }
    });

    new Chart(document.getElementById('stockChart'), {
        type: 'bar',
        data: {
            labels: @json($stockByCategory->pluck('category')->map(fn($c) => ucfirst($c))),
            datasets: [{ label: 'Remaining Stock', data: @json($stockByCategory->pluck('total')), backgroundColor: greenPalette, borderRadius: 4 }]
        },
        options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
    });

    new Chart(document.getElementById('serviceTypeChart'), {
        type: 'bar',
        data: {
            labels: @json($servicesByType->pluck('service_type')->map(fn($s) => ucfirst(str_replace('_', ' ', $s)))),
            datasets: [{ label: 'Services', data: @json($servicesByType->pluck('total')), backgroundColor: bluePalette, borderRadius: 4 }]
        },
        options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true, ticks: { stepSize: 1 } } } }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        const dropdown = document.getElementById('exportDropdown');
        const menu = document.getElementById('exportMenu');
        if (!dropdown.contains(e.target)) {
            menu.classList.add('hidden');
        }
    });
</script>

@endsection
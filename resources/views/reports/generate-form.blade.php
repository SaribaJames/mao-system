@extends('layouts.app')

@section('content')

    <div class="mb-6 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Generate Narrative Report</h2>
            <p class="text-gray-500 text-sm mt-1">Fill in the narrative sections to generate the official report</p>
        </div>
        <a href="{{ route('reports.index') }}"
            class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium px-4 py-2 rounded-md transition">
            ← Back to Reports
        </a>
    </div>

    <form method="POST" action="{{ route('reports.generate.pdf') }}" target="_blank">
        @csrf

        <div class="grid grid-cols-3 gap-4">

            {{-- Left: Form --}}
            <div class="col-span-2 space-y-4">

                {{-- Report Header Info --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">Report Information</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Report Period</label>
                            <input type="text" name="report_month" value="{{ now()->format('F Y') }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Prepared By</label>
                            <input type="text" name="prepared_by" value="{{ Auth::user()->name }}"
                                class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary" />
                        </div>
                    </div>
                </div>

                {{-- I. Introduction --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-base font-bold text-gray-800 mb-1">I. Introduction</h3>
                    <p class="text-xs text-gray-400 mb-3">Briefly describe the purpose of this report and the activities
                        covered.</p>
                    <textarea name="introduction" rows="4"
                        placeholder="e.g. This report covers the agricultural services and activities rendered by the Municipal Agriculture Office of Guinobatan, Albay for the month of {{ now()->format('F Y') }}..."
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                </div>

                {{-- II. Accomplishments --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-base font-bold text-gray-800 mb-1">II. Accomplishments</h3>
                    <p class="text-xs text-gray-400 mb-3">Describe the major accomplishments for this period. The system
                        stats below will also be included automatically.</p>
                    <textarea name="accomplishments" rows="5"
                        placeholder="e.g. The office successfully conducted farm visits, distributed seeds and fertilizers to qualified farmers, and processed various assistance requests..."
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                </div>

                {{-- III. Problems/Challenges --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-base font-bold text-gray-800 mb-1">III. Problems / Challenges Encountered</h3>
                    <p class="text-xs text-gray-400 mb-3">Describe any problems or challenges encountered during this
                        period.</p>
                    <textarea name="challenges" rows="4"
                        placeholder="e.g. Some farmers were unable to provide complete documentary requirements for the assistance program. Weather disturbances also affected farm visits in some barangays..."
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                </div>

                {{-- IV. Recommendations --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-base font-bold text-gray-800 mb-1">IV. Recommendations</h3>
                    <p class="text-xs text-gray-400 mb-3">Provide recommendations for improvement or future activities.</p>
                    <textarea name="recommendations" rows="4"
                        placeholder="e.g. It is recommended to conduct information drives in barangays with low farmer registration rates. Additional funding for seeds distribution should also be considered..."
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                </div>

                {{-- V. Conclusion --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                    <h3 class="text-base font-bold text-gray-800 mb-1">V. Conclusion</h3>
                    <p class="text-xs text-gray-400 mb-3">Provide a brief conclusion summarizing the report.</p>
                    <textarea name="conclusion" rows="3"
                        placeholder="e.g. Overall, the Municipal Agriculture Office of Guinobatan has made significant progress in delivering agricultural services to the farmers of the municipality..."
                        class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                </div>

                {{-- Submit --}}
                <div class="flex gap-3">
                    <button type="submit"
                        class="bg-gray-700 hover:bg-gray-800 text-white font-semibold px-6 py-2.5 rounded-md transition text-sm flex items-center gap-2">
                        <i class="fa-solid fa-file-contract"></i> Generate PDF Report
                    </button>
                    <a href="{{ route('reports.index') }}"
                        class="border border-gray-300 text-gray-600 hover:bg-gray-50 font-medium px-6 py-2.5 rounded-md transition text-sm">
                        Cancel
                    </a>
                </div>

            </div>

            {{-- Right: Auto Stats --}}
            <div class="col-span-1 space-y-4">

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 sticky top-20">
                    <h3 class="text-sm font-bold text-gray-800 mb-3 pb-2 border-b border-gray-200">
                        <i class="fa-solid fa-chart-bar text-primary mr-1"></i>
                        Auto-populated Statistics
                    </h3>
                    <p class="text-xs text-gray-400 mb-4">These will be automatically included in the report.</p>

                    <div class="space-y-3">
                        <div class="bg-green-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 mb-1">Farmers</p>
                            <p class="text-lg font-bold text-primary">{{ $totalFarmers }}</p>
                            <p class="text-xs text-gray-400">{{ $activeFarmers }} active • +{{ $newThisMonth }} this month
                            </p>
                        </div>
                        <div class="bg-blue-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 mb-1">Requests</p>
                            <p class="text-lg font-bold text-blue-600">{{ $totalRequests }}</p>
                            <p class="text-xs text-gray-400">{{ $completedRequests }} completed • {{ $pendingRequests }}
                                pending</p>
                        </div>
                        <div class="bg-yellow-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 mb-1">Services Rendered</p>
                            <p class="text-lg font-bold text-yellow-600">{{ $totalServices }}</p>
                            <p class="text-xs text-gray-400">{{ $completedServices }} completed</p>
                        </div>
                        <div class="bg-orange-50 rounded-lg p-3">
                            <p class="text-xs text-gray-500 mb-1">Stocks</p>
                            <p class="text-lg font-bold text-orange-600">{{ number_format($remainingStock, 0) }}</p>
                            <p class="text-xs text-gray-400">units remaining • {{ number_format($releasedStock, 0) }}
                                released</p>
                        </div>
                    </div>

                    <div class="mt-4 p-3 bg-gray-50 rounded-lg">
                        <p class="text-xs text-gray-500">
                            <i class="fa-solid fa-info-circle mr-1"></i>
                            These statistics are pulled directly from the system database and will appear in the generated
                            PDF report.
                        </p>
                    </div>
                </div>

            </div>
        </div>

    </form>
@endsection
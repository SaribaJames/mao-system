@extends('layouts.app')

@section('content')

    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Generate Narrative Report</h2>
            <p class="text-gray-500 text-sm mt-1">Type your report below — it will appear exactly as shown</p>
        </div>
        <a href="{{ route('reports.index') }}"
            class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium px-4 py-2 rounded transition">
            ← Back
        </a>
    </div>

    <div class="flex gap-6">

        {{-- Left: Paper Document --}}
        <div class="flex-1">
            <form method="POST" action="{{ route('reports.generate.pdf') }}" target="_blank" id="reportForm">
                @csrf

                {{-- Paper --}}
                <div class="bg-white border border-gray-300 rounded mx-auto"
                    style="width: 816px; min-height: 1056px; padding: 72px 80px; font-family: 'Times New Roman', serif;">

                    {{-- Letterhead --}}
                    <div
                        style="text-align: center; margin-bottom: 20px; border-bottom: 3px double #2D7A2D; padding-bottom: 15px;">
                        <div
                            style="display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 8px;">
                            {{-- Logo placeholder --}}
                            <div
                                style="width: 70px; height: 70px; border: 2px solid #2D7A2D; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 10px; color: #2D7A2D; text-align: center; font-weight: bold; line-height: 1.2;">
                                MAO<br>LOGO
                            </div>
                            <div>
                                <p style="font-size: 11px; margin: 0; color: #555;">Republic of the Philippines</p>
                                <p style="font-size: 11px; margin: 0; color: #555;">Province of Albay</p>
                                <p style="font-size: 16px; font-weight: bold; margin: 3px 0; color: #2D7A2D;">MUNICIPAL
                                    AGRICULTURE OFFICE</p>
                                <p style="font-size: 13px; font-weight: bold; margin: 0;">Guinobatan, Albay</p>
                                <p style="font-size: 10px; margin: 2px 0; color: #555;">Email:
                                    mao.guinobatan.albay@gmail.com</p>
                            </div>
                        </div>
                    </div>

                    {{-- Document Title --}}
                    <div style="text-align: center; margin-bottom: 25px;">
                        <p
                            style="font-size: 14px; font-weight: bold; text-transform: uppercase; text-decoration: underline; margin: 0;">
                            Narrative Report
                        </p>
                        <div style="margin-top: 8px; display: flex; justify-content: center; gap: 20px;">
                            <div style="display: flex; align-items: center; gap: 5px;">
                                <span style="font-size: 11px;">For the Month of:</span>
                                <input type="text" name="report_month" value="{{ now()->format('F Y') }}"
                                    style="border: none; border-bottom: 1px solid #333; font-size: 11px; text-align: center; width: 120px; outline: none; font-family: 'Times New Roman', serif;" />
                            </div>
                        </div>
                    </div>

                    {{-- I. Introduction --}}
                    <div style="margin-bottom: 20px;">
                        <p style="font-size: 12px; font-weight: bold; margin-bottom: 8px;">I. INTRODUCTION</p>
                        <textarea name="introduction"
                            style="width: 100%; border: none; outline: none; resize: none; font-size: 12px; font-family: 'Times New Roman', serif; line-height: 1.8; text-align: justify; background: transparent;"
                            rows="4" placeholder="Type introduction here..."
                            oninput="autoResize(this)">This report covers the agricultural services and activities rendered by the Municipal Agriculture Office of Guinobatan, Albay for the month of {{ now()->format('F Y') }}. The office is committed to providing quality agricultural services to the farmers of the municipality in line with the national agricultural development programs.</textarea>
                        <div style="border-bottom: 1px solid #ccc; margin-top: 2px;"></div>
                    </div>

                    {{-- II. Accomplishments --}}
                    <div style="margin-bottom: 20px;">
                        <p style="font-size: 12px; font-weight: bold; margin-bottom: 8px;">II. ACCOMPLISHMENTS</p>

                        {{-- Auto Stats --}}
                        <div
                            style="font-size: 11px; margin-bottom: 10px; padding: 8px; background: #f9f9f9; border-left: 3px solid #2D7A2D;">
                            <p style="font-weight: bold; margin-bottom: 5px;">System Data (Auto-generated):</p>
                            <p>• Total Registered Farmers: <strong>{{ $totalFarmers }}</strong> ({{ $activeFarmers }}
                                active, +{{ $newThisMonth }} new this month)</p>
                            <p>• Total Requests: <strong>{{ $totalRequests }}</strong> ({{ $completedRequests }} completed,
                                {{ $pendingRequests }} pending)</p>
                            <p>• Services Rendered: <strong>{{ $totalServices }}</strong> ({{ $completedServices }}
                                completed)</p>
                            <p>• Remaining Stocks: <strong>{{ number_format($remainingStock, 0) }}</strong> units
                                ({{ number_format($releasedStock, 0) }} released)</p>
                        </div>

                        <textarea name="accomplishments"
                            style="width: 100%; border: none; outline: none; resize: none; font-size: 12px; font-family: 'Times New Roman', serif; line-height: 1.8; text-align: justify; background: transparent;"
                            rows="5" placeholder="Describe your accomplishments here..."
                            oninput="autoResize(this)"></textarea>
                        <div style="border-bottom: 1px solid #ccc; margin-top: 2px;"></div>
                    </div>

                    {{-- III. Problems/Challenges --}}
                    <div style="margin-bottom: 20px;">
                        <p style="font-size: 12px; font-weight: bold; margin-bottom: 8px;">III. PROBLEMS / CHALLENGES
                            ENCOUNTERED</p>
                        <textarea name="challenges"
                            style="width: 100%; border: none; outline: none; resize: none; font-size: 12px; font-family: 'Times New Roman', serif; line-height: 1.8; text-align: justify; background: transparent;"
                            rows="4" placeholder="Describe problems or challenges encountered..."
                            oninput="autoResize(this)"></textarea>
                        <div style="border-bottom: 1px solid #ccc; margin-top: 2px;"></div>
                    </div>

                    {{-- IV. Recommendations --}}
                    <div style="margin-bottom: 20px;">
                        <p style="font-size: 12px; font-weight: bold; margin-bottom: 8px;">IV. RECOMMENDATIONS</p>
                        <textarea name="recommendations"
                            style="width: 100%; border: none; outline: none; resize: none; font-size: 12px; font-family: 'Times New Roman', serif; line-height: 1.8; text-align: justify; background: transparent;"
                            rows="4" placeholder="Type your recommendations here..." oninput="autoResize(this)"></textarea>
                        <div style="border-bottom: 1px solid #ccc; margin-top: 2px;"></div>
                    </div>

                    {{-- V. Conclusion --}}
                    <div style="margin-bottom: 40px;">
                        <p style="font-size: 12px; font-weight: bold; margin-bottom: 8px;">V. CONCLUSION</p>
                        <textarea name="conclusion"
                            style="width: 100%; border: none; outline: none; resize: none; font-size: 12px; font-family: 'Times New Roman', serif; line-height: 1.8; text-align: justify; background: transparent;"
                            rows="3" placeholder="Type your conclusion here..." oninput="autoResize(this)"></textarea>
                        <div style="border-bottom: 1px solid #ccc; margin-top: 2px;"></div>
                    </div>

                    {{-- Signature Area --}}
                    <div style="margin-top: 50px; display: table; width: 100%;">
                        <div style="display: table-cell; width: 50%; padding-right: 20px;">
                            <p style="font-size: 11px; margin-bottom: 3px;">Prepared by:</p>
                            <div style="border-bottom: 1px solid #333; margin-top: 35px; padding-top: 5px;">
                                <input type="text" name="prepared_by" value="{{ Auth::user()->name }}"
                                    style="border: none; outline: none; font-size: 11px; font-weight: bold; font-family: 'Times New Roman', serif; width: 100%; text-transform: uppercase;" />
                                <p style="font-size: 10px; color: #555; margin: 2px 0;">Agricultural Technician</p>
                                <p style="font-size: 10px; color: #555; margin: 0;">MAO Guinobatan, Albay</p>
                            </div>
                        </div>
                        <div style="display: table-cell; width: 50%; padding-left: 20px;">
                            <p style="font-size: 11px; margin-bottom: 3px;">Noted by:</p>
                            <div style="border-bottom: 1px solid #333; margin-top: 35px; padding-top: 5px;">
                                <p style="font-size: 11px; font-weight: bold; margin: 0;">_______________________</p>
                                <p style="font-size: 10px; color: #555; margin: 2px 0;">Municipal Agriculturist</p>
                                <p style="font-size: 10px; color: #555; margin: 0;">MAO Guinobatan, Albay</p>
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div style="text-align: center; margin-top: 40px; border-top: 1px solid #ddd; padding-top: 8px;">
                        <p style="font-size: 9px; color: #999;">Municipal Agriculture Office — Guinobatan, Albay | Digital
                            Farmer Records and Service Management System</p>
                        <p style="font-size: 9px; color: #999;">Generated on {{ date('F d, Y') }} | DRAFT — Subject to
                            review and approval</p>
                    </div>

                </div>

                {{-- Generate Button --}}
                <div class="flex gap-3 mt-4 justify-center" style="width: 816px; margin: 0 auto;">
                    <button type="submit"
                        class="bg-gray-700 hover:bg-gray-800 text-white font-semibold px-8 py-3 rounded transition flex items-center gap-2">
                        <i class="fa-solid fa-file-pdf"></i> Download as PDF
                    </button>
                    <a href="{{ route('reports.index') }}"
                        class="border border-gray-300 text-gray-600 hover:bg-gray-50 font-medium px-8 py-3 rounded transition">
                        Cancel
                    </a>
                </div>

            </form>
        </div>

    </div>

    <script>
        function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = textarea.scrollHeight + 'px';
        }

        // Auto resize all textareas on load
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('textarea').forEach(function (textarea) {
                autoResize(textarea);
            });
        });
    </script>

@endsection
@extends('layouts.app')

@section('content')

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Forms & Documents</h2>
        <p class="text-gray-500 text-sm mt-1">Official government forms for farmer assistance programs</p>
    </div>

    <div class="grid grid-cols-3 gap-4">

        {{-- PCIC ADSS Form --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="bg-blue-100 w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                <i class="fa-solid fa-shield-halved text-blue-600 text-xl"></i>
            </div>
            <h3 class="text-base font-bold text-gray-800 mb-1">PCIC ADSS Form</h3>
            <p class="text-xs text-gray-500 mb-1">Philippine Crop Insurance Corporation</p>
            <p class="text-xs text-gray-400 mb-4">Accident and Dismemberment Security Scheme — Life Insurance for farmers
            </p>
            <div class="flex items-center justify-between">
                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">ADSS Form No. 1</span>
                <a href="{{ route('forms.pcic-adss') }}"
                    class="bg-primary hover:bg-primary-dark text-white text-xs font-semibold px-3 py-1.5 rounded-md transition">
                    Fill Out →
                </a>
            </div>
        </div>

        {{-- Livestock Mortality Insurance Form --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="bg-green-100 w-12 h-12 rounded-lg flex items-center justify-center mb-4">
                <i class="fa-solid fa-cow text-green-600 text-xl"></i>
            </div>
            <h3 class="text-base font-bold text-gray-800 mb-1">Livestock Mortality Insurance</h3>
            <p class="text-xs text-gray-500 mb-1">Philippine Crop Insurance Corporation</p>
            <p class="text-xs text-gray-400 mb-4">Application for insurance coverage on cattle, carabao, swine, poultry, and other livestock</p>
            <div class="flex items-center justify-between">
                <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">LIV-UPI-01</span>
                <a href="{{ route('livestock-insurance.index') }}"
                    class="bg-primary hover:bg-primary-dark text-white text-xs font-semibold px-3 py-1.5 rounded-md transition">
                    Fill Out →
                </a>
            </div>
        </div>

        <div
            class="bg-gray-50 rounded-xl border border-dashed border-gray-200 p-5 flex flex-col items-center justify-center text-center">
            <i class="fa-solid fa-clock text-gray-300 text-3xl mb-3"></i>
            <p class="text-sm font-medium text-gray-400">More Forms Coming Soon</p>
            <p class="text-xs text-gray-300 mt-1">Additional government forms</p>
        </div>

    </div>

@endsection
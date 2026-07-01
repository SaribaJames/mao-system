@extends('layouts.app')

@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">
            {{ $farmer->first_name }} {{ $farmer->surname }}
        </h2>
        <p class="text-gray-500 text-sm mt-1 font-mono">{{ $farmer->reference_number }}</p>
    </div>
    <div class="flex gap-2">
        @if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff')
        <a href="{{ route('farmers.edit', $farmer) }}"
           class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold px-4 py-2 rounded-md transition">
            <i class="fa-solid fa-pen mr-1"></i> Edit
        </a>
        @endif
        <a href="{{ route('farmers.index') }}"
           class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium px-4 py-2 rounded-md transition">
            ← Back
        </a>
    </div>
</div>

<div class="grid grid-cols-3 gap-4">

    {{-- Left: Main Info --}}
    <div class="col-span-2 space-y-4">

        {{-- Personal Information --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <h3 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                Part I — Personal Information
            </h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Full Name</p>
                    <p class="font-medium text-gray-800">
                        {{ $farmer->first_name }} {{ $farmer->middle_name }} {{ $farmer->surname }}
                        @if($farmer->extension_name) {{ $farmer->extension_name }} @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Sex</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $farmer->sex }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Date of Birth</p>
                    <p class="font-medium text-gray-800">
                        {{ \Carbon\Carbon::parse($farmer->date_of_birth)->format('F d, Y') }}
                        ({{ \Carbon\Carbon::parse($farmer->date_of_birth)->age }} years old)
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Place of Birth</p>
                    <p class="font-medium text-gray-800">{{ $farmer->place_of_birth ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Mobile Number</p>
                    <p class="font-medium text-gray-800">{{ $farmer->mobile_number ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Civil Status</p>
                    <p class="font-medium text-gray-800 capitalize">{{ $farmer->civil_status ?? '—' }}</p>
                </div>
                <div class="col-span-2">
                    <p class="text-xs text-gray-400 mb-1">Address</p>
                    <p class="font-medium text-gray-800">
                        {{ $farmer->house_lot_number ?? '' }} {{ $farmer->street ?? '' }},
                        {{ $farmer->barangay?->name ?? '' }}, {{ $farmer->municipality ?? '' }}, {{ $farmer->province ?? '' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Religion</p>
                    <p class="font-medium text-gray-800">{{ $farmer->religion ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Education</p>
                    <p class="font-medium text-gray-800 capitalize">
                        {{ str_replace('_', ' ', $farmer->highest_education ?? '—') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Household Head</p>
                    <p class="font-medium text-gray-800">{{ $farmer->is_household_head ? 'Yes' : 'No' }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Household Members</p>
                    <p class="font-medium text-gray-800">{{ $farmer->household_members_count ?? '—' }}</p>
                </div>
            </div>
        </div>

        {{-- Special Classifications --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <h3 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                Special Classifications
            </h3>
            <div class="grid grid-cols-2 gap-3 text-sm">
                @foreach([
                    ['field' => 'is_pwd',                      'label' => 'Person with Disability (PWD)'],
                    ['field' => 'is_4ps_beneficiary',          'label' => "4P's Beneficiary"],
                    ['field' => 'is_indigenous',               'label' => 'Indigenous Group Member'],
                    ['field' => 'is_farmers_association_member','label' => 'Farmers Association Member'],
                ] as $item)
                <div class="flex items-center gap-2">
                    <span class="w-4 h-4 rounded flex items-center justify-center
                        {{ $farmer->{$item['field']} ? 'bg-primary' : 'bg-gray-200' }}">
                        @if($farmer->{$item['field']})
                            <i class="fa-solid fa-check text-white text-xs"></i>
                        @endif
                    </span>
                    <span class="text-gray-700">{{ $item['label'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Farm Profile --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <h3 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">
                Part II — Farm Profile
            </h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Main Livelihood</p>
                    <p class="font-medium text-gray-800 capitalize">
                        {{ str_replace('_', ' ', $farmer->main_livelihood ?? '—') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Land Holding Status</p>
                    <p class="font-medium text-gray-800 capitalize">
                        {{ str_replace('_', ' ', $farmer->land_holding_status ?? '—') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Farm Location</p>
                    <p class="font-medium text-gray-800">
                        {{ $farmer->farm_location_barangay ?? '' }}
                        @if($farmer->farm_location_municipality), {{ $farmer->farm_location_municipality }}@endif
                        @if($farmer->farm_location_province), {{ $farmer->farm_location_province }}@endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Land Area</p>
                    <p class="font-medium text-gray-800">
                        {{ $farmer->land_area_hectares ? $farmer->land_area_hectares . ' ha' : '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Farming Activities</p>
                    <div class="flex flex-wrap gap-1 mt-1">
                        @if($farmer->farming_rice)
                            <span class="px-2 py-0.5 bg-green-100 text-green-700 rounded-full text-xs">Rice</span>
                        @endif
                        @if($farmer->farming_corn)
                            <span class="px-2 py-0.5 bg-yellow-100 text-yellow-700 rounded-full text-xs">Corn</span>
                        @endif
                        @if($farmer->farming_livestock)
                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full text-xs">
                                Livestock {{ $farmer->farming_livestock_specify ? '('.$farmer->farming_livestock_specify.')' : '' }}
                            </span>
                        @endif
                        @if($farmer->farming_poultry)
                            <span class="px-2 py-0.5 bg-orange-100 text-orange-700 rounded-full text-xs">
                                Poultry {{ $farmer->farming_poultry_specify ? '('.$farmer->farming_poultry_specify.')' : '' }}
                            </span>
                        @endif
                        @if($farmer->farming_other_crops)
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-700 rounded-full text-xs">
                                {{ $farmer->farming_other_crops_specify ?? 'Other Crops' }}
                            </span>
                        @endif
                        @if(!$farmer->farming_rice && !$farmer->farming_corn && !$farmer->farming_livestock && !$farmer->farming_poultry && !$farmer->farming_other_crops)
                            <span class="text-gray-400">—</span>
                        @endif
                    </div>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Annual Income (Farming)</p>
                    <p class="font-medium text-gray-800">
                        {{ $farmer->gross_annual_income_farming ? '₱' . number_format($farmer->gross_annual_income_farming, 2) : '—' }}
                    </p>
                </div>
            </div>
        </div>

    </div>

    {{-- Right: Sidebar --}}
    <div class="space-y-4">

        {{-- Status --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <h3 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-200">Status</h3>
            @php $statusClass = $farmer->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'; @endphp
            <span class="px-3 py-1.5 rounded-full text-sm font-medium {{ $statusClass }}">
                {{ ucfirst($farmer->status) }}
            </span>
            <div class="mt-4 text-xs text-gray-400 space-y-1">
                <p>Registered: {{ $farmer->created_at->format('M d, Y') }}</p>
                <p>By: {{ $farmer->registeredBy?->name ?? '—' }}</p>
            </div>
        </div>

        {{-- Emergency Contact --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <h3 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-200">Emergency Contact</h3>
            <p class="text-sm font-medium text-gray-800">{{ $farmer->emergency_contact_name ?? '—' }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $farmer->emergency_contact_number ?? '' }}</p>
        </div>

        {{-- Government ID --}}
        @if($farmer->has_government_id)
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <h3 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-200">Government ID</h3>
            <p class="text-xs text-gray-400 mb-1">Type</p>
            <p class="text-sm font-medium text-gray-800">{{ $farmer->government_id_type ?? '—' }}</p>
            <p class="text-xs text-gray-400 mb-1 mt-2">ID Number</p>
            <p class="text-sm font-medium text-gray-800">{{ $farmer->government_id_number ?? '—' }}</p>
        </div>
        @endif

        {{-- Farmers Association --}}
        @if($farmer->is_farmers_association_member)
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5">
            <h3 class="text-base font-bold text-gray-800 mb-3 pb-2 border-b border-gray-200">Farmers Association</h3>
            <p class="text-sm font-medium text-gray-800">{{ $farmer->farmers_association_name ?? '—' }}</p>
        </div>
        @endif

    </div>
</div>

@endsection
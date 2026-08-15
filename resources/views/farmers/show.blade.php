@extends('layouts.app')

@section('content')

    {{-- Header --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            @if($farmer->photo)
                <img src="{{ asset('storage/' . $farmer->photo) }}" alt="Photo"
                    class="w-16 h-16 rounded-full object-cover border border-gray-200 flex-shrink-0">
            @else
                <div class="w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-user text-gray-300 dark:text-gray-600 text-xl"></i>
                </div>
            @endif
            <div>
                <h2 class="text-3xl font-bold tracking-tight text-gray-900">
                    {{ $farmer->first_name }} {{ $farmer->surname }}
                </h2>
                <p class="text-gray-500 text-sm mt-1 font-mono">{{ $farmer->reference_number }}</p>
            </div>
        </div>
        <div class="flex gap-2">
            @if(Auth::user()->isAdmin() || Auth::user()->role?->name === 'staff')
            <a href="{{ route('farmers.edit', $farmer) }}"
            class="bg-blue-500 hover:bg-blue-600 text-white text-sm font-semibold px-4 py-2 rounded-md transition">
                <i class="fa-solid fa-pen mr-1"></i> Edit
            </a>
            <a href="{{ route('farmers.print', $farmer) }}" target="_blank"
            class="bg-gray-700 hover:bg-gray-800 text-white text-sm font-semibold px-4 py-2 rounded-md transition">
                <i class="fa-solid fa-print mr-1"></i> Print
            </a>
            @endif
            <a href="{{ route('farmers.index') }}"
            class="border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 text-sm font-medium px-4 py-2 rounded-md transition">
                ← Back
            </a>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4">

        {{-- Left: Main Info --}}
        <div class="col-span-2 space-y-4">

            {{-- Personal Information --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-5">
                <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                    Part I — Personal Information
                </h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Full Name</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100">
                            {{ $farmer->first_name }} {{ $farmer->middle_name }} {{ $farmer->surname }}
                            @if($farmer->extension_name) {{ $farmer->extension_name }} @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Sex</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100 capitalize">{{ $farmer->sex }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Date of Birth</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100">
                            {{ \Carbon\Carbon::parse($farmer->date_of_birth)->format('F d, Y') }}
                            ({{ \Carbon\Carbon::parse($farmer->date_of_birth)->age }} years old)
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Place of Birth</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100">{{ $farmer->place_of_birth ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Mobile Number</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100">{{ $farmer->mobile_number ?? '—' }}</p>
                    </div>
                    @if($farmer->landline_number)
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Landline Number</p>
                            <p class="font-medium text-gray-800 dark:text-gray-100">{{ $farmer->landline_number }}</p>
                        </div>
                    @endif
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Civil Status</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100 capitalize">{{ $farmer->civil_status ?? '—' }}</p>
                    </div>
                    @if($farmer->civil_status === 'married' && $farmer->spouse_name)
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Spouse's Name</p>
                            <p class="font-medium text-gray-800 dark:text-gray-100">{{ $farmer->spouse_name }}</p>
                        </div>
                    @endif
                    @if($farmer->mother_maiden_name)
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Mother's Maiden Name</p>
                            <p class="font-medium text-gray-800 dark:text-gray-100">{{ $farmer->mother_maiden_name }}</p>
                        </div>
                    @endif
                    <div class="col-span-2">
                        <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Address</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100">
                            {{ $farmer->house_lot_number ?? '' }} {{ $farmer->street ?? '' }},
                            {{ $farmer->barangay?->name ?? '' }}, {{ $farmer->municipality ?? '' }},
                            {{ $farmer->province ?? '' }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Religion</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100">{{ $farmer->religion ?? '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Education</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100 capitalize">
                            {{ str_replace('_', ' ', $farmer->highest_education ?? '—') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Household Head</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100">{{ $farmer->is_household_head ? 'Yes' : 'No' }}</p>
                    </div>
                    @if(!$farmer->is_household_head && ($farmer->household_head_name || $farmer->household_head_relationship))
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Household Head Name</p>
                            <p class="font-medium text-gray-800 dark:text-gray-100">
                                {{ $farmer->household_head_name ?? '—' }}
                                @if($farmer->household_head_relationship)
                                    <span class="text-gray-400 font-normal">({{ $farmer->household_head_relationship }})</span>
                                @endif
                            </p>
                        </div>
                    @endif
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Household Members</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100">
                            {{ $farmer->household_members_count ?? '—' }}
                            @if($farmer->household_male_count || $farmer->household_female_count)
                                <span class="text-gray-400 font-normal text-xs">
                                    ({{ $farmer->household_male_count ?? 0 }} male, {{ $farmer->household_female_count ?? 0 }} female)
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            {{-- Special Classifications --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-5">
                <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                    Special Classifications
                </h3>
                <div class="grid grid-cols-2 gap-3 text-sm">
                    @foreach([
                            ['field' => 'is_pwd', 'label' => 'Person with Disability (PWD)'],
                            ['field' => 'is_4ps_beneficiary', 'label' => "4P's Beneficiary"],
                            ['field' => 'is_indigenous', 'label' => 'Indigenous Group Member'],
                            ['field' => 'is_farmers_association_member', 'label' => 'Farmers Association Member'],
                        ] as $item)
                        <div class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded flex items-center justify-center
                                {{ $farmer->{$item['field']} ? 'bg-primary' : 'bg-gray-200 dark:bg-gray-700' }}">
                                @if($farmer->{$item['field']})
                                    <i class="fa-solid fa-check text-white text-xs"></i>
                                @endif
                            </span>
                            <span class="text-gray-700 dark:text-gray-300">{{ $item['label'] }}</span>
                        </div>
                    @endforeach
                </div>
                @if($farmer->is_indigenous && $farmer->indigenous_group_name)
                    <div class="mt-3 pt-3 border-t border-border-soft text-sm">
                        <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Indigenous Group</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100">{{ $farmer->indigenous_group_name }}</p>
                    </div>
                @endif
            </div>

            {{-- Farm Profile --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-5">
                <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                    Part II — Farm Profile
                </h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Main Livelihood</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100 capitalize">
                            {{ str_replace('_', ' ', $farmer->main_livelihood ?? '—') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Land Holding Status</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100 capitalize">
                            {{ str_replace('_', ' ', $farmer->land_holding_status ?? '—') }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Farm Location</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100">
                            {{ $farmer->farm_location_barangay ?? '' }}
                            @if($farmer->farm_location_municipality), {{ $farmer->farm_location_municipality }}@endif
                            @if($farmer->farm_location_province), {{ $farmer->farm_location_province }}@endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Land Area</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100">
                            {{ $farmer->land_area_hectares ? $farmer->land_area_hectares . ' ha' : '—' }}
                        </p>
                </div>
                    @if($farmer->main_livelihood === 'farmer' || $farmer->farming_rice || $farmer->farming_corn || $farmer->farming_livestock || $farmer->farming_poultry || $farmer->farming_other_crops)
                        <div class="col-span-2">
                            <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Farming Activities</p>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @if($farmer->farming_rice)
                                    <span class="px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full text-xs">Rice</span>
                                @endif
                                @if($farmer->farming_corn)
                                    <span class="px-2 py-0.5 bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300 rounded-full text-xs">Corn</span>
                                @endif
                                @if($farmer->farming_livestock)
                                    <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full text-xs">
                                        Livestock {{ $farmer->farming_livestock_specify ? '(' . $farmer->farming_livestock_specify . ')' : '' }}
                                    </span>
                                @endif
                                @if($farmer->farming_poultry)
                                    <span class="px-2 py-0.5 bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-300 rounded-full text-xs">
                                        Poultry {{ $farmer->farming_poultry_specify ? '(' . $farmer->farming_poultry_specify . ')' : '' }}
                                    </span>
                                @endif
                                @if($farmer->farming_other_crops)
                                    <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-xs">
                                        {{ $farmer->farming_other_crops_specify ?? 'Other Crops' }}
                                    </span>
                                @endif
                                @if(!$farmer->farming_rice && !$farmer->farming_corn && !$farmer->farming_livestock && !$farmer->farming_poultry && !$farmer->farming_other_crops)
                                    <span class="text-gray-400">—</span>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($farmer->main_livelihood === 'farmworker' || $farmer->farmwork_land_preparation || $farmer->farmwork_planting || $farmer->farmwork_cultivation || $farmer->farmwork_harvesting || $farmer->farmwork_others)
                        <div class="col-span-2">
                            <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Farmworker Tasks</p>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @if($farmer->farmwork_land_preparation)
                                    <span class="px-2 py-0.5 bg-lime-100 dark:bg-lime-900/30 text-lime-700 dark:text-lime-300 rounded-full text-xs">Land Preparation</span>
                                @endif
                                @if($farmer->farmwork_planting)
                                    <span class="px-2 py-0.5 bg-lime-100 dark:bg-lime-900/30 text-lime-700 dark:text-lime-300 rounded-full text-xs">Planting</span>
                                @endif
                                @if($farmer->farmwork_cultivation)
                                    <span class="px-2 py-0.5 bg-lime-100 dark:bg-lime-900/30 text-lime-700 dark:text-lime-300 rounded-full text-xs">Cultivation</span>
                                @endif
                                @if($farmer->farmwork_harvesting)
                                    <span class="px-2 py-0.5 bg-lime-100 dark:bg-lime-900/30 text-lime-700 dark:text-lime-300 rounded-full text-xs">Harvesting</span>
                                @endif
                                @if($farmer->farmwork_others)
                                    <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-xs">
                                        {{ $farmer->farmwork_others_specify ?? 'Other' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($farmer->main_livelihood === 'fisherfolk' || $farmer->fishing_capture || $farmer->fishing_aquaculture || $farmer->fishing_processing || $farmer->fishing_vending || $farmer->fishing_gleaning || $farmer->fishing_others)
                        <div class="col-span-2">
                            <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Fisherfolk Activities</p>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @if($farmer->fishing_capture)
                                    <span class="px-2 py-0.5 bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-300 rounded-full text-xs">Capture</span>
                                @endif
                                @if($farmer->fishing_aquaculture)
                                    <span class="px-2 py-0.5 bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-300 rounded-full text-xs">Aquaculture</span>
                                @endif
                                @if($farmer->fishing_processing)
                                    <span class="px-2 py-0.5 bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-300 rounded-full text-xs">Processing</span>
                                @endif
                                @if($farmer->fishing_vending)
                                    <span class="px-2 py-0.5 bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-300 rounded-full text-xs">Vending</span>
                                @endif
                                @if($farmer->fishing_gleaning)
                                    <span class="px-2 py-0.5 bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-300 rounded-full text-xs">Gleaning</span>
                                @endif
                                @if($farmer->fishing_others)
                                    <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-xs">
                                        {{ $farmer->fishing_others_specify ?? 'Other' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if($farmer->main_livelihood === 'agri_youth' || $farmer->agri_youth_farming_household || $farmer->agri_youth_formal_course || $farmer->agri_youth_nonformal_course || $farmer->agri_youth_participated_program || $farmer->agri_youth_others)
                        <div class="col-span-2">
                            <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Agri-Youth Involvement</p>
                            <div class="flex flex-wrap gap-1 mt-1">
                                @if($farmer->agri_youth_farming_household)
                                    <span class="px-2 py-0.5 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-full text-xs">Part of Farming Household</span>
                                @endif
                                @if($farmer->agri_youth_formal_course)
                                    <span class="px-2 py-0.5 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-full text-xs">Formal Agri Course</span>
                                @endif
                                @if($farmer->agri_youth_nonformal_course)
                                    <span class="px-2 py-0.5 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-full text-xs">Non-Formal Agri Course</span>
                                @endif
                                @if($farmer->agri_youth_participated_program)
                                    <span class="px-2 py-0.5 bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 rounded-full text-xs">Participated in Agri Program</span>
                                @endif
                                @if($farmer->agri_youth_others)
                                    <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-xs">
                                        {{ $farmer->agri_youth_others_specify ?? 'Other' }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div>
                        <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Annual Income (Farming)</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100">
                            {{ $farmer->gross_annual_income_farming ? '₱' . number_format($farmer->gross_annual_income_farming, 2) : '—' }}
                        </p>
                    </div>
                    @if($farmer->gross_annual_income_non_farming)
                        <div>
                            <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Annual Income (Non-Farming)</p>
                            <p class="font-medium text-gray-800 dark:text-gray-100">
                                ₱{{ number_format($farmer->gross_annual_income_non_farming, 2) }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Farm Parcels --}}
            @if($farmer->farmParcels && $farmer->farmParcels->count() > 0)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-5">
                <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                    Farm Parcel Information ({{ $farmer->farmParcels->count() }})
                </h3>
                <div class="space-y-4">
                    @foreach($farmer->farmParcels as $parcel)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-md p-4">
                        <p class="text-xs font-bold text-primary uppercase tracking-wide mb-3">Parcel {{ $parcel->parcel_number }}</p>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Location</p>
                                <p class="font-medium text-gray-800 dark:text-gray-100">
                                    {{ $parcel->farm_location_barangay ?? '—' }}{{ $parcel->farm_location_municipality ? ', ' . $parcel->farm_location_municipality : '' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Total Farm Area</p>
                                <p class="font-medium text-gray-800 dark:text-gray-100">{{ $parcel->total_farm_area_ha ? number_format($parcel->total_farm_area_ha, 2) . ' ha' : '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Ownership Type</p>
                                <p class="font-medium text-gray-800 dark:text-gray-100 capitalize">{{ str_replace('_', ' ', $parcel->ownership_type ?? '—') }}</p>
                            </div>
                            @if($parcel->owner_name)
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Owner/Tenant/Lessee Name</p>
                                <p class="font-medium text-gray-800 dark:text-gray-100">{{ $parcel->owner_name }}</p>
                            </div>
                            @endif
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Crop/Commodity</p>
                                <p class="font-medium text-gray-800 dark:text-gray-100">{{ $parcel->crop_commodity ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Size</p>
                                <p class="font-medium text-gray-800 dark:text-gray-100">{{ $parcel->size_ha ? number_format($parcel->size_ha, 2) . ' ha' : '—' }}</p>
                            </div>
                            @if($parcel->no_of_head)
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">No. of Head</p>
                                <p class="font-medium text-gray-800 dark:text-gray-100">{{ $parcel->no_of_head }}</p>
                            </div>
                            @endif
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Organic Practitioner</p>
                                <p class="font-medium text-gray-800 dark:text-gray-100">{{ $parcel->organic_practitioner ? 'Yes' : 'No' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Ancestral Domain</p>
                                <p class="font-medium text-gray-800 dark:text-gray-100">{{ $parcel->within_ancestral_domain ? 'Yes' : 'No' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Agrarian Reform Beneficiary</p>
                                <p class="font-medium text-gray-800 dark:text-gray-100">{{ $parcel->agrarian_reform_beneficiary ? 'Yes' : 'No' }}</p>
                            </div>
                            @if($parcel->ownership_document_code)
                            <div>
                                <p class="text-xs text-gray-400 mb-0.5">Ownership Document Code</p>
                                <p class="font-medium text-gray-800 dark:text-gray-100">{{ $parcel->ownership_document_code }}</p>
                            </div>
                            @endif
                            @if($parcel->remarks)
                            <div class="col-span-2">
                                <p class="text-xs text-gray-400 mb-0.5">Remarks</p>
                                <p class="font-medium text-gray-800 dark:text-gray-100">{{ $parcel->remarks }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            {{-- Coconut Farm Profile --}}
            @if($farmer->coconutFarmProfile)
            @php $ccp = $farmer->coconutFarmProfile; @endphp
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-5">
                <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                    Coconut Farm Profile Supplement
                </h3>

                <div class="grid grid-cols-2 gap-4 text-sm mb-4">
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Land Holding Status</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100 capitalize">{{ str_replace('_', ' ', $ccp->coconut_land_holding_status ?? '—') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Farm Location</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100">
                            {{ $ccp->coconut_farm_location_barangay ?? '' }}
                            @if($ccp->coconut_farm_location_municipality), {{ $ccp->coconut_farm_location_municipality }}@endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Total Land Area</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100">{{ $ccp->land_area_absolute ? $ccp->land_area_absolute . ' ha' : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Area Classification</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100 capitalize">{{ str_replace('_', ' ', $ccp->area_classification ?? '—') }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Coconut Area</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100">{{ $ccp->land_area_coconut ? $ccp->land_area_coconut . ' ha' : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Intercrop Area</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100">{{ $ccp->land_area_intercrop ? $ccp->land_area_intercrop . ' ha' : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Other Crop Area</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100">{{ $ccp->land_area_other_crop ? $ccp->land_area_other_crop . ' ha' : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Idle Area</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100">{{ $ccp->land_area_idle ? $ccp->land_area_idle . ' ha' : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Distance to Market</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100">{{ $ccp->distance_to_market_km ? $ccp->distance_to_market_km . ' km' : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400 mb-1">Harvesting Cycle</p>
                        <p class="font-medium text-gray-800 dark:text-gray-100 capitalize">{{ str_replace('_', ' ', $ccp->coco_harvesting_cycle ?? '—') }}</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-1.5 mb-4">
                    @if($ccp->organic_certified)
                        <span class="px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full text-xs">Organic Certified</span>
                    @endif
                    @if($ccp->gap_certified)
                        <span class="px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-full text-xs">GAP Certified</span>
                    @endif
                    @if($ccp->processing_dryer)
                        <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full text-xs">Dryer {{ $ccp->processing_dryer_specify ? '(' . $ccp->processing_dryer_specify . ')' : '' }}</span>
                    @endif
                    @if($ccp->processing_charcoal_kiln)
                        <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full text-xs">Charcoal Kiln</span>
                    @endif
                    @if($ccp->processing_decort_machine)
                        <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 rounded-full text-xs">Decorticating Machine</span>
                    @endif
                    @if($ccp->processing_others)
                        <span class="px-2 py-0.5 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-xs">{{ $ccp->processing_others_specify ?? 'Other Processing' }}</span>
                    @endif
                </div>

                {{-- Coconut Tree Records --}}
                @if($ccp->coconutTreeRecords && $ccp->coconutTreeRecords->count() > 0)
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wide mb-2">Coconut Tree Records</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="text-left text-gray-400 border-b border-gray-200 dark:border-gray-700">
                                    <th class="py-1.5 pr-2 font-medium">Variety</th>
                                    <th class="py-1.5 pr-2 font-medium">Year Planted</th>
                                    <th class="py-1.5 pr-2 font-medium">No. of Trees</th>
                                    <th class="py-1.5 pr-2 font-medium">Avg. Nut/Tree/Year</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ccp->coconutTreeRecords as $tree)
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="py-1.5 pr-2 text-gray-700 dark:text-gray-200">{{ $tree->variety_code ?? '—' }}</td>
                                    <td class="py-1.5 pr-2 text-gray-700 dark:text-gray-200">{{ $tree->year_planted ?? '—' }}</td>
                                    <td class="py-1.5 pr-2 text-gray-700 dark:text-gray-200">{{ $tree->no_of_trees ?? '—' }}</td>
                                    <td class="py-1.5 pr-2 text-gray-700 dark:text-gray-200">{{ $tree->ave_nut_per_tree_year ?? '—' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                {{-- Farm Income Records --}}
                @if($ccp->farmIncomeRecords && $ccp->farmIncomeRecords->count() > 0)
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <p class="text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wide mb-2">Farm Income &amp; Expenses</p>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="text-left text-gray-400 border-b border-gray-200 dark:border-gray-700">
                                    <th class="py-1.5 pr-2 font-medium">Income Type</th>
                                    <th class="py-1.5 pr-2 font-medium">Qty/ha/yr</th>
                                    <th class="py-1.5 pr-2 font-medium">Unit Price</th>
                                    <th class="py-1.5 pr-2 font-medium">Expense Type</th>
                                    <th class="py-1.5 pr-2 font-medium">Expense Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ccp->farmIncomeRecords as $income)
                                <tr class="border-b border-gray-100 dark:border-gray-800">
                                    <td class="py-1.5 pr-2 text-gray-700 dark:text-gray-200">{{ $income->income_type_code ?? '—' }}</td>
                                    <td class="py-1.5 pr-2 text-gray-700 dark:text-gray-200">{{ $income->quantity_per_hectare_year ?? '—' }} {{ $income->unit ?? '' }}</td>
                                    <td class="py-1.5 pr-2 text-gray-700 dark:text-gray-200">{{ $income->unit_price ? '₱' . number_format($income->unit_price, 2) : '—' }}</td>
                                    <td class="py-1.5 pr-2 text-gray-700 dark:text-gray-200">{{ $income->expense_type_code ?? '—' }}</td>
                                    <td class="py-1.5 pr-2 text-gray-700 dark:text-gray-200">{{ $income->expense_amount ? '₱' . number_format($income->expense_amount, 2) : '—' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                @if($ccp->owner_or_tenant_lastname || $ccp->owner_or_tenant_firstname)
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 text-sm">
                    <p class="text-xs text-gray-400 mb-1">Owner/Tenant</p>
                    <p class="font-medium text-gray-800 dark:text-gray-100">
                        {{ $ccp->owner_or_tenant_firstname }} {{ $ccp->owner_or_tenant_middlename }} {{ $ccp->owner_or_tenant_lastname }} {{ $ccp->owner_or_tenant_extension }}
                    </p>
                </div>
                @endif
            </div>
            @endif

        </div>

        {{-- Right: Sidebar --}}
        <div class="space-y-4">

            {{-- Status --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-5">
                <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">Status</h3>
                @php $statusClass = $farmer->status === 'active' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400'; @endphp
                <span class="px-3 py-1.5 rounded-full text-sm font-medium {{ $statusClass }}">
                    {{ ucfirst($farmer->status) }}
                </span>
                @if($farmer->registration_status && $farmer->registration_status !== 'approved')
                    @php $regClass = $farmer->registration_status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-accent/10 text-accent border border-accent/30'; @endphp
                    <span class="ml-2 px-3 py-1.5 rounded-full text-sm font-medium {{ $regClass }}">
                        {{ ucfirst($farmer->registration_status) }}
                    </span>
                    @if($farmer->registration_status === 'rejected' && $farmer->registration_rejection_reason)
                        <p class="text-xs text-red-500 mt-2">{{ $farmer->registration_rejection_reason }}</p>
                    @endif
                @endif
                <div class="mt-4 text-xs text-gray-400 space-y-1">
                    <p>Registered: {{ $farmer->created_at->format('M d, Y') }}</p>
                    <p>By: {{ $farmer->registeredBy?->name ?? '—' }}</p>
                </div>
            </div>

            {{-- Emergency Contact --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-5">
                <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">Emergency Contact</h3>
                <p class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $farmer->emergency_contact_name ?? '—' }}</p>
                <p class="text-xs text-gray-400 mt-1">{{ $farmer->emergency_contact_number ?? '' }}</p>
            </div>

            {{-- Government ID --}}
            @if($farmer->has_government_id)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-5">
                    <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">Government ID</h3>
                    <p class="text-xs text-gray-400 dark:text-gray-400 mb-1">Type</p>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $farmer->government_id_type ?? '—' }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-400 mb-1 mt-2">ID Number</p>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $farmer->government_id_number ?? '—' }}</p>
                </div>
            @endif

            {{-- Farmers Association --}}
            @if($farmer->is_farmers_association_member)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-border-soft dark:border-gray-700 p-5">
                    <h3 class="text-base font-bold text-gray-800 dark:text-gray-100 mb-3 pb-2 border-b border-gray-200 dark:border-gray-700">Farmers Association</h3>
                    <p class="text-sm font-medium text-gray-800 dark:text-gray-100">{{ $farmer->farmers_association_name ?? '—' }}</p>
                </div>
            @endif

        </div>
    </div>

@endsection
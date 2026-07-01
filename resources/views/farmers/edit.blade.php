@extends('layouts.app')

@section('content')

<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-2xl font-bold text-gray-800">Edit Farmer</h2>
        <p class="text-gray-500 text-sm mt-1">{{ $farmer->reference_number }}</p>
    </div>
    <a href="{{ route('farmers.show', $farmer) }}"
       class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium px-4 py-2 rounded-md transition">
        ← Back
    </a>
</div>

@if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-600 text-sm rounded-md p-3 mb-4">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('farmers.update', $farmer) }}">
@csrf @method('PUT')

{{-- Enrollment Type --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 mb-4">
    <div class="flex items-center gap-6">
        <p class="text-sm font-medium text-gray-700">Enrollment Type:</p>
        <label class="flex items-center gap-2 text-sm">
            <input type="radio" name="enrollment_type" value="new"
                {{ $farmer->enrollment_type == 'new' ? 'checked' : '' }} class="accent-primary"> New
        </label>
        <label class="flex items-center gap-2 text-sm">
            <input type="radio" name="enrollment_type" value="updating"
                {{ $farmer->enrollment_type == 'updating' ? 'checked' : '' }} class="accent-primary"> Updating
        </label>
    </div>
</div>

{{-- PART I: PERSONAL INFORMATION --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 mb-4">
    <h3 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">PART I: PERSONAL INFORMATION</h3>

    <div class="grid grid-cols-3 gap-4 mb-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Surname <span class="text-red-500">*</span></label>
            <input type="text" name="surname" value="{{ old('surname', $farmer->surname) }}" required
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">First Name <span class="text-red-500">*</span></label>
            <input type="text" name="first_name" value="{{ old('first_name', $farmer->first_name) }}" required
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Middle Name</label>
            <input type="text" name="middle_name" value="{{ old('middle_name', $farmer->middle_name) }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4 mb-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Extension Name</label>
            <input type="text" name="extension_name" value="{{ old('extension_name', $farmer->extension_name) }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Sex <span class="text-red-500">*</span></label>
            <select name="sex" required class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="male"   {{ $farmer->sex == 'male'   ? 'selected' : '' }}>Male</option>
                <option value="female" {{ $farmer->sex == 'female' ? 'selected' : '' }}>Female</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Date of Birth <span class="text-red-500">*</span></label>
            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $farmer->date_of_birth?->format('Y-m-d')) }}" required
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Place of Birth</label>
            <input type="text" name="place_of_birth" value="{{ old('place_of_birth', $farmer->place_of_birth) }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Mobile Number</label>
            <input type="text" name="mobile_number" value="{{ old('mobile_number', $farmer->mobile_number) }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">House/Lot/Bldg. No./Purok</label>
            <input type="text" name="house_lot_number" value="{{ old('house_lot_number', $farmer->house_lot_number) }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Street/Sitio/Subdv.</label>
            <input type="text" name="street" value="{{ old('street', $farmer->street) }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4 mb-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Barangay</label>
            <select name="barangay_id" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">Select Barangay</option>
                @foreach($barangays as $barangay)
                    <option value="{{ $barangay->id }}" {{ $farmer->barangay_id == $barangay->id ? 'selected' : '' }}>
                        {{ $barangay->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Municipality/City</label>
            <input type="text" name="municipality" value="{{ old('municipality', $farmer->municipality) }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Province</label>
            <input type="text" name="province" value="{{ old('province', $farmer->province) }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Religion</label>
            <input type="text" name="religion" value="{{ old('religion', $farmer->religion) }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Civil Status</label>
            <select name="civil_status" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">Select</option>
                <option value="single"    {{ $farmer->civil_status == 'single'    ? 'selected' : '' }}>Single</option>
                <option value="married"   {{ $farmer->civil_status == 'married'   ? 'selected' : '' }}>Married</option>
                <option value="widowed"   {{ $farmer->civil_status == 'widowed'   ? 'selected' : '' }}>Widowed</option>
                <option value="separated" {{ $farmer->civil_status == 'separated' ? 'selected' : '' }}>Separated</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Name of Spouse (if married)</label>
            <input type="text" name="spouse_name" value="{{ old('spouse_name', $farmer->spouse_name) }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Mother's Maiden Name</label>
            <input type="text" name="mother_maiden_name" value="{{ old('mother_maiden_name', $farmer->mother_maiden_name) }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
    </div>

    <div class="mb-4">
        <label class="block text-xs font-medium text-gray-600 mb-2">Household Head?</label>
        <div class="flex gap-4">
            <label class="flex items-center gap-2 text-sm">
                <input type="radio" name="is_household_head" value="1" class="accent-primary"
                    {{ $farmer->is_household_head ? 'checked' : '' }}> Yes
            </label>
            <label class="flex items-center gap-2 text-sm">
                <input type="radio" name="is_household_head" value="0" class="accent-primary"
                    {{ !$farmer->is_household_head ? 'checked' : '' }}> No
            </label>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4 mb-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">No. of Household Members</label>
            <input type="number" name="household_members_count" value="{{ old('household_members_count', $farmer->household_members_count) }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">No. of Male</label>
            <input type="number" name="household_male_count" value="{{ old('household_male_count', $farmer->household_male_count) }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">No. of Female</label>
            <input type="number" name="household_female_count" value="{{ old('household_female_count', $farmer->household_female_count) }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
    </div>

    {{-- Education --}}
    <div class="mb-4">
        <label class="block text-xs font-medium text-gray-600 mb-2">Highest Formal Education</label>
        <div class="grid grid-cols-3 gap-2">
            @foreach([
                'pre_school' => 'Pre-school',
                'elementary' => 'Elementary',
                'high_school_non_k12' => 'High School (non K-12)',
                'junior_high_k12' => 'Junior High School (K-12)',
                'senior_high_k12' => 'Senior High School (K-12)',
                'vocational' => 'Vocational',
                'college' => 'College',
                'post_graduate' => 'Post-graduate',
                'none' => 'None',
            ] as $value => $label)
            <label class="flex items-center gap-2 text-sm">
                <input type="radio" name="highest_education" value="{{ $value }}" class="accent-primary"
                    {{ $farmer->highest_education == $value ? 'checked' : '' }}>
                {{ $label }}
            </label>
            @endforeach
        </div>
    </div>

    {{-- Special Classifications --}}
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="space-y-2">
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_pwd" value="1" class="accent-primary"
                    {{ $farmer->is_pwd ? 'checked' : '' }}> Person with Disability (PWD)
            </label>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_4ps_beneficiary" value="1" class="accent-primary"
                    {{ $farmer->is_4ps_beneficiary ? 'checked' : '' }}> 4P's Beneficiary
            </label>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_indigenous" value="1" class="accent-primary"
                    {{ $farmer->is_indigenous ? 'checked' : '' }}> Member of Indigenous Group
            </label>
        </div>
        <div class="space-y-2">
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="has_government_id" value="1" class="accent-primary"
                    {{ $farmer->has_government_id ? 'checked' : '' }}> With Government ID
            </label>
            <input type="text" name="government_id_type" value="{{ old('government_id_type', $farmer->government_id_type) }}"
                   placeholder="ID Type"
                   class="w-full border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
            <input type="text" name="government_id_number" value="{{ old('government_id_number', $farmer->government_id_number) }}"
                   placeholder="ID Number"
                   class="w-full border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
    </div>

    <div class="mb-4">
        <label class="flex items-center gap-2 text-sm mb-2">
            <input type="checkbox" name="is_farmers_association_member" value="1" class="accent-primary"
                {{ $farmer->is_farmers_association_member ? 'checked' : '' }}>
            Member of any Farmers Association/Cooperative
        </label>
        <input type="text" name="farmers_association_name" value="{{ old('farmers_association_name', $farmer->farmers_association_name) }}"
               placeholder="If yes, specify association name"
               class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Emergency Contact Name</label>
            <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name', $farmer->emergency_contact_name) }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Emergency Contact Number</label>
            <input type="text" name="emergency_contact_number" value="{{ old('emergency_contact_number', $farmer->emergency_contact_number) }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
    </div>
</div>

{{-- PART II: FARM PROFILE --}}
<div class="bg-white rounded-lg shadow-sm border border-gray-100 p-5 mb-4">
    <h3 class="text-base font-bold text-gray-800 mb-4 pb-2 border-b border-gray-200">PART II: FARM PROFILE</h3>

    <div class="mb-4">
        <label class="block text-xs font-medium text-gray-600 mb-2">Main Livelihood</label>
        <div class="flex gap-6">
            @foreach(['farmer' => 'Farmer', 'farmworker' => 'Farmworker/Laborer', 'fisherfolk' => 'Fisherfolk', 'agri_youth' => 'Agri Youth'] as $value => $label)
            <label class="flex items-center gap-2 text-sm">
                <input type="radio" name="main_livelihood" value="{{ $value }}" class="accent-primary"
                    {{ $farmer->main_livelihood == $value ? 'checked' : '' }}>
                {{ $label }}
            </label>
            @endforeach
        </div>
    </div>

    <div class="mb-4">
        <label class="block text-xs font-medium text-gray-600 mb-2">Type of Farming Activity</label>
        <div class="grid grid-cols-2 gap-2">
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="farming_rice" value="1" class="accent-primary"
                    {{ $farmer->farming_rice ? 'checked' : '' }}> Rice
            </label>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="farming_corn" value="1" class="accent-primary"
                    {{ $farmer->farming_corn ? 'checked' : '' }}> Corn
            </label>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="farming_other_crops" value="1" class="accent-primary"
                    {{ $farmer->farming_other_crops ? 'checked' : '' }}> Other Crops
            </label>
            <input type="text" name="farming_other_crops_specify"
                   value="{{ old('farming_other_crops_specify', $farmer->farming_other_crops_specify) }}"
                   placeholder="Please specify"
                   class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="farming_livestock" value="1" class="accent-primary"
                    {{ $farmer->farming_livestock ? 'checked' : '' }}> Livestock
            </label>
            <input type="text" name="farming_livestock_specify"
                   value="{{ old('farming_livestock_specify', $farmer->farming_livestock_specify) }}"
                   placeholder="Please specify"
                   class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="farming_poultry" value="1" class="accent-primary"
                    {{ $farmer->farming_poultry ? 'checked' : '' }}> Poultry
            </label>
            <input type="text" name="farming_poultry_specify"
                   value="{{ old('farming_poultry_specify', $farmer->farming_poultry_specify) }}"
                   placeholder="Please specify"
                   class="border border-gray-300 rounded-md px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
    </div>

    <div class="mb-4">
        <label class="block text-xs font-medium text-gray-600 mb-2">Land Holding Status</label>
        <div class="grid grid-cols-3 gap-2">
            @foreach(['owner' => 'Owner', 'owner_tiller' => 'Owner-Tiller', 'grower' => 'Grower', 'tenant' => 'Tenant', 'tenant_worker' => 'Tenant-Worker', 'worker_laborer' => 'Worker-Laborer', 'others' => 'Others'] as $value => $label)
            <label class="flex items-center gap-2 text-sm">
                <input type="radio" name="land_holding_status" value="{{ $value }}" class="accent-primary"
                    {{ $farmer->land_holding_status == $value ? 'checked' : '' }}>
                {{ $label }}
            </label>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4 mb-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Farm Location — Province</label>
            <input type="text" name="farm_location_province"
                   value="{{ old('farm_location_province', $farmer->farm_location_province) }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Municipality</label>
            <input type="text" name="farm_location_municipality"
                   value="{{ old('farm_location_municipality', $farmer->farm_location_municipality) }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Barangay</label>
            <input type="text" name="farm_location_barangay"
                   value="{{ old('farm_location_barangay', $farmer->farm_location_barangay) }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Land Ownership Area (in Hectares)</label>
            <input type="number" step="0.0001" name="land_area_hectares"
                   value="{{ old('land_area_hectares', $farmer->land_area_hectares) }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
            <select name="status" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="active"   {{ $farmer->status == 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ $farmer->status == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Gross Annual Income — Farming (₱)</label>
            <input type="number" step="0.01" name="gross_annual_income_farming"
                   value="{{ old('gross_annual_income_farming', $farmer->gross_annual_income_farming) }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">Gross Annual Income — Non-Farming (₱)</label>
            <input type="number" step="0.01" name="gross_annual_income_non_farming"
                   value="{{ old('gross_annual_income_non_farming', $farmer->gross_annual_income_non_farming) }}"
                   class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary"/>
        </div>
    </div>
</div>

{{-- Submit --}}
<div class="flex items-center gap-3">
    <button type="submit"
            class="bg-primary hover:bg-primary-dark text-white font-semibold px-6 py-2.5 rounded-md transition text-sm">
        Save Changes
    </button>
    <a href="{{ route('farmers.show', $farmer) }}"
       class="border border-gray-300 text-gray-600 hover:bg-gray-50 font-medium px-6 py-2.5 rounded-md transition text-sm">
        Cancel
    </a>
</div>

</form>
@endsection
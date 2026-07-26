@extends('layouts.app')

@section('content')

    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Edit Farmer</h2>
            <p class="text-gray-500 text-sm mt-1">{{ $farmer->reference_number }}</p>
        </div>
        <a href="{{ route('farmers.show', $farmer) }}"
            class="border border-gray-300 text-gray-600 hover:bg-gray-50 font-medium px-5 py-2 rounded-md text-sm">
            ← Back
        </a>
    </div>

    @if ($errors->any())
        <div class="max-w-[850px] mx-auto bg-red-50 border border-red-200 text-red-600 text-sm rounded-md p-3 mb-4">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <style>
                input[type="radio"].cb {
            appearance: none; -webkit-appearance: none;
            width: 11px; height: 11px;
            border: 1px solid #333; border-radius: 0;
            background: #fff; cursor: pointer;
            position: absolute; margin: 0; padding: 0;
        }
        input[type="radio"].cb:checked::after {
            content: "\2713";
            position: absolute; top: -4px; left: 0;
            font-size: 12px; font-weight: bold; line-height: 1; color: #000;
        }
        .rsbsa-page {
            position: relative;
            width: 850px;
            margin: 0 auto 24px auto;
            background: #fff;
            box-shadow: 0 2px 12px rgba(0, 0, 0, .15);
        }

        .rsbsa-page img {
            width: 100%;
            display: block;
            pointer-events: none;
            user-select: none;
        }

        .f {
            position: absolute;
            background: rgba(255, 255, 255, 0.01);
            border: none;
            outline: none;
            font-family: Arial, sans-serif;
            font-size: 11.5px;
            color: #000;
            padding: 0 2px;
            box-sizing: border-box;
        }

        .f:focus {
            background: rgba(255, 255, 180, 0.8);
            outline: 1px solid #2563eb;
        }

        .cb {
            position: absolute;
            width: 13px;
            height: 13px;
            cursor: pointer;
            accent-color: #111;
            margin: 0;
        }

        .dg {
            position: absolute;
            text-align: center;
            border: 1px solid #999;
            background: rgba(255, 255, 255, 0.4);
            font-size: 10px;
            font-weight: bold;
            padding: 0;
            outline: none;
        }

        .dg:focus {
            background: rgba(255, 255, 180, 0.8);
        }

        .photo-upload {
            position: absolute;
            cursor: pointer;
        }

        .photo-upload input[type=file] {
            position: absolute;
            inset: 0;
            opacity: 0;
            cursor: pointer;
        }

        .photo-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: none;
        }
    </style>

    @php
        $dobValue = old('date_of_birth', $farmer->date_of_birth?->format('Y-m-d'));
        $pobParts = old('place_of_birth', $farmer->place_of_birth) ? explode(', ', old('place_of_birth', $farmer->place_of_birth)) : [];
        $religionVal = old('religion', $farmer->religion);
        $isOtherReligion = $religionVal && !in_array($religionVal, ['Christianity', 'Islam']);

        $dateAdminVal = old('date_administered', $farmer->date_administered?->format('Y-m-d'));
        $dateAdminDigits = $dateAdminVal ? str_split(\Carbon\Carbon::parse($dateAdminVal)->format('mdY')) : [];
        $refDigits = str_split(preg_replace('/\D/', '', old('reference_number', $farmer->reference_number) ?? ''));
    @endphp

    <form method="POST" action="{{ route('farmers.update', $farmer) }}" enctype="multipart/form-data" id="farmerForm"
        autocomplete="off">
        @csrf
        @method('PUT')

        <div class="rsbsa-page">
            <img src="{{ asset('images/rsbsa_page-1.jpg') }}" alt="RSBSA Page 1">

            <div class="photo-upload" style="top:2.81%;left:74.42%;width:21.45%;height:13.93%;">
                <input type="file" name="photo" id="photoInput" accept="image/*" autocomplete="off">
                <img id="photoPreview" class="photo-preview" alt="Preview"
                    src="{{ $farmer->photo ? asset('storage/' . $farmer->photo) : '' }}"
                    style="display:{{ $farmer->photo ? 'block' : 'none' }};">
            </div>

            <input type="checkbox" class="cb" id="cb_new" style="top:12.81%;left:18.26%;" {{ old('enrollment_type', $farmer->enrollment_type) === 'new' ? 'checked' : '' }}
                onchange="if(this.checked)document.getElementById('cb_upd').checked=false; document.getElementById('enroll_type').value='new';">
            <input type="checkbox" class="cb" id="cb_upd" style="top:12.78%;left:25.39%;" {{ old('enrollment_type', $farmer->enrollment_type) === 'updating' ? 'checked' : '' }}
                onchange="if(this.checked){document.getElementById('cb_new').checked=false; document.getElementById('enroll_type').value='updating';}">
            <input type="hidden" name="enrollment_type" id="enroll_type"
                value="{{ old('enrollment_type', $farmer->enrollment_type) }}">

            {{-- DATE ADMINISTERED (8 real, individually-typeable boxes: MMDDYYYY) --}}
            <div style="position:absolute;top:12.56%;left:35.54%;width:16%;height:1.24%;display:flex;">
                @for($i = 0; $i < 8; $i++)
                    <input type="text" maxlength="1"
                        style="width:12%;height:100%;text-align:center;border:1px solid #999;background:rgba(255,255,255,0.4);font-size:10px;font-weight:bold;padding:0;outline:none;"
                        class="dateadmin-d" autocomplete="off" value="{{ $dateAdminDigits[$i] ?? '' }}">
                @endfor
            </div>
            <input type="hidden" name="date_administered" id="dateAdminH" value="{{ $dateAdminVal }}">

            {{-- REFERENCE NUMBER (15 real, individually-typeable boxes) --}}
            <div style="position:absolute;top:14.64%;left:18.38%;width:33%;height:1.33%;display:flex;">
                @for($i = 0; $i < 15; $i++)
                    <input type="text" maxlength="1"
                        style="width:6.6%;height:100%;text-align:center;border:1px solid #999;background:rgba(255,255,255,0.4);font-size:10px;font-weight:bold;padding:0;outline:none;"
                        class="refnum-d" autocomplete="off" value="{{ $refDigits[$i] ?? '' }}">
                @endfor
            </div>
            <input type="hidden" name="reference_number" id="refH"
                value="{{ old('reference_number', $farmer->reference_number) }}">

            <input type="text" name="surname" id="mainSurname" value="{{ old('surname', $farmer->surname) }}" required
                autocomplete="off" class="f" style="top:18.64%;left:5.58%;width:42.06%;height:2.05%;">
            <input type="text" name="first_name" id="mainFirstName" value="{{ old('first_name', $farmer->first_name) }}"
                required autocomplete="off" class="f" style="top:18.49%;left:52.41%;width:42.06%;height:2.05%;">
            <input type="text" name="middle_name" id="mainMiddleName" value="{{ old('middle_name', $farmer->middle_name) }}"
                autocomplete="off" class="f" style="top:21.40%;left:5.62%;width:42.06%;height:2.05%;">
            <input type="text" name="extension_name" id="mainExtName"
                value="{{ old('extension_name', $farmer->extension_name) }}" autocomplete="off" class="f"
                style="top:21.44%;left:50.98%;width:9.79%;height:2.05%;">

            <input type="checkbox" class="cb" id="sex_m" style="top:23.50%;left:76.56%;"
                onchange="if(this.checked){document.getElementById('sex_f').checked=false;document.getElementById('sex_h').value='male';}"
                {{ old('sex', $farmer->sex) == 'male' ? 'checked' : '' }}>
            <input type="checkbox" class="cb" id="sex_f" style="top:23.44%;left:84.74%;"
                onchange="if(this.checked){document.getElementById('sex_m').checked=false;document.getElementById('sex_h').value='female';}"
                {{ old('sex', $farmer->sex) == 'female' ? 'checked' : '' }}>
            <input type="hidden" name="sex" id="sex_h" value="{{ old('sex', $farmer->sex) }}" required>

            <input type="text" name="house_lot_number" value="{{ old('house_lot_number', $farmer->house_lot_number) }}"
                autocomplete="off" class="f" style="top:25.54%;left:11.90%;width:26.46%;height:1.63%;">
            <input type="text" name="street" value="{{ old('street', $farmer->street) }}" autocomplete="off" class="f"
                style="top:25.59%;left:40.18%;width:26.46%;height:1.63%;">
            <select name="barangay_id" class="f"
                style="top:25.55%;left:68.38%;width:26.46%;height:1.63%;background:rgba(255,255,255,0.9);cursor:pointer;">
                <option value="">Select</option>
                @foreach($barangays as $b)
                    <option value="{{ $b->id }}" {{ old('barangay_id', $farmer->barangay_id) == $b->id ? 'selected' : '' }}>
                        {{ $b->name }}</option>
                @endforeach
            </select>

            <input type="text" name="municipality" value="{{ old('municipality', $farmer->municipality) }}"
                autocomplete="off" class="f" style="top:28.69%;left:11.79%;width:26.46%;height:1.63%;">
            <input type="text" name="province" value="{{ old('province', $farmer->province) }}" autocomplete="off" class="f"
                style="top:28.69%;left:40.18%;width:26.46%;height:1.63%;">
            <input type="text" name="region" value="{{ old('region', $farmer->region) }}" autocomplete="off" class="f"
                style="top:28.75%;left:68.38%;width:26.46%;height:1.63%;">

            <div style="position:absolute;top:32.78%;left:5.39%;width:20%;height:1.09%;display:flex;" id="mobBoxes"></div>
            <input type="text" id="mobReal" maxlength="11" inputmode="numeric"
                value="{{ old('mobile_number', $farmer->mobile_number) }}"
                style="position:absolute;top:32.78%;left:5.39%;width:20%;height:1.09%;opacity:0;border:none;">
            <input type="hidden" name="mobile_number" id="mobH" value="{{ old('mobile_number', $farmer->mobile_number) }}">

            <div style="position:absolute;top:32.72%;left:29.62%;width:18%;height:1.19%;display:flex;" id="landBoxes"></div>
            <input type="text" id="landReal" maxlength="10" inputmode="numeric"
                value="{{ old('landline_number', $farmer->landline_number) }}"
                style="position:absolute;top:32.72%;left:29.62%;width:18%;height:1.19%;opacity:0;border:none;">
            <input type="hidden" name="landline_number" id="landH"
                value="{{ old('landline_number', $farmer->landline_number) }}">

            @php
                $eduMap = [
                    'pre_school' => ['top' => '33.57%', 'left' => '52.98%'],
                    'junior_high_k12' => ['top' => '33.57%', 'left' => '67.82%'],
                    'vocational' => ['top' => '33.58%', 'left' => '83.89%'],
                    'elementary' => ['top' => '34.81%', 'left' => '53.00%'],
                    'senior_high_k12' => ['top' => '34.85%', 'left' => '67.77%'],
                    'post_graduate' => ['top' => '34.86%', 'left' => '83.85%'],
                    'high_school_non_k12' => ['top' => '36.20%', 'left' => '52.98%'],
                    'college' => ['top' => '36.20%', 'left' => '67.85%'],
                    'none' => ['top' => '36.21%', 'left' => '83.88%'],
                ];
                $eduVal = old('highest_education', $farmer->highest_education);
            @endphp
            @foreach($eduMap as $val => $pos)
                <input type="checkbox" class="cb edu-cb" data-val="{{ $val }}"
                    style="top:{{ $pos['top'] }};left:{{ $pos['left'] }};" {{ $eduVal == $val ? 'checked' : '' }}>
            @endforeach
            <input type="hidden" name="highest_education" id="edu_h" value="{{ $eduVal }}">

            <div style="position:absolute;top:36.06%;left:5.45%;width:17.5%;height:1.45%;display:flex;" id="dobBoxes"></div>
            <input type="text" id="dobReal" maxlength="8" inputmode="numeric"
                value="{{ $dobValue ? \Carbon\Carbon::parse($dobValue)->format('mdY') : '' }}"
                style="position:absolute;top:36.06%;left:5.45%;width:17.5%;height:1.45%;opacity:0;border:none;">
            <input type="hidden" name="date_of_birth" id="dobH" value="{{ $dobValue }}">

            <input type="text" id="pob1" placeholder="City/Municipality" value="{{ $pobParts[0] ?? '' }}" autocomplete="off"
                class="f" style="top:35.74%;left:25.54%;width:23.42%;height:0.91%;font-size:9px;">
            <input type="text" id="pob2" placeholder="Province/State" value="{{ $pobParts[1] ?? '' }}" autocomplete="off"
                class="f" style="top:37.03%;left:25.43%;width:11.29%;height:0.91%;font-size:9px;">
            <input type="text" id="pob3" placeholder="Country" value="{{ $pobParts[2] ?? 'Philippines' }}"
                autocomplete="off" class="f" style="top:37.04%;left:37.49%;width:11.29%;height:0.91%;font-size:9px;">
            <input type="hidden" name="place_of_birth" id="pobH"
                value="{{ old('place_of_birth', $farmer->place_of_birth) }}">

            <input type="checkbox" class="cb" id="pwd_y" style="top:38.52%;left:75.99%;"
                onchange="if(this.checked){document.getElementById('pwd_n').checked=false;document.getElementById('pwd_h').value='1';}"
                {{ old('is_pwd', $farmer->is_pwd) == '1' ? 'checked' : '' }}>
            <input type="checkbox" class="cb" id="pwd_n" style="top:38.52%;left:83.11%;"
                onchange="if(this.checked){document.getElementById('pwd_y').checked=false;document.getElementById('pwd_h').value='0';}"
                {{ old('is_pwd', $farmer->is_pwd) == '0' ? 'checked' : '' }}>
            <input type="hidden" name="is_pwd" id="pwd_h" value="{{ old('is_pwd', $farmer->is_pwd ? '1' : '0') }}">

            <input type="checkbox" class="cb rel-cb" data-val="Christianity" style="top:40.30%;left:13.03%;" {{ $religionVal == 'Christianity' ? 'checked' : '' }}>
            <input type="checkbox" class="cb rel-cb" data-val="Islam" style="top:40.39%;left:22.96%;" {{ $religionVal == 'Islam' ? 'checked' : '' }}>
            <input type="checkbox" class="cb rel-cb" data-val="others" style="top:40.36%;left:28.92%;" {{ $isOtherReligion ? 'checked' : '' }}>
            <input type="text" id="religionOther" name="religion_other"
                value="{{ $isOtherReligion ? $religionVal : old('religion_other') }}" autocomplete="off" class="f"
                style="top:40.22%;left:39.73%;width:9.61%;height:0.91%;display:{{ $isOtherReligion ? 'block' : 'none' }};">
            <input type="hidden" name="religion" id="religion_h" value="{{ $isOtherReligion ? 'others' : $religionVal }}">

            <input type="checkbox" class="cb" id="fps_y" style="top:41.09%;left:75.69%;"
                onchange="if(this.checked){document.getElementById('fps_n').checked=false;document.getElementById('fps_h').value='1';}"
                {{ old('is_4ps_beneficiary', $farmer->is_4ps_beneficiary) == '1' ? 'checked' : '' }}>
            <input type="checkbox" class="cb" id="fps_n" style="top:41.10%;left:82.66%;"
                onchange="if(this.checked){document.getElementById('fps_y').checked=false;document.getElementById('fps_h').value='0';}"
                {{ old('is_4ps_beneficiary', $farmer->is_4ps_beneficiary) == '0' ? 'checked' : '' }}>
            <input type="hidden" name="is_4ps_beneficiary" id="fps_h"
                value="{{ old('is_4ps_beneficiary', $farmer->is_4ps_beneficiary ? '1' : '0') }}">

            @php $csMap = ['single' => '15.74%', 'married' => '23.36%', 'widowed' => '31.52%', 'separated' => '40.66%'];
            $csVal = old('civil_status', $farmer->civil_status); @endphp
            @foreach($csMap as $val => $left)
                <input type="checkbox" class="cb cs-cb" data-val="{{ $val }}" style="top:42.47%;left:{{ $left }};" {{ $csVal == $val ? 'checked' : '' }}>
            @endforeach
            <input type="hidden" name="civil_status" id="cs_h" value="{{ $csVal }}">

            <input type="checkbox" class="cb" id="ind_y" style="top:42.76%;left:75.67%;"
                onchange="if(this.checked){document.getElementById('ind_n').checked=false;document.getElementById('ind_h').value='1';}"
                {{ old('is_indigenous', $farmer->is_indigenous) == '1' ? 'checked' : '' }}>
            <input type="checkbox" class="cb" id="ind_n" style="top:42.75%;left:82.66%;"
                onchange="if(this.checked){document.getElementById('ind_y').checked=false;document.getElementById('ind_h').value='0';}"
                {{ old('is_indigenous', $farmer->is_indigenous) == '0' ? 'checked' : '' }}>
            <input type="hidden" name="is_indigenous" id="ind_h"
                value="{{ old('is_indigenous', $farmer->is_indigenous ? '1' : '0') }}">
            <input type="text" name="indigenous_group_name"
                value="{{ old('indigenous_group_name', $farmer->indigenous_group_name) }}" autocomplete="off" class="f"
                style="top:44.07%;left:60.40%;width:33.99%;height:1.45%;">

            <input type="text" name="spouse_name" value="{{ old('spouse_name', $farmer->spouse_name) }}" autocomplete="off"
                class="f" style="top:44.84%;left:17.02%;width:31.72%;height:1.39%;">

            <input type="checkbox" class="cb" id="gid_y" style="top:46.62%;left:66.74%;"
                onchange="if(this.checked){document.getElementById('gid_n').checked=false;document.getElementById('gid_h').value='1';}"
                {{ old('has_government_id', $farmer->has_government_id) == '1' ? 'checked' : '' }}>
            <input type="checkbox" class="cb" id="gid_n" style="top:46.62%;left:73.87%;"
                onchange="if(this.checked){document.getElementById('gid_y').checked=false;document.getElementById('gid_h').value='0';}"
                {{ old('has_government_id', $farmer->has_government_id) == '0' ? 'checked' : '' }}>
            <input type="hidden" name="has_government_id" id="gid_h"
                value="{{ old('has_government_id', $farmer->has_government_id ? '1' : '0') }}">
            <input type="text" name="government_id_type"
                value="{{ old('government_id_type', $farmer->government_id_type) }}" autocomplete="off" class="f"
                style="top:47.70%;left:66.66%;width:28.02%;height:1.01%;">
            <input type="text" name="government_id_number"
                value="{{ old('government_id_number', $farmer->government_id_number) }}" autocomplete="off" class="f"
                style="top:48.90%;left:66.64%;width:28.02%;height:1.01%;">

            <input type="text" name="mother_maiden_name"
                value="{{ old('mother_maiden_name', $farmer->mother_maiden_name) }}" autocomplete="off" class="f"
                style="top:48.00%;left:17.00%;width:31.72%;height:1.39%;">

            <input type="checkbox" class="cb" id="hh_y" style="top:50.36%;left:22.57%;"
                onchange="if(this.checked){document.getElementById('hh_n').checked=false;document.getElementById('hh_h').value='1';document.getElementById('hhSubFields').style.display='none';}"
                {{ old('is_household_head', $farmer->is_household_head) == '1' ? 'checked' : '' }}>
            <input type="checkbox" class="cb" id="hh_n" style="top:50.41%;left:29.72%;"
                onchange="if(this.checked){document.getElementById('hh_y').checked=false;document.getElementById('hh_h').value='0';document.getElementById('hhSubFields').style.display='block';}"
                {{ old('is_household_head', $farmer->is_household_head) == '0' ? 'checked' : '' }}>
            <input type="hidden" name="is_household_head" id="hh_h"
                value="{{ old('is_household_head', $farmer->is_household_head ? '1' : '0') }}">

            <div id="hhSubFields"
                style="display:{{ old('is_household_head', $farmer->is_household_head) == '0' ? 'block' : 'none' }};">
                <input type="text" name="household_head_name"
                    value="{{ old('household_head_name', $farmer->household_head_name) }}" autocomplete="off" class="f"
                    style="top:51.86%;left:24.09%;width:24.67%;height:1.39%;">
                <input type="text" name="household_head_relationship"
                    value="{{ old('household_head_relationship', $farmer->household_head_relationship) }}"
                    autocomplete="off" class="f" style="top:53.76%;left:24.11%;width:24.67%;height:1.39%;">
            </div>
            <input type="text" name="household_members_count"
                value="{{ old('household_members_count', $farmer->household_members_count) }}" autocomplete="off" class="f"
                style="top:55.91%;left:25.78%;width:22.88%;height:1.39%;">
            <input type="text" name="household_male_count"
                value="{{ old('household_male_count', $farmer->household_male_count) }}" autocomplete="off" class="f"
                style="top:57.72%;left:13.19%;width:10.51%;height:1.39%;">
            <input type="text" name="household_female_count"
                value="{{ old('household_female_count', $farmer->household_female_count) }}" autocomplete="off" class="f"
                style="top:57.75%;left:37.94%;width:10.51%;height:1.39%;">

            <input type="checkbox" class="cb" id="asc_y" style="top:51.04%;left:84.31%;"
                onchange="if(this.checked){document.getElementById('asc_n').checked=false;document.getElementById('asc_h').value='1';}"
                {{ old('is_farmers_association_member', $farmer->is_farmers_association_member) == '1' ? 'checked' : '' }}>
            <input type="checkbox" class="cb" id="asc_n" style="top:51.04%;left:90.27%;"
                onchange="if(this.checked){document.getElementById('asc_y').checked=false;document.getElementById('asc_h').value='0';}"
                {{ old('is_farmers_association_member', $farmer->is_farmers_association_member) == '0' ? 'checked' : '' }}>
            <input type="hidden" name="is_farmers_association_member" id="asc_h"
                value="{{ old('is_farmers_association_member', $farmer->is_farmers_association_member ? '1' : '0') }}">
            <input type="text" name="farmers_association_name"
                value="{{ old('farmers_association_name', $farmer->farmers_association_name) }}" autocomplete="off"
                class="f" style="top:52.57%;left:60.28%;width:34.29%;height:1.25%;">

            <input type="text" name="emergency_contact_name"
                value="{{ old('emergency_contact_name', $farmer->emergency_contact_name) }}" autocomplete="off" class="f"
                style="top:55.61%;left:65.70%;width:28.97%;height:1.25%;">
            <div style="position:absolute;top:57.63%;left:65.69%;width:28%;height:1.74%;display:flex;" id="conBoxes"></div>
            <input type="text" id="conReal" maxlength="11" inputmode="numeric"
                value="{{ old('emergency_contact_number', $farmer->emergency_contact_number) }}"
                style="position:absolute;top:57.63%;left:65.69%;width:28%;height:1.74%;opacity:0;border:none;">
            <input type="hidden" name="emergency_contact_number" id="conH"
                value="{{ old('emergency_contact_number', $farmer->emergency_contact_number) }}">

            @php $lhMap = ['farmer' => '20.44%', 'farmworker' => '35.32%', 'fisherfolk' => '60.42%', 'agri_youth' => '80.50%'];
            $lhVal = old('main_livelihood', $farmer->main_livelihood); @endphp
            @foreach($lhMap as $val => $left)
                <input type="checkbox" class="cb lh-cb" data-val="{{ $val }}" style="top:61.93%;left:{{ $left }};" {{ $lhVal == $val ? 'checked' : '' }}>
            @endforeach
            <input type="hidden" name="main_livelihood" id="lh_h" value="{{ $lhVal }}">

            <input type="checkbox" class="cb" name="farming_rice" value="1" style="top:67.23%;left:5.60%;" {{ old('farming_rice', $farmer->farming_rice) ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="farming_corn" value="1" style="top:69.19%;left:5.56%;" {{ old('farming_corn', $farmer->farming_corn) ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="farming_other_crops" value="1" style="top:71.16%;left:5.63%;" {{ old('farming_other_crops', $farmer->farming_other_crops) ? 'checked' : '' }}>
            <input type="text" name="farming_other_crops_specify"
                value="{{ old('farming_other_crops_specify', $farmer->farming_other_crops_specify) }}" autocomplete="off"
                class="f" style="top:71.87%;left:16.89%;width:16.94%;height:1.03%;">
            <input type="checkbox" class="cb" name="farming_livestock" value="1" style="top:73.88%;left:5.51%;" {{ old('farming_livestock', $farmer->farming_livestock) ? 'checked' : '' }}>
            <input type="text" name="farming_livestock_specify"
                value="{{ old('farming_livestock_specify', $farmer->farming_livestock_specify) }}" autocomplete="off"
                class="f" style="top:74.74%;left:16.86%;width:16.94%;height:1.03%;">
            <input type="checkbox" class="cb" name="farming_poultry" value="1" style="top:76.50%;left:5.49%;" {{ old('farming_poultry', $farmer->farming_poultry) ? 'checked' : '' }}>
            <input type="text" name="farming_poultry_specify"
                value="{{ old('farming_poultry_specify', $farmer->farming_poultry_specify) }}" autocomplete="off" class="f"
                style="top:77.38%;left:17.03%;width:16.94%;height:1.03%;">

            <input type="checkbox" class="cb" name="farmwork_land_preparation" value="1" style="top:67.42%;left:35.96%;" {{ old('farmwork_land_preparation', $farmer->farmwork_land_preparation) ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="farmwork_planting" value="1" style="top:69.34%;left:35.95%;" {{ old('farmwork_planting', $farmer->farmwork_planting) ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="farmwork_cultivation" value="1" style="top:71.25%;left:35.99%;" {{ old('farmwork_cultivation', $farmer->farmwork_cultivation) ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="farmwork_harvesting" value="1" style="top:73.16%;left:35.86%;" {{ old('farmwork_harvesting', $farmer->farmwork_harvesting) ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="farmwork_others" value="1" style="top:75.06%;left:36.00%;" {{ old('farmwork_others', $farmer->farmwork_others) ? 'checked' : '' }}>
            <input type="text" name="farmwork_others_specify"
                value="{{ old('farmwork_others_specify', $farmer->farmwork_others_specify) }}" autocomplete="off" class="f"
                style="top:77.35%;left:36.18%;width:16.07%;height:1.03%;">

            <input type="checkbox" class="cb" name="fishing_capture" value="1" style="top:72.32%;left:55.64%;" {{ old('fishing_capture', $farmer->fishing_capture) ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="fishing_processing" value="1" style="top:72.30%;left:66.69%;" {{ old('fishing_processing', $farmer->fishing_processing) ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="fishing_aquaculture" value="1" style="top:73.68%;left:55.58%;" {{ old('fishing_aquaculture', $farmer->fishing_aquaculture) ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="fishing_vending" value="1" style="top:73.65%;left:66.65%;" {{ old('fishing_vending', $farmer->fishing_vending) ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="fishing_gleaning" value="1" style="top:74.99%;left:55.53%;" {{ old('fishing_gleaning', $farmer->fishing_gleaning) ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="fishing_others" value="1" style="top:76.27%;left:55.62%;" {{ old('fishing_others', $farmer->fishing_others) ? 'checked' : '' }}>
            <input type="text" name="fishing_others_specify"
                value="{{ old('fishing_others_specify', $farmer->fishing_others_specify) }}" autocomplete="off" class="f"
                style="top:77.21%;left:54.81%;width:21.85%;height:1.03%;">

            <input type="checkbox" class="cb" name="agri_youth_farming_household" value="1" style="top:70.52%;left:79.28%;"
                {{ old('agri_youth_farming_household', $farmer->agri_youth_farming_household) ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="agri_youth_formal_course" value="1" style="top:71.64%;left:79.31%;" {{ old('agri_youth_formal_course', $farmer->agri_youth_formal_course) ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="agri_youth_nonformal_course" value="1" style="top:73.42%;left:79.32%;"
                {{ old('agri_youth_nonformal_course', $farmer->agri_youth_nonformal_course) ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="agri_youth_participated_program" value="1"
                style="top:75.39%;left:79.28%;" {{ old('agri_youth_participated_program', $farmer->agri_youth_participated_program) ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="agri_youth_others" value="1" style="top:77.34%;left:79.34%;" {{ old('agri_youth_others', $farmer->agri_youth_others) ? 'checked' : '' }}>
            <input type="text" name="agri_youth_others_specify"
                value="{{ old('agri_youth_others_specify', $farmer->agri_youth_others_specify) }}" autocomplete="off"
                class="f" style="top:77.86%;left:79.28%;width:14.72%;height:0.74%;">

            <input type="text" name="gross_annual_income_farming"
                value="{{ old('gross_annual_income_farming', $farmer->gross_annual_income_farming) }}" autocomplete="off"
                class="f" style="top:79.63%;left:35.85%;width:22.18%;height:1.0%;">
            <input type="text" name="gross_annual_income_non_farming"
                value="{{ old('gross_annual_income_non_farming', $farmer->gross_annual_income_non_farming) }}"
                autocomplete="off" class="f" style="top:79.68%;left:72.69%;width:21.78%;height:1.0%;">

            <input type="hidden" name="farm_location_barangay"
                value="{{ old('farm_location_barangay', $farmer->farm_location_barangay) }}">
            <input type="hidden" name="farm_location_municipality"
                value="{{ old('farm_location_municipality', $farmer->farm_location_municipality) }}">
            <input type="hidden" name="farm_location_province"
                value="{{ old('farm_location_province', $farmer->farm_location_province) }}">
            <input type="hidden" name="land_area_hectares"
                value="{{ old('land_area_hectares', $farmer->land_area_hectares) }}">
            <input type="hidden" name="land_holding_status"
                value="{{ old('land_holding_status', $farmer->land_holding_status) }}">
            <input type="hidden" name="status" value="{{ old('status', $farmer->status) }}">

            <input type="text" id="stubSurname" readonly tabindex="-1" class="f"
                style="top:90.55%;left:4.87%;width:43.16%;height:1.43%;color:#333;"
                value="{{ strtoupper($farmer->surname) }}">
            <input type="text" id="stubFirstName" readonly tabindex="-1" class="f"
                style="top:90.55%;left:51.26%;width:43.16%;height:1.43%;color:#333;"
                value="{{ strtoupper($farmer->first_name) }}">
            <input type="text" id="stubMiddleName" readonly tabindex="-1" class="f"
                style="top:93.73%;left:4.86%;width:43.16%;height:1.43%;color:#333;"
                value="{{ strtoupper($farmer->middle_name) }}">
            <input type="text" id="stubExtName" readonly tabindex="-1" class="f"
                style="top:93.73%;left:50.39%;width:11.90%;height:1.43%;color:#333;"
                value="{{ strtoupper($farmer->extension_name) }}">

        </div>

       {{-- ══════════════ PAGE 2 — FARM PARCEL INFORMATION ══════════════ --}}
@php
    $parcels = isset($farmer) ? $farmer->farmParcels : collect();
    $pc = [
      'no_parcels'=>['2.72%','19.21%','5.09%','1.24%'],
      'rot1'=>['2.55%','45.83%','14.54%','1.24%'],
      'rot2'=>['2.59%','63.21%','14.54%','1.24%'],
      'rot3'=>['2.59%','80.49%','14.54%','1.24%'],
      1=>['brgy'=>['11.01%','16.81%','27.5%','1.0%'],'muni'=>['12.17%','16.81%','27.5%','1.0%'],
          'area'=>['13.96%','23.93%','2.27%','1.0%'],'doc'=>['16.15%','22.86%','2.81%','1.0%'],
          'anc_y'=>['15.35%','30.1%'],'anc_n'=>['15.36%','37.11%'],
          'arb_y'=>['17.51%','30.1%'],'arb_n'=>['17.5%','37.14%'],
          'reg'=>['19.34%','24.32%'],'oth'=>['19.32%','10.37%'],'ten'=>['20.45%','10.35%'],'les'=>['21.66%','10.37%'],
          'oth_t'=>['18.84%','29.45%','14.5%','1.0%'],'ten_t'=>['19.97%','26.27%','17.87%','1.0%'],'les_t'=>['21.16%','26.3%','17.76%','1.0%'],
          'crop'=>['10.83%','46.07%','12.22%','2.43%'],'size'=>['10.92%','58.3%','6.84%','2.43%'],
          'head'=>['10.9%','64.94%','6.84%','2.43%'],'ftype'=>['10.95%','71.68%','7.26%','2.4%'],
          'org'=>['10.92%','79.09%','7.19%','2.43%'],'rem'=>['10.86%','86.29%','8.99%','2.43%']],
      2=>['brgy'=>['22.92%','16.76%','27.55%','1.0%'],'muni'=>['24.12%','16.76%','27.55%','1.0%'],
          'area'=>['25.94%','23.88%','2.3%','1.0%'],'doc'=>['28.09%','22.86%','2.67%','1.0%'],
          'anc_y'=>['27.33%','30.08%'],'anc_n'=>['27.3%','37.13%'],
          'arb_y'=>['29.46%','30.13%'],'arb_n'=>['29.45%','37.13%'],
          'reg'=>['31.3%','24.3%'],'oth'=>['31.28%','10.39%'],'ten'=>['32.42%','10.35%'],'les'=>['33.61%','10.35%'],
          'oth_t'=>['30.84%','29.39%','14.59%','1.0%'],'ten_t'=>['32.0%','26.22%','18.0%','1.0%'],'les_t'=>['33.14%','26.05%','18.0%','1.0%'],
          'crop'=>['22.7%','46.15%','12.22%','2.43%'],'size'=>['22.7%','58.32%','6.84%','2.43%'],
          'head'=>['22.79%','65.09%','6.57%','2.36%'],'ftype'=>['22.78%','71.7%','7.26%','2.4%'],
          'org'=>['22.74%','79.14%','7.19%','2.43%'],'rem'=>['22.75%','86.39%','8.99%','2.43%']],
      3=>['brgy'=>['34.96%','17.39%','27.41%','0.8%'],'muni'=>['36.08%','17.39%','27.41%','0.82%'],
          'area'=>['37.96%','24.47%','2.44%','0.73%'],'doc'=>['40.11%','23.26%','2.81%','0.73%'],
          'anc_y'=>['39.1%','30.62%'],'anc_n'=>['39.09%','37.65%'],
          'arb_y'=>['41.25%','30.64%'],'arb_n'=>['41.25%','37.66%'],
          'reg'=>['43.07%','24.82%'],'oth'=>['43.06%','10.89%'],'ten'=>['44.2%','10.91%'],'les'=>['45.39%','10.91%'],
          'oth_t'=>['42.86%','29.98%','14.5%','0.73%'],'ten_t'=>['44.02%','26.81%','17.87%','0.73%'],'les_t'=>['45.15%','26.57%','18.0%','0.73%'],
          'crop'=>['34.59%','46.1%','12.22%','2.43%'],'size'=>['34.6%','58.49%','6.57%','2.43%'],
          'head'=>['34.66%','65.13%','6.57%','2.36%'],'ftype'=>['34.65%','71.73%','7.26%','2.4%'],
          'org'=>['34.61%','79.08%','7.26%','2.4%'],'rem'=>['34.65%','86.37%','8.99%','2.43%']],
      'sig'=>['date'=>['62.65%','5.29%','17.14%','1.47%'],'pname'=>['62.65%','24.29%','26.67%','1.47%'],
              'v1'=>['69.41%','5.43%','26.59%','1.47%'],'v2'=>['69.4%','34.82%','29.82%','1.47%'],'v3'=>['69.4%','67.36%','26.59%','1.47%'],
              'pn1'=>['92.02%','5.48%','26.59%','1.47%'],'pn2'=>['91.99%','34.81%','29.82%','1.47%'],'pn3'=>['92.0%','67.48%','26.59%','1.47%']],
    ];
    $box = fn($a) => "top:{$a[0]};left:{$a[1]};width:{$a[2]};height:{$a[3]};";
    $chk = fn($a) => "top:{$a[0]};left:{$a[1]};";
@endphp

<div style="position:relative;width:850px;height:1275px;margin:0 auto 24px auto;background:#fff;box-shadow:0 2px 12px rgba(0,0,0,.15);">
<img src="{{ asset('images/rsbsa_page-2.jpg') }}" alt="RSBSA Page 2"
     style="width:100%;height:1275px;display:block;pointer-events:none;user-select:none;">

<input type="text" name="no_of_parcels" value="{{ old('no_of_parcels', $parcels->count() ?: '') }}"
    autocomplete="off" class="f" style="{{ $box($pc['no_parcels']) }}">
<input type="text" name="rotation_p1" value="{{ old('rotation_p1') }}" autocomplete="off" class="f" style="{{ $box($pc['rot1']) }}font-size:9px;">
<input type="text" name="rotation_p2" value="{{ old('rotation_p2') }}" autocomplete="off" class="f" style="{{ $box($pc['rot2']) }}font-size:9px;">
<input type="text" name="rotation_p3" value="{{ old('rotation_p3') }}" autocomplete="off" class="f" style="{{ $box($pc['rot3']) }}font-size:9px;">

@for ($n = 1; $n <= 3; $n++)
@php $p = $parcels->get($n - 1); $i = $n - 1; $g = $pc[$n]; @endphp

<input type="text" name="parcel_barangay[]" value="{{ old("parcel_barangay.$i", $p?->farm_location_barangay) }}"
    autocomplete="off" class="f" style="{{ $box($g['brgy']) }}font-size:9px;">
<input type="text" name="parcel_municipality[]" value="{{ old("parcel_municipality.$i", $p?->farm_location_municipality) }}"
    autocomplete="off" class="f" style="{{ $box($g['muni']) }}font-size:9px;">
<input type="text" name="parcel_area[]" value="{{ old("parcel_area.$i", $p?->total_farm_area_ha) }}"
    autocomplete="off" class="f" style="{{ $box($g['area']) }}font-size:9px;">
<input type="text" name="parcel_doc_code[]" value="{{ old("parcel_doc_code.$i", $p?->ownership_document_code) }}"
    autocomplete="off" class="f" style="{{ $box($g['doc']) }}font-size:9px;">

<input type="radio" name="parcel_ancestral[{{ $i }}]" value="1" class="cb" style="{{ $chk($g['anc_y']) }}"
    {{ old("parcel_ancestral.$i", $p ? ($p->within_ancestral_domain ? '1' : '0') : null) === '1' ? 'checked' : '' }}>
<input type="radio" name="parcel_ancestral[{{ $i }}]" value="0" class="cb" style="{{ $chk($g['anc_n']) }}"
    {{ old("parcel_ancestral.$i", $p ? ($p->within_ancestral_domain ? '1' : '0') : null) === '0' ? 'checked' : '' }}>

<input type="radio" name="parcel_arb[{{ $i }}]" value="1" class="cb" style="{{ $chk($g['arb_y']) }}"
    {{ old("parcel_arb.$i", $p ? ($p->agrarian_reform_beneficiary ? '1' : '0') : null) === '1' ? 'checked' : '' }}>
<input type="radio" name="parcel_arb[{{ $i }}]" value="0" class="cb" style="{{ $chk($g['arb_n']) }}"
    {{ old("parcel_arb.$i", $p ? ($p->agrarian_reform_beneficiary ? '1' : '0') : null) === '0' ? 'checked' : '' }}>

@php $ot = old("parcel_ownership_type.$i", $p?->ownership_type); @endphp
<input type="radio" name="parcel_ownership_type[{{ $i }}]" value="registered_owner" class="cb" style="{{ $chk($g['reg']) }}" {{ $ot === 'registered_owner' ? 'checked' : '' }}>
<input type="radio" name="parcel_ownership_type[{{ $i }}]" value="others" class="cb" style="{{ $chk($g['oth']) }}" {{ $ot === 'others' ? 'checked' : '' }}>
<input type="radio" name="parcel_ownership_type[{{ $i }}]" value="tenant" class="cb" style="{{ $chk($g['ten']) }}" {{ $ot === 'tenant' ? 'checked' : '' }}>
<input type="radio" name="parcel_ownership_type[{{ $i }}]" value="lessee" class="cb" style="{{ $chk($g['les']) }}" {{ $ot === 'lessee' ? 'checked' : '' }}>

<input type="text" name="parcel_others_specify[]" value="{{ old("parcel_others_specify.$i", $ot === 'others' ? $p?->owner_name : '') }}"
    autocomplete="off" class="f" style="{{ $box($g['oth_t']) }}font-size:8px;">
<input type="text" name="parcel_tenant_name[]" value="{{ old("parcel_tenant_name.$i", $ot === 'tenant' ? $p?->owner_name : '') }}"
    autocomplete="off" class="f" style="{{ $box($g['ten_t']) }}font-size:8px;">
<input type="text" name="parcel_lessee_name[]" value="{{ old("parcel_lessee_name.$i", $ot === 'lessee' ? $p?->owner_name : '') }}"
    autocomplete="off" class="f" style="{{ $box($g['les_t']) }}font-size:8px;">

<input type="text" name="parcel_crop[]" value="{{ old("parcel_crop.$i", $p?->crop_commodity) }}"
    autocomplete="off" class="f" style="{{ $box($g['crop']) }}font-size:9px;">
<input type="text" name="parcel_size[]" value="{{ old("parcel_size.$i", $p?->size_ha) }}"
    autocomplete="off" class="f" style="{{ $box($g['size']) }}font-size:9px;">
<input type="text" name="parcel_no_head[]" value="{{ old("parcel_no_head.$i", $p?->no_of_head) }}"
    autocomplete="off" class="f" style="{{ $box($g['head']) }}font-size:9px;">
<input type="text" name="parcel_farm_type[]" value="{{ old("parcel_farm_type.$i", $p?->farm_type) }}"
    autocomplete="off" class="f" style="{{ $box($g['ftype']) }}font-size:9px;" placeholder="1-3">
<input type="text" name="parcel_organic[]" value="{{ old("parcel_organic.$i", $p ? ($p->organic_practitioner ? 'Y' : 'N') : '') }}"
    autocomplete="off" class="f" style="{{ $box($g['org']) }}font-size:9px;" placeholder="Y/N">
<input type="text" name="parcel_remarks[]" value="{{ old("parcel_remarks.$i", $p?->remarks) }}"
    autocomplete="off" class="f" style="{{ $box($g['rem']) }}font-size:8px;">
@endfor

<input type="text" name="p2_date" value="{{ old('p2_date') }}" autocomplete="off" class="f" style="{{ $box($pc['sig']['date']) }}font-size:9px;">
<input type="text" name="p2_printed_name"
    value="{{ old('p2_printed_name', isset($farmer) ? strtoupper(trim($farmer->first_name.' '.$farmer->middle_name.' '.$farmer->surname)) : '') }}"
    autocomplete="off" class="f" style="{{ $box($pc['sig']['pname']) }}font-size:9px;">
<input type="text" name="p2_verified_by_1" value="{{ old('p2_verified_by_1') }}" autocomplete="off" class="f" style="{{ $box($pc['sig']['v1']) }}font-size:9px;">
<input type="text" name="p2_verified_by_2" value="{{ old('p2_verified_by_2') }}" autocomplete="off" class="f" style="{{ $box($pc['sig']['v2']) }}font-size:9px;">
<input type="text" name="p2_verified_by_3" value="{{ old('p2_verified_by_3') }}" autocomplete="off" class="f" style="{{ $box($pc['sig']['v3']) }}font-size:9px;">
<input type="text" name="p2_printed_name_barangay" value="{{ old('p2_printed_name_barangay') }}" autocomplete="off" class="f" style="{{ $box($pc['sig']['pn1']) }}font-size:8px;">
<input type="text" name="p2_printed_name_mao" value="{{ old('p2_printed_name_mao') }}" autocomplete="off" class="f" style="{{ $box($pc['sig']['pn2']) }}font-size:8px;">
<input type="text" name="p2_printed_name_cafc" value="{{ old('p2_printed_name_cafc') }}" autocomplete="off" class="f" style="{{ $box($pc['sig']['pn3']) }}font-size:8px;">

</div>
        <div style="width:850px;margin:0 auto 32px auto;display:flex;gap:12px;">
            <button type="submit"
                class="bg-primary hover:bg-primary-dark text-white font-semibold px-8 py-2.5 rounded-md transition text-sm">
                Save Changes
            </button>
            <a href="{{ route('farmers.show', $farmer) }}"
                class="border border-gray-300 text-gray-600 hover:bg-gray-50 font-medium px-8 py-2.5 rounded-md transition text-sm">
                Cancel
            </a>
        </div>

    </form>

    <script>
        document.querySelectorAll('.edu-cb').forEach(cb => cb.addEventListener('change', () => {
            document.querySelectorAll('.edu-cb').forEach(o => { if (o !== cb) o.checked = false; });
            document.getElementById('edu_h').value = cb.checked ? cb.dataset.val : '';
        }));
        document.querySelectorAll('.cs-cb').forEach(cb => cb.addEventListener('change', () => {
            document.querySelectorAll('.cs-cb').forEach(o => { if (o !== cb) o.checked = false; });
            document.getElementById('cs_h').value = cb.checked ? cb.dataset.val : '';
        }));
        document.querySelectorAll('.lh-cb').forEach(cb => cb.addEventListener('change', () => {
            document.querySelectorAll('.lh-cb').forEach(o => { if (o !== cb) o.checked = false; });
            document.getElementById('lh_h').value = cb.checked ? cb.dataset.val : '';
        }));
        document.querySelectorAll('.rel-cb').forEach(cb => cb.addEventListener('change', () => {
            document.querySelectorAll('.rel-cb').forEach(o => { if (o !== cb) o.checked = false; });
            document.getElementById('religion_h').value = cb.checked ? cb.dataset.val : '';
            document.getElementById('religionOther').style.display = (cb.checked && cb.dataset.val === 'others') ? 'block' : 'none';
        }));

        function mkDig(sel, hiddenId) {
            const bs = Array.from(document.querySelectorAll(sel));
            bs.forEach((b, i) => {
                b.addEventListener('input', () => { b.value = b.value.replace(/\D/g, ''); if (b.value && i < bs.length - 1) bs[i + 1].focus(); });
                b.addEventListener('keydown', e => { if (e.key === 'Backspace' && !b.value && i > 0) bs[i - 1].focus(); });
            });
            document.getElementById('farmerForm').addEventListener('submit', () => {
                document.getElementById(hiddenId).value = bs.map(b => b.value).join('');
            });
            return bs;
        }
        mkDig('.dateadmin-d', 'dateAdminH');
        mkDig('.refnum-d', 'refH');

        function setupDigitRow(boxesId, realId, hiddenId, length, boxWidthPct) {
            const boxesDiv = document.getElementById(boxesId);
            const real = document.getElementById(realId);
            const hidden = document.getElementById(hiddenId);

            function render() {
                const val = real.value.padEnd(length, ' ');
                boxesDiv.innerHTML = '';
                for (let i = 0; i < length; i++) {
                    const box = document.createElement('div');
                    box.textContent = val[i].trim();
                    box.style.cssText = `width:${boxWidthPct}%;height:100%;text-align:center;font-size:10px;font-weight:bold;border:1px solid #999;background:rgba(255,255,255,0.4);display:flex;align-items:center;justify-content:center;`;
                    boxesDiv.appendChild(box);
                }
                hidden.value = real.value;
            }

            real.addEventListener('input', () => {
                real.value = real.value.replace(/\D/g, '').slice(0, length);
                render();
            });

            render();
            boxesDiv.addEventListener('click', () => real.focus());
        }

        setupDigitRow('mobBoxes', 'mobReal', 'mobH', 11, 9);
        setupDigitRow('landBoxes', 'landReal', 'landH', 10, 10);
        setupDigitRow('dobBoxes', 'dobReal', 'dobH', 8, 12);
        setupDigitRow('conBoxes', 'conReal', 'conH', 11, 9);

        document.getElementById('farmerForm').addEventListener('submit', () => {
            const dob = document.getElementById('dobReal').value;
            if (dob.length === 8) document.getElementById('dobH').value = `${dob.slice(4, 8)}-${dob.slice(0, 2)}-${dob.slice(2, 4)}`;
        });
        document.getElementById('farmerForm').addEventListener('submit', () => {
            const p1 = document.getElementById('pob1').value;
            const p2 = document.getElementById('pob2').value;
            const p3 = document.getElementById('pob3').value;
            document.getElementById('pobH').value = [p1, p2, p3].filter(Boolean).join(', ');
        });
        document.getElementById('farmerForm').addEventListener('submit', () => {
            const v = document.querySelectorAll('.dateadmin-d');
            const digits = Array.from(v).map(b => b.value).join('');
            if (digits.length === 8) document.getElementById('dateAdminH').value = `${digits.slice(4, 8)}-${digits.slice(0, 2)}-${digits.slice(2, 4)}`;
        });

        document.getElementById('photoInput').addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = e => {
                    const img = document.getElementById('photoPreview');
                    img.src = e.target.result;
                    img.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    </script>

@endsection
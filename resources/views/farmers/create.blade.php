@extends('layouts.app')

@section('content')

    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Register Farmer</h2>
            <p class="text-gray-500 text-sm mt-1">ANI AT KITA — RSBSA Enrollment Form</p>
        </div>
        <a href="{{ route('farmers.index') }}"
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

    <form method="POST" action="{{ route('farmers.store') }}" enctype="multipart/form-data" id="farmerForm"
        autocomplete="off">
        @csrf

        {{-- ══════════════ PAGE 1 ══════════════ --}}
        <div class="rsbsa-page">
            <img src="{{ asset('images/rsbsa_page-1.jpg') }}" alt="RSBSA Page 1">

            {{-- PHOTO UPLOAD (over the 2x2 Picture box) --}}
            <div class="photo-upload" style="top:2.81%;left:74.42%;width:21.45%;height:13.93%;">
                <input type="file" name="photo" id="photoInput" accept="image/*" autocomplete="off">
                <img id="photoPreview" class="photo-preview" alt="Preview">
            </div>

            {{-- ENROLLMENT TYPE --}}
            <input type="checkbox" class="cb" id="cb_new" style="top:12.81%;left:18.26%;" checked
                onchange="if(this.checked)document.getElementById('cb_upd').checked=false; document.getElementById('enroll_type').value='new';">
            <input type="checkbox" class="cb" id="cb_upd" style="top:12.78%;left:25.39%;"
                onchange="if(this.checked){document.getElementById('cb_new').checked=false; document.getElementById('enroll_type').value='updating';}">
            <input type="hidden" name="enrollment_type" id="enroll_type" value="new">

            {{-- DATE ADMINISTERED (8 real, individually-typeable boxes: MMDDYYYY) --}}
            <div style="position:absolute;top:12.56%;left:35.54%;width:16%;height:1.24%;display:flex;">
                @for($i = 0; $i < 8; $i++)
                    <input type="text" maxlength="1"
                        style="width:12%;height:100%;text-align:center;border:1px solid #999;background:rgba(255,255,255,0.4);font-size:10px;font-weight:bold;padding:0;outline:none;"
                        class="dateadmin-d" autocomplete="off">
                @endfor
            </div>
            <input type="hidden" name="date_administered" id="dateAdminH">

            {{-- REFERENCE NUMBER (15 real, individually-typeable boxes) --}}
            <div style="position:absolute;top:14.64%;left:18.38%;width:33%;height:1.33%;display:flex;">
                @for($i = 0; $i < 15; $i++)
                    <input type="text" maxlength="1"
                        style="width:6.6%;height:100%;text-align:center;border:1px solid #999;background:rgba(255,255,255,0.4);font-size:10px;font-weight:bold;padding:0;outline:none;"
                        class="refnum-d" autocomplete="off">
                @endfor
            </div>
            <input type="hidden" name="reference_number" id="refH">

            {{-- SURNAME --}}
            <input type="text" name="surname" id="mainSurname" value="{{ old('surname') }}" required autocomplete="off"
                class="f" style="top:18.64%;left:5.58%;width:42.06%;height:2.05%;">

            {{-- FIRST NAME --}}
            <input type="text" name="first_name" id="mainFirstName" value="{{ old('first_name') }}" required
                autocomplete="off" class="f" style="top:18.49%;left:52.41%;width:42.06%;height:2.05%;">

            {{-- MIDDLE NAME --}}
            <input type="text" name="middle_name" id="mainMiddleName" value="{{ old('middle_name') }}" autocomplete="off"
                class="f" style="top:21.40%;left:5.62%;width:42.06%;height:2.05%;">

            {{-- EXTENSION NAME --}}
            <input type="text" name="extension_name" id="mainExtName" value="{{ old('extension_name') }}" autocomplete="off"
                class="f" style="top:21.44%;left:50.98%;width:9.79%;height:2.05%;">

            {{-- SEX --}}
            <input type="checkbox" class="cb" id="sex_m" style="top:23.50%;left:76.56%;"
                onchange="if(this.checked){document.getElementById('sex_f').checked=false;document.getElementById('sex_h').value='male';}"
                {{ old('sex') == 'male' ? 'checked' : '' }}>
            <input type="checkbox" class="cb" id="sex_f" style="top:23.44%;left:84.74%;"
                onchange="if(this.checked){document.getElementById('sex_m').checked=false;document.getElementById('sex_h').value='female';}"
                {{ old('sex') == 'female' ? 'checked' : '' }}>
            <input type="hidden" name="sex" id="sex_h" value="{{ old('sex') }}" required>

            {{-- ADDRESS HOUSE --}}
            <input type="text" name="house_lot_number" value="{{ old('house_lot_number') }}" autocomplete="off" class="f"
                style="top:25.54%;left:11.90%;width:26.46%;height:1.63%;">

            {{-- ADDRESS STREET --}}
            <input type="text" name="street" value="{{ old('street') }}" autocomplete="off" class="f"
                style="top:25.59%;left:40.18%;width:26.46%;height:1.63%;">

            {{-- BARANGAY --}}
            <select name="barangay_id" class="f" autocomplete="off"
                style="top:25.55%;left:68.38%;width:26.46%;height:1.63%;background:rgba(255,255,255,0.9);cursor:pointer;">
                <option value="">Select</option>
                @foreach($barangays as $b)
                    <option value="{{ $b->id }}" {{ old('barangay_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                @endforeach
            </select>

            {{-- MUNICIPALITY --}}
            <input type="text" name="municipality" value="{{ old('municipality', 'Guinobatan') }}" autocomplete="off"
                class="f" style="top:28.69%;left:11.79%;width:26.46%;height:1.63%;">

            {{-- PROVINCE --}}
            <input type="text" name="province" value="{{ old('province', 'Albay') }}" autocomplete="off" class="f"
                style="top:28.69%;left:40.18%;width:26.46%;height:1.63%;">

            {{-- REGION --}}
            <input type="text" name="region" value="{{ old('region') }}" autocomplete="off" class="f"
                style="top:28.75%;left:68.38%;width:26.46%;height:1.63%;">

            {{-- MOBILE NUMBER (11 digit boxes) --}}
            <div style="position:absolute;top:32.78%;left:5.39%;width:20%;height:1.09%;display:flex;">
                @for($i = 0; $i < 11; $i++)
                    <input type="text" maxlength="1"
                        style="width:9%;height:100%;text-align:center;border:none;background:transparent;font-size:10px;font-weight:bold;padding:0;outline:none;"
                        class="mob-d" autocomplete="off">
                @endfor
            </div>
            <input type="hidden" name="mobile_number" id="mobH" value="{{ old('mobile_number') }}">

            {{-- LANDLINE NUMBER (10 digit boxes) --}}
            <div style="position:absolute;top:32.72%;left:29.62%;width:18%;height:1.19%;display:flex;">
                @for($i = 0; $i < 10; $i++)
                    <input type="text" maxlength="1"
                        style="width:10%;height:100%;text-align:center;border:none;background:transparent;font-size:10px;font-weight:bold;padding:0;outline:none;"
                        class="land-d" autocomplete="off">
                @endfor
            </div>
            <input type="hidden" name="landline_number" id="landH" value="{{ old('landline_number') }}">

            {{-- HIGHEST EDUCATION --}}
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
            @endphp
            @foreach($eduMap as $val => $pos)
                <input type="checkbox" class="cb edu-cb" data-val="{{ $val }}"
                    style="top:{{ $pos['top'] }};left:{{ $pos['left'] }};" {{ old('highest_education') == $val ? 'checked' : '' }}>
            @endforeach
            <input type="hidden" name="highest_education" id="edu_h" value="{{ old('highest_education') }}">

            {{-- DATE OF BIRTH (8 digit boxes) --}}
            <div style="position:absolute;top:36.06%;left:5.45%;width:17.5%;height:1.45%;display:flex;">
                @for($i = 0; $i < 8; $i++)
                    <input type="text" maxlength="1"
                        style="width:12%;height:100%;text-align:center;border:none;background:transparent;font-size:10px;font-weight:bold;padding:0;outline:none;"
                        class="dob-d" autocomplete="off">
                @endfor
            </div>
            <input type="hidden" name="date_of_birth" id="dobH" value="{{ old('date_of_birth') }}">

            {{-- PLACE OF BIRTH --}}
            <input type="text" id="pob1" placeholder="City/Municipality" value="" autocomplete="off" class="f"
                style="top:35.74%;left:25.54%;width:23.42%;height:0.91%;font-size:9px;">
            <input type="text" id="pob2" placeholder="Province/State" value="" autocomplete="off" class="f"
                style="top:37.03%;left:25.43%;width:11.29%;height:0.91%;font-size:9px;">
            <input type="text" id="pob3" placeholder="Country" value="Philippines" autocomplete="off" class="f"
                style="top:37.04%;left:37.49%;width:11.29%;height:0.91%;font-size:9px;">
            <input type="hidden" name="place_of_birth" id="pobH" value="{{ old('place_of_birth') }}">

            {{-- PWD --}}
            <input type="checkbox" class="cb" id="pwd_y" style="top:38.52%;left:75.99%;"
                onchange="if(this.checked){document.getElementById('pwd_n').checked=false;document.getElementById('pwd_h').value='1';}"
                {{ old('is_pwd') == '1' ? 'checked' : '' }}>
            <input type="checkbox" class="cb" id="pwd_n" style="top:38.52%;left:83.11%;"
                onchange="if(this.checked){document.getElementById('pwd_y').checked=false;document.getElementById('pwd_h').value='0';}"
                {{ old('is_pwd') == '0' ? 'checked' : '' }}>
            <input type="hidden" name="is_pwd" id="pwd_h" value="{{ old('is_pwd', '0') }}">

            {{-- RELIGION --}}
            <input type="checkbox" class="cb rel-cb" data-val="Christianity" style="top:40.30%;left:13.03%;" {{ old('religion') == 'Christianity' ? 'checked' : '' }}>
            <input type="checkbox" class="cb rel-cb" data-val="Islam" style="top:40.39%;left:22.96%;" {{ old('religion') == 'Islam' ? 'checked' : '' }}>
            <input type="checkbox" class="cb rel-cb" data-val="others" style="top:40.36%;left:28.92%;" {{ old('religion') == 'others' ? 'checked' : '' }}>
            <input type="text" id="religionOther" name="religion_other" value="{{ old('religion_other') }}"
                autocomplete="off" class="f"
                style="top:40.22%;left:39.73%;width:9.61%;height:0.91%;display:{{ old('religion') == 'others' ? 'block' : 'none' }};">
            <input type="hidden" name="religion" id="religion_h" value="{{ old('religion') }}">

            {{-- 4PS --}}
            <input type="checkbox" class="cb" id="fps_y" style="top:41.09%;left:75.69%;"
                onchange="if(this.checked){document.getElementById('fps_n').checked=false;document.getElementById('fps_h').value='1';}"
                {{ old('is_4ps_beneficiary') == '1' ? 'checked' : '' }}>
            <input type="checkbox" class="cb" id="fps_n" style="top:41.10%;left:82.66%;"
                onchange="if(this.checked){document.getElementById('fps_y').checked=false;document.getElementById('fps_h').value='0';}"
                {{ old('is_4ps_beneficiary') == '0' ? 'checked' : '' }}>
            <input type="hidden" name="is_4ps_beneficiary" id="fps_h" value="{{ old('is_4ps_beneficiary', '0') }}">

            {{-- CIVIL STATUS --}}
            @php $csMap = ['single' => '15.74%', 'married' => '23.36%', 'widowed' => '31.52%', 'separated' => '40.66%']; @endphp
            @foreach($csMap as $val => $left)
                <input type="checkbox" class="cb cs-cb" data-val="{{ $val }}" style="top:42.47%;left:{{ $left }};" {{ old('civil_status') == $val ? 'checked' : '' }}>
            @endforeach
            <input type="hidden" name="civil_status" id="cs_h" value="{{ old('civil_status') }}">

            {{-- INDIGENOUS GROUP --}}
            <input type="checkbox" class="cb" id="ind_y" style="top:42.76%;left:75.67%;"
                onchange="if(this.checked){document.getElementById('ind_n').checked=false;document.getElementById('ind_h').value='1';}"
                {{ old('is_indigenous') == '1' ? 'checked' : '' }}>
            <input type="checkbox" class="cb" id="ind_n" style="top:42.75%;left:82.66%;"
                onchange="if(this.checked){document.getElementById('ind_y').checked=false;document.getElementById('ind_h').value='0';}"
                {{ old('is_indigenous') == '0' ? 'checked' : '' }}>
            <input type="hidden" name="is_indigenous" id="ind_h" value="{{ old('is_indigenous', '0') }}">
            <input type="text" name="indigenous_group_name" value="{{ old('indigenous_group_name') }}" autocomplete="off"
                class="f" style="top:44.07%;left:60.40%;width:33.99%;height:1.45%;">

            {{-- SPOUSE NAME --}}
            <input type="text" name="spouse_name" value="{{ old('spouse_name') }}" autocomplete="off" class="f"
                style="top:44.84%;left:17.02%;width:31.72%;height:1.39%;">

            {{-- GOVERNMENT ID --}}
            <input type="checkbox" class="cb" id="gid_y" style="top:46.62%;left:66.74%;"
                onchange="if(this.checked){document.getElementById('gid_n').checked=false;document.getElementById('gid_h').value='1';}"
                {{ old('has_government_id') == '1' ? 'checked' : '' }}>
            <input type="checkbox" class="cb" id="gid_n" style="top:46.62%;left:73.87%;"
                onchange="if(this.checked){document.getElementById('gid_y').checked=false;document.getElementById('gid_h').value='0';}"
                {{ old('has_government_id') == '0' ? 'checked' : '' }}>
            <input type="hidden" name="has_government_id" id="gid_h" value="{{ old('has_government_id', '0') }}">
            <input type="text" name="government_id_type" value="{{ old('government_id_type') }}" autocomplete="off"
                class="f" style="top:47.70%;left:66.66%;width:28.02%;height:1.01%;">
            <input type="text" name="government_id_number" value="{{ old('government_id_number') }}" autocomplete="off"
                class="f" style="top:48.90%;left:66.64%;width:28.02%;height:1.01%;">

            {{-- MOTHER'S MAIDEN NAME --}}
            <input type="text" name="mother_maiden_name" value="{{ old('mother_maiden_name') }}" autocomplete="off"
                class="f" style="top:48.00%;left:17.00%;width:31.72%;height:1.39%;">

            {{-- HOUSEHOLD HEAD --}}
            <input type="checkbox" class="cb" id="hh_y" style="top:50.36%;left:22.57%;"
                onchange="if(this.checked){document.getElementById('hh_n').checked=false;document.getElementById('hh_h').value='1';document.getElementById('hhSubFields').style.display='none';}"
                {{ old('is_household_head') == '1' ? 'checked' : '' }}>
            <input type="checkbox" class="cb" id="hh_n" style="top:50.41%;left:29.72%;"
                onchange="if(this.checked){document.getElementById('hh_y').checked=false;document.getElementById('hh_h').value='0';document.getElementById('hhSubFields').style.display='block';}"
                {{ old('is_household_head') == '0' ? 'checked' : '' }}>
            <input type="hidden" name="is_household_head" id="hh_h" value="{{ old('is_household_head', '1') }}">

            <div id="hhSubFields" style="display:{{ old('is_household_head') == '0' ? 'block' : 'none' }};">
                <input type="text" name="household_head_name" value="{{ old('household_head_name') }}" autocomplete="off"
                    class="f" style="top:51.86%;left:24.09%;width:24.67%;height:1.39%;">
                <input type="text" name="household_head_relationship" value="{{ old('household_head_relationship') }}"
                    autocomplete="off" class="f" style="top:53.76%;left:24.11%;width:24.67%;height:1.39%;">
            </div>
            <input type="text" name="household_members_count" value="{{ old('household_members_count') }}"
                autocomplete="off" class="f" style="top:55.91%;left:25.78%;width:22.88%;height:1.39%;">
            <input type="text" name="household_male_count" value="{{ old('household_male_count') }}" autocomplete="off"
                class="f" style="top:57.72%;left:13.19%;width:10.51%;height:1.39%;">
            <input type="text" name="household_female_count" value="{{ old('household_female_count') }}" autocomplete="off"
                class="f" style="top:57.75%;left:37.94%;width:10.51%;height:1.39%;">

            {{-- FARMERS ASSOCIATION --}}
            <input type="checkbox" class="cb" id="asc_y" style="top:51.04%;left:84.31%;"
                onchange="if(this.checked){document.getElementById('asc_n').checked=false;document.getElementById('asc_h').value='1';}"
                {{ old('is_farmers_association_member') == '1' ? 'checked' : '' }}>
            <input type="checkbox" class="cb" id="asc_n" style="top:51.04%;left:90.27%;"
                onchange="if(this.checked){document.getElementById('asc_y').checked=false;document.getElementById('asc_h').value='0';}"
                {{ old('is_farmers_association_member') == '0' ? 'checked' : '' }}>
            <input type="hidden" name="is_farmers_association_member" id="asc_h"
                value="{{ old('is_farmers_association_member', '0') }}">
            <input type="text" name="farmers_association_name" value="{{ old('farmers_association_name') }}"
                autocomplete="off" class="f" style="top:52.57%;left:60.28%;width:34.29%;height:1.25%;">

            {{-- EMERGENCY CONTACT --}}
            <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" autocomplete="off"
                class="f" style="top:55.61%;left:65.70%;width:28.97%;height:1.25%;">

            {{-- CONTACT NUMBER (11 digit boxes) --}}
            <div style="position:absolute;top:57.63%;left:65.69%;width:28%;height:1.74%;display:flex;">
                @for($i = 0; $i < 11; $i++)
                    <input type="text" maxlength="1"
                        style="width:9%;height:100%;text-align:center;border:none;background:transparent;font-size:10px;font-weight:bold;padding:0;outline:none;"
                        class="con-d" autocomplete="off">
                @endfor
            </div>
            <input type="hidden" name="emergency_contact_number" id="conH" value="{{ old('emergency_contact_number') }}">

            {{-- PART II: MAIN LIVELIHOOD --}}
            <input type="checkbox" class="cb lh-cb" data-val="farmer" style="top:61.93%;left:20.44%;" {{ old('main_livelihood') == 'farmer' ? 'checked' : '' }}>
            <input type="checkbox" class="cb lh-cb" data-val="farmworker" style="top:61.91%;left:35.32%;" {{ old('main_livelihood') == 'farmworker' ? 'checked' : '' }}>
            <input type="checkbox" class="cb lh-cb" data-val="fisherfolk" style="top:61.94%;left:60.42%;" {{ old('main_livelihood') == 'fisherfolk' ? 'checked' : '' }}>
            <input type="checkbox" class="cb lh-cb" data-val="agri_youth" style="top:61.94%;left:80.50%;" {{ old('main_livelihood') == 'agri_youth' ? 'checked' : '' }}>
            <input type="hidden" name="main_livelihood" id="lh_h" value="{{ old('main_livelihood') }}">

            {{-- FOR FARMERS --}}
            <input type="checkbox" class="cb" name="farming_rice" value="1" style="top:67.23%;left:5.60%;" {{ old('farming_rice') ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="farming_corn" value="1" style="top:69.19%;left:5.56%;" {{ old('farming_corn') ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="farming_other_crops" value="1" style="top:71.16%;left:5.63%;" {{ old('farming_other_crops') ? 'checked' : '' }}>
            <input type="text" name="farming_other_crops_specify" value="{{ old('farming_other_crops_specify') }}"
                autocomplete="off" class="f" style="top:71.87%;left:16.89%;width:16.94%;height:1.03%;">
            <input type="checkbox" class="cb" name="farming_livestock" value="1" style="top:73.88%;left:5.51%;" {{ old('farming_livestock') ? 'checked' : '' }}>
            <input type="text" name="farming_livestock_specify" value="{{ old('farming_livestock_specify') }}"
                autocomplete="off" class="f" style="top:74.74%;left:16.86%;width:16.94%;height:1.03%;">
            <input type="checkbox" class="cb" name="farming_poultry" value="1" style="top:76.50%;left:5.49%;" {{ old('farming_poultry') ? 'checked' : '' }}>
            <input type="text" name="farming_poultry_specify" value="{{ old('farming_poultry_specify') }}"
                autocomplete="off" class="f" style="top:77.38%;left:17.03%;width:16.94%;height:1.03%;">

            {{-- FOR FARMWORKERS --}}
            <input type="checkbox" class="cb" name="farmwork_land_preparation" value="1" style="top:67.42%;left:35.96%;" {{ old('farmwork_land_preparation') ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="farmwork_planting" value="1" style="top:69.34%;left:35.95%;" {{ old('farmwork_planting') ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="farmwork_cultivation" value="1" style="top:71.25%;left:35.99%;" {{ old('farmwork_cultivation') ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="farmwork_harvesting" value="1" style="top:73.16%;left:35.86%;" {{ old('farmwork_harvesting') ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="farmwork_others" value="1" style="top:75.06%;left:36.00%;" {{ old('farmwork_others') ? 'checked' : '' }}>
            <input type="text" name="farmwork_others_specify" value="{{ old('farmwork_others_specify') }}"
                autocomplete="off" class="f" style="top:77.35%;left:36.18%;width:16.07%;height:1.03%;">

            {{-- FISHERFOLK --}}
            <input type="checkbox" class="cb" name="fishing_capture" value="1" style="top:72.32%;left:55.64%;" {{ old('fishing_capture') ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="fishing_processing" value="1" style="top:72.30%;left:66.69%;" {{ old('fishing_processing') ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="fishing_aquaculture" value="1" style="top:73.68%;left:55.58%;" {{ old('fishing_aquaculture') ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="fishing_vending" value="1" style="top:73.65%;left:66.65%;" {{ old('fishing_vending') ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="fishing_gleaning" value="1" style="top:74.99%;left:55.53%;" {{ old('fishing_gleaning') ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="fishing_others" value="1" style="top:76.27%;left:55.62%;" {{ old('fishing_others') ? 'checked' : '' }}>
            <input type="text" name="fishing_others_specify" value="{{ old('fishing_others_specify') }}" autocomplete="off"
                class="f" style="top:77.21%;left:54.81%;width:21.85%;height:1.03%;">

            {{-- AGRI YOUTH --}}
            <input type="checkbox" class="cb" name="agri_youth_farming_household" value="1" style="top:70.52%;left:79.28%;"
                {{ old('agri_youth_farming_household') ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="agri_youth_formal_course" value="1" style="top:71.64%;left:79.31%;" {{ old('agri_youth_formal_course') ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="agri_youth_nonformal_course" value="1" style="top:73.42%;left:79.32%;"
                {{ old('agri_youth_nonformal_course') ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="agri_youth_participated_program" value="1"
                style="top:75.39%;left:79.28%;" {{ old('agri_youth_participated_program') ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="agri_youth_others" value="1" style="top:77.34%;left:79.34%;" {{ old('agri_youth_others') ? 'checked' : '' }}>
            <input type="text" name="agri_youth_others_specify" value="{{ old('agri_youth_others_specify') }}"
                autocomplete="off" class="f" style="top:77.86%;left:79.28%;width:14.72%;height:0.74%;">

            {{-- GROSS ANNUAL INCOME --}}
            <input type="text" name="gross_annual_income_farming" value="{{ old('gross_annual_income_farming') }}"
                autocomplete="off" class="f" style="top:79.63%;left:35.85%;width:22.18%;height:1.0%;">
            <input type="text" name="gross_annual_income_non_farming" value="{{ old('gross_annual_income_non_farming') }}"
                autocomplete="off" class="f" style="top:79.68%;left:72.69%;width:21.78%;height:1.0%;">

            {{-- Farm location — no dedicated spot on this page; kept hidden with defaults --}}
            <input type="hidden" name="farm_location_barangay" value="{{ old('farm_location_barangay') }}">
            <input type="hidden" name="farm_location_municipality"
                value="{{ old('farm_location_municipality', 'Guinobatan') }}">
            <input type="hidden" name="farm_location_province" value="{{ old('farm_location_province', 'Albay') }}">
            <input type="hidden" name="land_area_hectares" value="{{ old('land_area_hectares') }}">

            {{-- STUB (bottom of page — auto-mirrors main fields, read-only) --}}
            <input type="text" id="stubSurname" readonly tabindex="-1" class="f"
                style="top:90.55%;left:4.87%;width:43.16%;height:1.43%;color:#333;">
            <input type="text" id="stubFirstName" readonly tabindex="-1" class="f"
                style="top:90.55%;left:51.26%;width:43.16%;height:1.43%;color:#333;">
            <input type="text" id="stubMiddleName" readonly tabindex="-1" class="f"
                style="top:93.73%;left:4.86%;width:43.16%;height:1.43%;color:#333;">
            <input type="text" id="stubExtName" readonly tabindex="-1" class="f"
                style="top:93.73%;left:50.39%;width:11.90%;height:1.43%;color:#333;">

        </div>

        {{-- ══════════════ PAGE 2 — FARM PARCEL INFORMATION ══════════════ --}}
        <div class="rsbsa-page" style="width:850px;height:1275px;">
            <img src="{{ asset('images/rsbsa_page-2.jpg') }}" alt="RSBSA Page 2"
                style="width:100%;height:1275px;display:block;">

            {{-- HEADER: No. of Farm Parcels --}}
            <input type="text" name="no_of_parcels"
                value="{{ old('no_of_parcels', isset($farmer) ? $farmer->farmParcels->count() : '') }}" autocomplete="off"
                class="f" style="top:2.72%;left:19.22%;width:5.09%;height:1.24%;">

            {{-- HEADER: Name of Farmer/s in Rotation P1/P2/P3 --}}
            <input type="text" name="rotation_p1" value="{{ old('rotation_p1') }}" autocomplete="off" class="f"
                style="top:2.55%;left:45.84%;width:14.54%;height:1.24%;font-size:9px;">
            <input type="text" name="rotation_p2" value="{{ old('rotation_p2') }}" autocomplete="off" class="f"
                style="top:2.58%;left:63.21%;width:14.54%;height:1.24%;font-size:9px;">
            <input type="text" name="rotation_p3" value="{{ old('rotation_p3') }}" autocomplete="off" class="f"
                style="top:2.58%;left:80.48%;width:14.54%;height:1.24%;font-size:9px;">

            {{-- ══ PARCEL 1 ══ --}}
            {{-- Farm Location Barangay --}}
            <input type="text" name="parcel_barangay[]"
                value="{{ old('parcel_barangay.0', isset($farmer) ? ($farmer->farmParcels[0]->farm_location_barangay ?? '') : '') }}"
                autocomplete="off" class="f" style="top:11.01%;left:16.81%;width:27.50%;height:1.00%;font-size:9px;">
            {{-- Farm Location City/Muni --}}
            <input type="text" name="parcel_municipality[]"
                value="{{ old('parcel_municipality.0', isset($farmer) ? ($farmer->farmParcels[0]->farm_location_municipality ?? '') : '') }}"
                autocomplete="off" class="f" style="top:12.18%;left:16.81%;width:27.50%;height:1.00%;font-size:9px;">
            {{-- Total Farm Area --}}
            <input type="text" name="parcel_area[]"
                value="{{ old('parcel_area.0', isset($farmer) ? ($farmer->farmParcels[0]->total_farm_area_ha ?? '') : '') }}"
                autocomplete="off" class="f" style="top:13.97%;left:23.93%;width:2.26%;height:1.00%;font-size:9px;">
            {{-- Ownership Doc Code --}}
            <input type="text" name="parcel_doc_code[]"
                value="{{ old('parcel_doc_code.0', isset($farmer) ? ($farmer->farmParcels[0]->ownership_document_code ?? '') : '') }}"
                autocomplete="off" class="f" style="top:16.15%;left:22.86%;width:2.80%;height:1.00%;font-size:9px;">
            {{-- Within Ancestral Domain Yes/No --}}
            <input type="checkbox" class="cb" name="parcel_ancestral_yes[]" value="1" style="top:15.35%;left:30.10%;" {{ old('parcel_ancestral_yes.0', isset($farmer) && ($farmer->farmParcels[0]->within_ancestral_domain ?? false) ? '1' : '') == '1' ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="parcel_ancestral_no[]" value="1" style="top:15.36%;left:37.11%;" {{ old('parcel_ancestral_yes.0', isset($farmer) && ($farmer->farmParcels[0]->within_ancestral_domain ?? false) ? '1' : '') != '1' ? 'checked' : '' }}>
            {{-- ARB Yes/No --}}
            <input type="checkbox" class="cb" name="parcel_arb_yes[]" value="1" style="top:17.51%;left:30.10%;" {{ old('parcel_arb_yes.0', isset($farmer) && ($farmer->farmParcels[0]->agrarian_reform_beneficiary ?? false) ? '1' : '') == '1' ? 'checked' : '' }}>
            <input type="checkbox" class="cb" name="parcel_arb_no[]" value="1" style="top:17.50%;left:37.14%;" {{ old('parcel_arb_yes.0', isset($farmer) && ($farmer->farmParcels[0]->agrarian_reform_beneficiary ?? false) ? '1' : '') != '1' ? 'checked' : '' }}>
            {{-- Ownership Type --}}
            <input type="checkbox" class="cb" style="top:19.33%;left:24.32%;"> {{-- Registered Owner --}}
            <input type="checkbox" class="cb" style="top:19.32%;left:10.37%;"> {{-- Others --}}
            <input type="checkbox" class="cb" style="top:20.45%;left:10.36%;"> {{-- Tenant --}}
            <input type="checkbox" class="cb" style="top:21.65%;left:10.37%;"> {{-- Lessee --}}
            {{-- Owner name (Tenant) --}}
            <input type="text" name="parcel_owner_name[]"
                value="{{ old('parcel_owner_name.0', isset($farmer) ? ($farmer->farmParcels[0]->owner_name ?? '') : '') }}"
                autocomplete="off" class="f" style="top:19.97%;left:26.26%;width:17.86%;height:1.00%;font-size:10px;">
            <input type="text" class="f" style="top:21.16%;left:26.31%;width:17.77%;height:1.00%;font-size:10px;"> {{--
            Lessee name --}}
            <input type="text" class="f" style="top:18.84%;left:29.45%;width:14.50%;height:1.00%;font-size:10px;"> {{--
            Others specify --}}
            {{-- Crop rows P1 (5 rows) --}}
            <input type="text" name="parcel_crop[]"
                value="{{ old('parcel_crop.0', isset($farmer) ? ($farmer->farmParcels[0]->crop_commodity ?? '') : '') }}"
                autocomplete="off" class="f" style="top:10.82%;left:46.06%;width:12.21%;height:2.43%;font-size:10px;">
            <input type="text" name="parcel_size[]"
                value="{{ old('parcel_size.0', isset($farmer) ? ($farmer->farmParcels[0]->size_ha ?? '') : '') }}"
                autocomplete="off" class="f" style="top:10.91%;left:58.30%;width:6.83%;height:2.43%;font-size:10px;">
            <input type="text" name="parcel_no_head[]"
                value="{{ old('parcel_no_head.0', isset($farmer) ? ($farmer->farmParcels[0]->no_of_head ?? '') : '') }}"
                autocomplete="off" class="f" style="top:10.90%;left:64.94%;width:6.83%;height:2.43%;font-size:10px;">
            <input type="text" name="parcel_farm_type[]"
                value="{{ old('parcel_farm_type.0', isset($farmer) ? ($farmer->farmParcels[0]->farm_type ?? '') : '') }}"
                autocomplete="off" class="f" style="top:10.95%;left:71.69%;width:7.26%;height:2.40%;font-size:10px;">
            <input type="text" name="parcel_organic[]"
                value="{{ old('parcel_organic.0', isset($farmer) ? ($farmer->farmParcels[0]->organic_practitioner ? 'Y' : '') : '') }}"
                autocomplete="off" class="f" style="top:10.92%;left:79.09%;width:7.19%;height:2.43%;font-size:10px;">
            <input type="text" name="parcel_remarks[]"
                value="{{ old('parcel_remarks.0', isset($farmer) ? ($farmer->farmParcels[0]->remarks ?? '') : '') }}"
                autocomplete="off" class="f" style="top:10.86%;left:86.29%;width:9.00%;height:2.43%;font-size:10px;">
            {{-- Extra crop rows for parcel 1 (rows 2-5) --}}
            <input type="text" class="f" style="top:13.24%;left:46.11%;width:12.21%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:13.33%;left:58.38%;width:6.83%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:13.32%;left:65.12%;width:6.57%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:13.35%;left:71.75%;width:7.26%;height:2.40%;font-size:10px;">
            <input type="text" class="f" style="top:13.27%;left:79.11%;width:7.19%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:13.32%;left:86.39%;width:9.00%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:15.69%;left:46.08%;width:12.21%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:15.70%;left:58.27%;width:6.83%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:15.65%;left:65.05%;width:6.57%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:15.62%;left:71.78%;width:7.26%;height:2.40%;font-size:10px;">
            <input type="text" class="f" style="top:15.65%;left:79.04%;width:7.19%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:15.63%;left:86.21%;width:9.00%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:17.99%;left:46.12%;width:12.21%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:18.05%;left:58.35%;width:6.83%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:17.94%;left:65.10%;width:6.57%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:18.02%;left:71.72%;width:7.26%;height:2.40%;font-size:10px;">
            <input type="text" class="f" style="top:17.97%;left:79.04%;width:7.19%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:17.95%;left:86.31%;width:9.00%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:20.38%;left:46.15%;width:12.21%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:20.39%;left:58.32%;width:6.83%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:20.47%;left:65.07%;width:6.57%;height:2.36%;font-size:10px;">
            <input type="text" class="f" style="top:20.42%;left:71.75%;width:7.26%;height:2.40%;font-size:10px;">
            <input type="text" class="f" style="top:20.37%;left:79.11%;width:7.19%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:20.37%;left:86.35%;width:9.00%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:22.70%;left:46.15%;width:12.21%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:22.70%;left:58.32%;width:6.83%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:22.79%;left:65.10%;width:6.57%;height:2.36%;font-size:10px;">
            <input type="text" class="f" style="top:22.78%;left:71.71%;width:7.26%;height:2.40%;font-size:10px;">
            <input type="text" class="f" style="top:22.75%;left:79.14%;width:7.19%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:22.75%;left:86.38%;width:9.00%;height:2.43%;font-size:10px;">
            {{-- Farmer name rows P1 --}}
            <input type="text" class="f" style="top:22.93%;left:16.76%;width:27.54%;height:1.00%;font-size:10px;">
            <input type="text" class="f" style="top:24.12%;left:16.76%;width:27.54%;height:1.00%;font-size:10px;">

            {{-- ══ PARCEL 2 ══ --}}
            <input type="text" name="parcel_barangay[]"
                value="{{ old('parcel_barangay.1', isset($farmer) ? ($farmer->farmParcels[1]->farm_location_barangay ?? '') : '') }}"
                autocomplete="off" class="f" style="top:23.11%;left:16.81%;width:27.50%;height:1.00%;font-size:9px;">
            <input type="text" name="parcel_municipality[]"
                value="{{ old('parcel_municipality.1', isset($farmer) ? ($farmer->farmParcels[1]->farm_location_municipality ?? '') : '') }}"
                autocomplete="off" class="f" style="top:24.28%;left:16.81%;width:27.50%;height:1.00%;font-size:9px;">
            <input type="text" name="parcel_area[]"
                value="{{ old('parcel_area.1', isset($farmer) ? ($farmer->farmParcels[1]->total_farm_area_ha ?? '') : '') }}"
                autocomplete="off" class="f" style="top:25.94%;left:23.89%;width:2.31%;height:1.00%;font-size:9px;">
            <input type="text" name="parcel_doc_code[]"
                value="{{ old('parcel_doc_code.1', isset($farmer) ? ($farmer->farmParcels[1]->ownership_document_code ?? '') : '') }}"
                autocomplete="off" class="f" style="top:28.09%;left:22.86%;width:2.67%;height:1.00%;font-size:9px;">
            <input type="checkbox" class="cb" style="top:27.33%;left:30.08%;">
            <input type="checkbox" class="cb" style="top:27.30%;left:37.12%;">
            <input type="checkbox" class="cb" style="top:29.46%;left:30.14%;">
            <input type="checkbox" class="cb" style="top:29.45%;left:37.12%;">
            <input type="checkbox" class="cb" style="top:31.30%;left:24.31%;">
            <input type="checkbox" class="cb" style="top:31.28%;left:10.38%;">
            <input type="text" name="parcel_owner_name[]"
                value="{{ old('parcel_owner_name.1', isset($farmer) ? ($farmer->farmParcels[1]->owner_name ?? '') : '') }}"
                autocomplete="off" class="f" style="top:32.00%;left:26.22%;width:17.99%;height:1.00%;font-size:10px;">
            <input type="text" class="f" style="top:33.14%;left:26.04%;width:17.99%;height:1.00%;font-size:10px;">
            <input type="text" class="f" style="top:30.84%;left:29.40%;width:14.59%;height:1.00%;font-size:10px;">
            <input type="checkbox" class="cb" style="top:32.41%;left:10.36%;">
            <input type="checkbox" class="cb" style="top:33.61%;left:10.36%;">
            <input type="text" name="parcel_crop[]"
                value="{{ old('parcel_crop.1', isset($farmer) ? ($farmer->farmParcels[1]->crop_commodity ?? '') : '') }}"
                autocomplete="off" class="f" style="top:25.17%;left:46.08%;width:12.21%;height:2.43%;font-size:10px;">
            <input type="text" name="parcel_size[]"
                value="{{ old('parcel_size.1', isset($farmer) ? ($farmer->farmParcels[1]->size_ha ?? '') : '') }}"
                autocomplete="off" class="f" style="top:25.09%;left:58.40%;width:6.57%;height:2.43%;font-size:10px;">
            <input type="text" name="parcel_no_head[]"
                value="{{ old('parcel_no_head.1', isset($farmer) ? ($farmer->farmParcels[1]->no_of_head ?? '') : '') }}"
                autocomplete="off" class="f" style="top:25.21%;left:65.07%;width:6.57%;height:2.36%;font-size:10px;">
            <input type="text" name="parcel_farm_type[]"
                value="{{ old('parcel_farm_type.1', isset($farmer) ? ($farmer->farmParcels[1]->farm_type ?? '') : '') }}"
                autocomplete="off" class="f" style="top:25.19%;left:71.79%;width:7.26%;height:2.40%;font-size:10px;">
            <input type="text" name="parcel_organic[]"
                value="{{ old('parcel_organic.1', isset($farmer) ? ($farmer->farmParcels[1]->organic_practitioner ? 'Y' : '') : '') }}"
                autocomplete="off" class="f" style="top:25.10%;left:79.07%;width:7.19%;height:2.43%;font-size:10px;">
            <input type="text" name="parcel_remarks[]"
                value="{{ old('parcel_remarks.1', isset($farmer) ? ($farmer->farmParcels[1]->remarks ?? '') : '') }}"
                autocomplete="off" class="f" style="top:25.15%;left:86.30%;width:9.00%;height:2.43%;font-size:10px;">
            {{-- Extra crop rows parcel 2 --}}
            <input type="text" class="f" style="top:27.43%;left:46.11%;width:12.21%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:27.54%;left:58.43%;width:6.57%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:27.52%;left:65.10%;width:6.57%;height:2.36%;font-size:10px;">
            <input type="text" class="f" style="top:27.53%;left:71.71%;width:7.26%;height:2.40%;font-size:10px;">
            <input type="text" class="f" style="top:27.50%;left:79.06%;width:7.19%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:27.49%;left:86.44%;width:9.00%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:29.89%;left:46.07%;width:12.21%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:29.92%;left:58.46%;width:6.57%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:29.94%;left:65.10%;width:6.57%;height:2.36%;font-size:10px;">
            <input type="text" class="f" style="top:29.88%;left:71.73%;width:7.26%;height:2.40%;font-size:10px;">
            <input type="text" class="f" style="top:29.82%;left:79.10%;width:7.19%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:29.88%;left:86.43%;width:9.00%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:32.18%;left:46.15%;width:12.21%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:32.20%;left:58.43%;width:6.57%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:32.28%;left:65.10%;width:6.57%;height:2.36%;font-size:10px;">
            <input type="text" class="f" style="top:32.19%;left:71.76%;width:7.26%;height:2.40%;font-size:10px;">
            <input type="text" class="f" style="top:32.22%;left:79.08%;width:7.19%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:32.26%;left:86.26%;width:9.00%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:34.60%;left:46.10%;width:12.21%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:34.60%;left:58.49%;width:6.57%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:34.65%;left:65.13%;width:6.57%;height:2.36%;font-size:10px;">
            <input type="text" class="f" style="top:34.65%;left:71.73%;width:7.26%;height:2.40%;font-size:10px;">
            <input type="text" class="f" style="top:34.61%;left:79.08%;width:7.26%;height:2.40%;font-size:10px;">
            <input type="text" class="f" style="top:34.65%;left:86.36%;width:9.00%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:34.96%;left:17.39%;width:27.41%;height:0.79%;font-size:10px;">
            <input type="text" class="f" style="top:36.08%;left:17.39%;width:27.41%;height:0.82%;font-size:10px;">
            <input type="text" class="f" style="top:36.98%;left:86.26%;width:9.00%;height:2.43%;font-size:10px;">

            {{-- ══ PARCEL 3 ══ --}}
            <input type="text" name="parcel_barangay[]"
                value="{{ old('parcel_barangay.2', isset($farmer) ? ($farmer->farmParcels[2]->farm_location_barangay ?? '') : '') }}"
                autocomplete="off" class="f" style="top:35.08%;left:16.81%;width:27.50%;height:1.00%;font-size:9px;">
            <input type="text" name="parcel_municipality[]"
                value="{{ old('parcel_municipality.2', isset($farmer) ? ($farmer->farmParcels[2]->farm_location_municipality ?? '') : '') }}"
                autocomplete="off" class="f" style="top:36.22%;left:16.81%;width:27.50%;height:1.00%;font-size:9px;">
            <input type="text" name="parcel_area[]"
                value="{{ old('parcel_area.2', isset($farmer) ? ($farmer->farmParcels[2]->total_farm_area_ha ?? '') : '') }}"
                autocomplete="off" class="f" style="top:37.96%;left:24.47%;width:2.44%;height:0.73%;font-size:9px;">
            <input type="text" name="parcel_doc_code[]"
                value="{{ old('parcel_doc_code.2', isset($farmer) ? ($farmer->farmParcels[2]->ownership_document_code ?? '') : '') }}"
                autocomplete="off" class="f" style="top:40.11%;left:23.26%;width:2.80%;height:0.73%;font-size:9px;">
            <input type="checkbox" class="cb" style="top:39.10%;left:30.62%;">
            <input type="checkbox" class="cb" style="top:39.09%;left:37.65%;">
            <input type="checkbox" class="cb" style="top:41.25%;left:30.64%;">
            <input type="checkbox" class="cb" style="top:41.25%;left:37.67%;">
            <input type="checkbox" class="cb" style="top:43.07%;left:24.82%;">
            <input type="checkbox" class="cb" style="top:43.06%;left:10.90%;">
            <input type="text" name="parcel_owner_name[]"
                value="{{ old('parcel_owner_name.2', isset($farmer) ? ($farmer->farmParcels[2]->owner_name ?? '') : '') }}"
                autocomplete="off" class="f" style="top:44.02%;left:26.80%;width:17.86%;height:0.73%;font-size:10px;">
            <input type="text" class="f" style="top:45.15%;left:26.58%;width:17.99%;height:0.73%;font-size:10px;">
            <input type="text" class="f" style="top:42.86%;left:29.98%;width:14.50%;height:0.73%;font-size:10px;">
            <input type="checkbox" class="cb" style="top:44.20%;left:10.91%;">
            <input type="checkbox" class="cb" style="top:45.38%;left:10.90%;">
            <input type="text" name="parcel_crop[]"
                value="{{ old('parcel_crop.2', isset($farmer) ? ($farmer->farmParcels[2]->crop_commodity ?? '') : '') }}"
                autocomplete="off" class="f" style="top:37.03%;left:46.07%;width:12.21%;height:2.43%;font-size:10px;">
            <input type="text" name="parcel_size[]"
                value="{{ old('parcel_size.2', isset($farmer) ? ($farmer->farmParcels[2]->size_ha ?? '') : '') }}"
                autocomplete="off" class="f" style="top:37.04%;left:58.49%;width:6.57%;height:2.43%;font-size:10px;">
            <input type="text" name="parcel_no_head[]"
                value="{{ old('parcel_no_head.2', isset($farmer) ? ($farmer->farmParcels[2]->no_of_head ?? '') : '') }}"
                autocomplete="off" class="f" style="top:37.00%;left:65.05%;width:6.57%;height:2.36%;font-size:10px;">
            <input type="text" name="parcel_farm_type[]"
                value="{{ old('parcel_farm_type.2', isset($farmer) ? ($farmer->farmParcels[2]->farm_type ?? '') : '') }}"
                autocomplete="off" class="f" style="top:37.00%;left:71.73%;width:7.26%;height:2.40%;font-size:10px;">
            <input type="text" name="parcel_organic[]"
                value="{{ old('parcel_organic.2', isset($farmer) ? ($farmer->farmParcels[2]->organic_practitioner ? 'Y' : '') : '') }}"
                autocomplete="off" class="f" style="top:37.02%;left:79.11%;width:7.26%;height:2.40%;font-size:10px;">
            <input type="text" name="parcel_remarks[]"
                value="{{ old('parcel_remarks.2', isset($farmer) ? ($farmer->farmParcels[2]->remarks ?? '') : '') }}"
                autocomplete="off" class="f" style="top:36.98%;left:86.26%;width:9.00%;height:2.43%;font-size:10px;">
            {{-- Extra crop rows parcel 3 --}}
            <input type="text" class="f" style="top:39.29%;left:46.12%;width:12.21%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:39.40%;left:58.49%;width:6.57%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:39.39%;left:65.10%;width:6.57%;height:2.36%;font-size:10px;">
            <input type="text" class="f" style="top:39.42%;left:71.76%;width:7.26%;height:2.40%;font-size:10px;">
            <input type="text" class="f" style="top:39.36%;left:79.08%;width:7.26%;height:2.40%;font-size:10px;">
            <input type="text" class="f" style="top:39.27%;left:86.30%;width:9.00%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:41.74%;left:46.15%;width:12.21%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:41.75%;left:58.51%;width:6.57%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:41.70%;left:65.07%;width:6.57%;height:2.36%;font-size:10px;">
            <input type="text" class="f" style="top:41.73%;left:71.73%;width:7.26%;height:2.40%;font-size:10px;">
            <input type="text" class="f" style="top:41.72%;left:79.00%;width:7.26%;height:2.40%;font-size:10px;">
            <input type="text" class="f" style="top:41.72%;left:86.23%;width:9.00%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:44.04%;left:46.01%;width:12.48%;height:2.37%;font-size:10px;">
            <input type="text" class="f" style="top:44.09%;left:58.48%;width:6.57%;height:2.43%;font-size:10px;">
            <input type="text" class="f" style="top:44.12%;left:65.04%;width:6.57%;height:2.36%;font-size:10px;">
            <input type="text" class="f" style="top:44.11%;left:71.70%;width:7.26%;height:2.40%;font-size:10px;">
            <input type="text" class="f" style="top:44.06%;left:78.97%;width:7.26%;height:2.40%;font-size:10px;">
            <input type="text" class="f" style="top:44.07%;left:86.25%;width:9.00%;height:2.43%;font-size:10px;">

            {{-- DECLARATION / SIGNATURE AREA --}}
            <input type="text" name="p2_date" value="{{ old('p2_date') }}" autocomplete="off" class="f"
                style="top:62.66%;left:5.29%;width:17.14%;height:1.47%;font-size:11px;">
            <input type="text" name="p2_printed_name"
                value="{{ old('p2_printed_name', isset($farmer) ? strtoupper($farmer->first_name . ' ' . $farmer->middle_name . ' ' . $farmer->surname) : '') }}"
                autocomplete="off" class="f" style="top:62.66%;left:24.28%;width:26.67%;height:1.47%;font-size:11px;">
            <input type="text" name="p2_verified_by_1" value="{{ old('p2_verified_by_1') }}" autocomplete="off" class="f"
                style="top:69.40%;left:5.42%;width:26.59%;height:1.47%;font-size:11px;">
            <input type="text" name="p2_verified_by_2" value="{{ old('p2_verified_by_2') }}" autocomplete="off" class="f"
                style="top:69.40%;left:34.83%;width:29.82%;height:1.47%;font-size:11px;">
            <input type="text" name="p2_verified_by_3" value="{{ old('p2_verified_by_3') }}" autocomplete="off" class="f"
                style="top:69.39%;left:67.37%;width:26.59%;height:1.47%;font-size:11px;">
            <input type="text" name="p2_printed_name_barangay" value="{{ old('p2_printed_name_barangay') }}"
                autocomplete="off" class="f" style="top:92.02%;left:5.48%;width:26.59%;height:1.47%;font-size:11px;">
            <input type="text" name="p2_printed_name_mao" value="{{ old('p2_printed_name_mao') }}" autocomplete="off"
                class="f" style="top:92.00%;left:34.81%;width:29.82%;height:1.47%;font-size:11px;">
            <input type="text" name="p2_printed_name_cafc" value="{{ old('p2_printed_name_cafc') }}" autocomplete="off"
                class="f" style="top:92.00%;left:67.48%;width:26.59%;height:1.47%;font-size:11px;">

        </div>

        {{-- SUBMIT --}}
        <div style="width:850px;margin:0 auto 32px auto;display:flex;gap:12px;">
            <button type="submit"
                class="bg-primary hover:bg-primary-dark text-white font-semibold px-8 py-2.5 rounded-md transition text-sm">
                Register Farmer
            </button>
            <a href="{{ route('farmers.index') }}"
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
        mkDig('.mob-d', 'mobH');
        mkDig('.land-d', 'landH');
        mkDig('.con-d', 'conH');
        mkDig('.dateadmin-d', 'dateAdminH');
        mkDig('.refnum-d', 'refH');
        const dobBoxes = mkDig('.dob-d', 'dobH');
        document.getElementById('farmerForm').addEventListener('submit', () => {
            const v = dobBoxes.map(b => b.value).join('');
            if (v.length === 8) document.getElementById('dobH').value = `${v.slice(4, 8)}-${v.slice(0, 2)}-${v.slice(2, 4)}`;
        });
        document.getElementById('farmerForm').addEventListener('submit', () => {
            const p1 = document.getElementById('pob1').value;
            const p2 = document.getElementById('pob2').value;
            const p3 = document.getElementById('pob3').value;
            document.getElementById('pobH').value = [p1, p2, p3].filter(Boolean).join(', ');
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

        function syncStub() {
            document.getElementById('stubSurname').value = document.getElementById('mainSurname').value;
            document.getElementById('stubFirstName').value = document.getElementById('mainFirstName').value;
            document.getElementById('stubMiddleName').value = document.getElementById('mainMiddleName').value;
            document.getElementById('stubExtName').value = document.getElementById('mainExtName').value;
        }
        ['mainSurname', 'mainFirstName', 'mainMiddleName', 'mainExtName'].forEach(id => {
            document.getElementById(id).addEventListener('input', syncStub);
        });
    </script>

@endsection
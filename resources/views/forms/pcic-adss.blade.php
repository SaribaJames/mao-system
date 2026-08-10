@extends('layouts.app')

@section('content')

    <div class="mb-4 flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">PCIC ADSS Form</h2>
            <p class="text-gray-500 text-sm mt-1">Accident and Dismemberment Security Scheme</p>
        </div>
        <a href="{{ route('forms.index') }}"
            class="border border-gray-300 text-gray-600 hover:bg-gray-50 text-sm font-medium px-4 py-2 rounded transition">
            ← Back
        </a>
    </div>

    {{-- Farmer Selection --}}
    <div class="bg-white rounded border border-gray-300 p-5 mb-4">
        <h3 class="text-sm font-bold text-gray-800 mb-3">Select Farmer (Optional)</h3>
        <p class="text-xs text-gray-400 mb-3">Select a farmer to auto-fill their information, or fill manually.</p>
        <form method="GET" action="{{ route('forms.pcic-adss') }}" class="flex gap-3">
            <select name="farmer_id"
                class="flex-1 border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-primary">
                <option value="">-- Select Farmer to Auto-fill --</option>
                @foreach($farmers as $f)
                    <option value="{{ $f->id }}" {{ request('farmer_id') == $f->id ? 'selected' : '' }}>
                        {{ $f->surname }}, {{ $f->first_name }} — {{ $f->barangay?->name }}
                    </option>
                @endforeach
            </select>
            <button type="submit"
                class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded text-sm font-medium transition">
                Auto-fill
            </button>
            <a href="{{ route('forms.pcic-adss') }}"
                class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-4 py-2 rounded text-sm transition">
                Clear
            </a>
        </form>
    </div>

    <form method="POST" action="{{ route('forms.pcic-adss.pdf') }}" target="_blank">
        @csrf
        <input type="hidden" name="farmer_id" value="{{ $farmer?->id }}">

        {{-- Paper Document --}}
        <div class="bg-white border border-gray-300 rounded mx-auto"
            style="width: 816px; min-height: 1056px; padding: 40px 50px; font-family: Arial, sans-serif; font-size: 11px;">

            {{-- Header --}}
            <div style="text-align: center; margin-bottom: 15px;">
                <p style="font-size: 10px; margin: 0;">Republic of the Philippines</p>
                <p style="font-size: 14px; font-weight: bold; margin: 2px 0;">PHILIPPINE CROP INSURANCE CORPORATION</p>
                <p style="font-size: 10px; margin: 0;">Regional Office No. ___</p>
                <div style="margin-top: 10px;">
                    <p style="font-size: 13px; font-weight: bold; text-decoration: underline; margin: 0;">APPLICATION FOR
                        INSURANCE</p>
                    <p style="font-size: 11px; margin: 3px 0;">Accident and Dismemberment Security Scheme (ADSS)</p>
                </div>
                <div style="display: flex; justify-content: space-between; margin-top: 8px; font-size: 10px;">
                    <p style="margin: 0;">Kindly fill out all entries and tick all boxes (✓) as appropriate</p>
                    <p style="margin: 0;">Date of Application:
                        <input type="text" name="date_application" value="{{ date('m/d/Y') }}"
                            style="border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px; width: 80px;" />
                    </p>
                </div>
                <p style="font-size: 9px; text-align: right; margin: 2px 0; color: #555;">ADSS Form No. 1 | Rev. 2025/Sep
                </p>
            </div>

            {{-- Section A --}}
            <div style="border: 1px solid #333; margin-bottom: 10px;">
                <div
                    style="background: #e8e8e8; padding: 3px 6px; font-weight: bold; font-size: 10px; border-bottom: 1px solid #333;">
                    A. BASIC APPLICANT INFORMATION
                </div>
                <div style="padding: 8px;">

                    {{-- A.1 Name & A.2 Contact --}}
                    <div style="display: table; width: 100%; margin-bottom: 8px;">
                        <div style="display: table-cell; width: 70%;">
                            <p style="font-size: 10px; font-weight: bold; margin: 0 0 4px;">A.1 Name</p>
                            <div style="display: table; width: 100%;">
                                <div style="display: table-cell; width: 33%; padding-right: 5px;">
                                    <input type="text" name="last_name" value="{{ $farmer?->surname }}"
                                        style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 11px;" />
                                    <p style="font-size: 8px; text-align: center; margin: 1px 0; color: #555;">Last Name</p>
                                </div>
                                <div style="display: table-cell; width: 33%; padding-right: 5px;">
                                    <input type="text" name="first_name" value="{{ $farmer?->first_name }}"
                                        style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 11px;" />
                                    <p style="font-size: 8px; text-align: center; margin: 1px 0; color: #555;">First Name
                                    </p>
                                </div>
                                <div style="display: table-cell; width: 22%; padding-right: 5px;">
                                    <input type="text" name="middle_name" value="{{ $farmer?->middle_name }}"
                                        style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 11px;" />
                                    <p style="font-size: 8px; text-align: center; margin: 1px 0; color: #555;">Middle Name
                                    </p>
                                </div>
                                <div style="display: table-cell; width: 12%;">
                                    <input type="text" name="suffix" value="{{ $farmer?->extension_name }}"
                                        style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 11px;" />
                                    <p style="font-size: 8px; text-align: center; margin: 1px 0; color: #555;">Suffix</p>
                                </div>
                            </div>
                        </div>
                        <div style="display: table-cell; width: 30%; padding-left: 10px; vertical-align: top;">
                            <p style="font-size: 10px; font-weight: bold; margin: 0 0 4px;">A.2 Contact Number</p>
                            <input type="text" name="contact_number" value="{{ $farmer?->mobile_number }}"
                                style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 11px;" />
                        </div>
                    </div>

                    {{-- A.3 Address & A.4 Date of Birth --}}
                    <div style="display: table; width: 100%; margin-bottom: 8px;">
                        <div style="display: table-cell; width: 70%;">
                            <p style="font-size: 10px; font-weight: bold; margin: 0 0 4px;">A.3 Address</p>
                            <div style="display: table; width: 100%;">
                                <div style="display: table-cell; width: 30%; padding-right: 5px;">
                                    <input type="text" name="street"
                                        value="{{ $farmer?->house_lot_number }} {{ $farmer?->street }}"
                                        style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px;" />
                                    <p style="font-size: 8px; text-align: center; margin: 1px 0; color: #555;">No. &
                                        Street/Sitio</p>
                                </div>
                                <div style="display: table-cell; width: 25%; padding-right: 5px;">
                                    <input type="text" name="barangay" value="{{ $farmer?->barangay?->name }}"
                                        style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px;" />
                                    <p style="font-size: 8px; text-align: center; margin: 1px 0; color: #555;">Barangay</p>
                                </div>
                                <div style="display: table-cell; width: 25%; padding-right: 5px;">
                                    <input type="text" name="municipality" value="{{ $farmer?->municipality }}"
                                        style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px;" />
                                    <p style="font-size: 8px; text-align: center; margin: 1px 0; color: #555;">
                                        Municipality/City</p>
                                </div>
                                <div style="display: table-cell; width: 20%;">
                                    <input type="text" name="province" value="{{ $farmer?->province }}"
                                        style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px;" />
                                    <p style="font-size: 8px; text-align: center; margin: 1px 0; color: #555;">Province</p>
                                </div>
                            </div>
                        </div>
                        <div style="display: table-cell; width: 30%; padding-left: 10px; vertical-align: top;">
                            <p style="font-size: 10px; font-weight: bold; margin: 0 0 4px;">A.4 Date of Birth</p>
                            <input type="text" name="date_of_birth"
                                value="{{ $farmer?->date_of_birth ? \Carbon\Carbon::parse($farmer->date_of_birth)->format('m/d/Y') : '' }}"
                                style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 11px;" />
                            <p style="font-size: 8px; text-align: center; margin: 1px 0; color: #555;">(mm/dd/yyyy)</p>
                        </div>
                    </div>

                    {{-- A.5 Sex & A.6 Special Sector --}}
                    <div style="margin-bottom: 8px;">
                        <span style="font-size: 10px; font-weight: bold;">A.5 Sex</span>
                        <span style="margin-left: 10px;">
                            <input type="checkbox" name="sex_male" {{ $farmer?->sex == 'male' ? 'checked' : '' }} /> Male
                            &nbsp;
                            <input type="checkbox" name="sex_female" {{ $farmer?->sex == 'female' ? 'checked' : '' }} />
                            Female
                        </span>
                        <span style="margin-left: 20px; font-size: 10px; font-weight: bold;">A.6 Are you part of a special
                            sector?</span>
                        <span style="margin-left: 10px; font-size: 10px;">
                            <input type="checkbox" name="is_pwd" {{ $farmer?->is_pwd ? 'checked' : '' }} /> PWD &nbsp;
                            <input type="checkbox" /> Senior Citizen &nbsp;
                            <input type="checkbox" /> Youth (18-30 y/o) &nbsp;
                            <input type="checkbox" name="is_indigenous" {{ $farmer?->is_indigenous ? 'checked' : '' }} />
                            Indigenous People
                        </span>
                    </div>

                    {{-- A.7 Civil Status --}}
                    <div style="margin-bottom: 8px;">
                        <span style="font-size: 10px; font-weight: bold;">A.7 Civil Status</span>
                        <span style="margin-left: 10px; font-size: 10px;">
                            <input type="checkbox" {{ $farmer?->civil_status == 'single' ? 'checked' : '' }} /> Single &nbsp;
                            <input type="checkbox" {{ $farmer?->civil_status == 'married' ? 'checked' : '' }} /> Married
                            &nbsp;
                            <input type="checkbox" {{ $farmer?->civil_status == 'widowed' ? 'checked' : '' }} /> Widow/er
                            &nbsp;
                            <input type="checkbox" {{ $farmer?->civil_status == 'separated' ? 'checked' : '' }} /> Separated
                        </span>
                        <span style="margin-left: 10px; font-size: 10px;">If married, Name of Spouse:
                            <input type="text" name="spouse_name" value="{{ $farmer?->spouse_name }}"
                                style="border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px; width: 150px;" />
                        </span>
                    </div>

                    {{-- A.8 Occupation --}}
                    <div style="margin-bottom: 8px;">
                        <span style="font-size: 10px; font-weight: bold;">A.8 Occupation/Livelihood</span>
                        <input type="text" name="occupation"
                            value="{{ $farmer ? ucfirst(str_replace('_', ' ', $farmer->main_livelihood ?? '')) : '' }}"
                            style="border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px; width: 300px; margin-left: 5px;" />
                        <span style="margin-left: 20px; font-size: 10px;">Work Address:
                            <input type="text" name="work_address"
                                style="border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px; width: 200px;" />
                        </span>
                    </div>

                    {{-- A.9 Insurance Coverage --}}
                    <div style="margin-bottom: 8px;">
                        <p style="font-size: 10px; font-weight: bold; margin: 0 0 4px;">A.9 Desired Insurance Coverage (in
                            thousand pesos)</p>
                        <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 2px; font-size: 10px;">
                            @foreach(['15T', '20T', '25T', '30T', '35T', '40T', '45T', '50T', '55T', '60T', '65T', '70T', '75T', '80T', '85T', '90T', '95T', '100T'] as $plan)
                                <span><input type="checkbox" name="plan_{{ strtolower($plan) }}" /> Plan {{ $plan }}</span>
                            @endforeach
                        </div>
                    </div>

                    {{-- A.10 Beneficiaries --}}
                    <div style="margin-bottom: 8px;">
                        <p style="font-size: 10px; font-weight: bold; margin: 0 0 4px;">A.10 Name of Legal Beneficiary/ies
                        </p>

                        <div style="margin-bottom: 5px;">
                            <span style="font-size: 10px;">Primary Beneficiary:</span>
                            <div style="display: table; width: 100%; margin-top: 3px;">
                                <div style="display: table-cell; width: 25%; padding-right: 5px;">
                                    <input type="text" name="primary_last"
                                        style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px;" />
                                    <p style="font-size: 8px; margin: 1px 0; color: #555; text-align: center;">Last Name</p>
                                </div>
                                <div style="display: table-cell; width: 25%; padding-right: 5px;">
                                    <input type="text" name="primary_first"
                                        style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px;" />
                                    <p style="font-size: 8px; margin: 1px 0; color: #555; text-align: center;">First Name
                                    </p>
                                </div>
                                <div style="display: table-cell; width: 25%; padding-right: 5px;">
                                    <input type="text" name="primary_middle"
                                        style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px;" />
                                    <p style="font-size: 8px; margin: 1px 0; color: #555; text-align: center;">Middle Name
                                    </p>
                                </div>
                                <div style="display: table-cell; width: 10%; padding-right: 5px;">
                                    <input type="text" name="primary_suffix"
                                        style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px;" />
                                    <p style="font-size: 8px; margin: 1px 0; color: #555; text-align: center;">Suffix</p>
                                </div>
                                <div style="display: table-cell; width: 15%; vertical-align: top;">
                                    <p style="font-size: 9px; margin: 0;">Relationship: <input type="text"
                                            name="primary_relationship"
                                            style="border: none; border-bottom: 1px solid #333; outline: none; font-size: 9px; width: 80px;" />
                                    </p>
                                    <p style="font-size: 9px; margin: 2px 0;">Birthdate: <input type="text"
                                            name="primary_birthdate"
                                            style="border: none; border-bottom: 1px solid #333; outline: none; font-size: 9px; width: 70px;" />
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div style="margin-bottom: 5px;">
                            <span style="font-size: 10px;">Secondary Beneficiary:</span>
                            <div style="display: table; width: 100%; margin-top: 3px;">
                                <div style="display: table-cell; width: 25%; padding-right: 5px;">
                                    <input type="text" name="secondary_last"
                                        style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px;" />
                                    <p style="font-size: 8px; margin: 1px 0; color: #555; text-align: center;">Last Name</p>
                                </div>
                                <div style="display: table-cell; width: 25%; padding-right: 5px;">
                                    <input type="text" name="secondary_first"
                                        style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px;" />
                                    <p style="font-size: 8px; margin: 1px 0; color: #555; text-align: center;">First Name
                                    </p>
                                </div>
                                <div style="display: table-cell; width: 25%; padding-right: 5px;">
                                    <input type="text" name="secondary_middle"
                                        style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px;" />
                                    <p style="font-size: 8px; margin: 1px 0; color: #555; text-align: center;">Middle Name
                                    </p>
                                </div>
                                <div style="display: table-cell; width: 10%; padding-right: 5px;">
                                    <input type="text" name="secondary_suffix"
                                        style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px;" />
                                    <p style="font-size: 8px; margin: 1px 0; color: #555; text-align: center;">Suffix</p>
                                </div>
                                <div style="display: table-cell; width: 15%; vertical-align: top;">
                                    <p style="font-size: 9px; margin: 0;">Relationship: <input type="text"
                                            name="secondary_relationship"
                                            style="border: none; border-bottom: 1px solid #333; outline: none; font-size: 9px; width: 80px;" />
                                    </p>
                                    <p style="font-size: 9px; margin: 2px 0;">Birthdate: <input type="text"
                                            name="secondary_birthdate"
                                            style="border: none; border-bottom: 1px solid #333; outline: none; font-size: 9px; width: 70px;" />
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div>
                            <span style="font-size: 10px;">Guardian <span style="font-size: 9px; font-style: italic;">(if
                                    beneficiary is a minor)</span>:</span>
                            <div style="display: table; width: 100%; margin-top: 3px;">
                                <div style="display: table-cell; width: 25%; padding-right: 5px;">
                                    <input type="text" name="guardian_last"
                                        style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px;" />
                                    <p style="font-size: 8px; margin: 1px 0; color: #555; text-align: center;">Last Name</p>
                                </div>
                                <div style="display: table-cell; width: 25%; padding-right: 5px;">
                                    <input type="text" name="guardian_first"
                                        style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px;" />
                                    <p style="font-size: 8px; margin: 1px 0; color: #555; text-align: center;">First Name
                                    </p>
                                </div>
                                <div style="display: table-cell; width: 25%; padding-right: 5px;">
                                    <input type="text" name="guardian_middle"
                                        style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px;" />
                                    <p style="font-size: 8px; margin: 1px 0; color: #555; text-align: center;">Middle Name
                                    </p>
                                </div>
                                <div style="display: table-cell; width: 10%; padding-right: 5px;">
                                    <input type="text" name="guardian_suffix"
                                        style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px;" />
                                    <p style="font-size: 8px; margin: 1px 0; color: #555; text-align: center;">Suffix</p>
                                </div>
                                <div style="display: table-cell; width: 15%; vertical-align: top;">
                                    <p style="font-size: 9px; margin: 0;">Relationship: <input type="text"
                                            name="guardian_relationship"
                                            style="border: none; border-bottom: 1px solid #333; outline: none; font-size: 9px; width: 80px;" />
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- A.11 Payment Method --}}
                    <div style="margin-bottom: 8px;">
                        <p style="font-size: 10px; font-weight: bold; margin: 0 0 4px;">A.11 In the event of claim, what is
                            your preferred method of receiving indemnity payment?</p>
                        <span style="font-size: 10px;">
                            <input type="checkbox" name="payment_landbank" /> LandBank or DBP &nbsp;&nbsp;
                            <input type="checkbox" name="payment_palawan" /> Palawan Express &nbsp;&nbsp;
                            <input type="checkbox" name="payment_gcash" /> GCash &nbsp;&nbsp;
                            <input type="checkbox" name="payment_others" /> Others: <input type="text"
                                name="payment_others_specify"
                                style="border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px; width: 100px;" />
                        </span>
                    </div>

                    {{-- A.12 Family Member --}}
                    <div style="margin-bottom: 5px;">
                        <p style="font-size: 10px; font-weight: bold; margin: 0 0 4px;">A.12 Are you a family member or a
                            worker of a farmer who has an active agricultural/crop insurance coverage with PCIC?</p>
                        <span style="font-size: 10px;">
                            <input type="checkbox" name="is_family_yes" /> Yes &nbsp;
                            <input type="checkbox" name="is_family_no" /> No
                        </span>
                        <span style="margin-left: 20px; font-size: 10px;">If yes, indicate:</span>
                        <div style="margin-top: 4px; font-size: 10px;">
                            Name of Farmer: <input type="text" name="farmer_name_insured"
                                style="border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px; width: 200px;" />
                            &nbsp; Relationship: <input type="text" name="farmer_relationship"
                                style="border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px; width: 100px;" />
                        </div>
                        <div style="margin-top: 4px; font-size: 10px;">
                            Address: <input type="text" name="farmer_address"
                                style="border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px; width: 400px;" />
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section B --}}
            <div style="border: 1px solid #333; margin-bottom: 10px;">
                <div
                    style="background: #e8e8e8; padding: 3px 6px; font-weight: bold; font-size: 10px; border-bottom: 1px solid #333;">
                    B. CERTIFICATION AND DATA PRIVACY CONSENT STATEMENT
                </div>
                <div style="padding: 8px; display: table; width: 100%;">
                    <div style="display: table-cell; width: 65%; padding-right: 15px; font-size: 10px; line-height: 1.5;">
                        <p style="margin: 0 0 5px;"><input type="checkbox" /> I hereby certify that the foregoing answers
                            and statements are complete, true and correct. If the application is approved, the insurance
                            shall be deemed based upon the statements contained herein. I further agree that PCIC reserves
                            the right to reject and/or void the insurance if found that there is
                            fraud/concealment/misrepresentation on this statement material to the risk.</p>
                        <p style="margin: 0;"><input type="checkbox" /> By submitting this application, I hereby consent to
                            the collection, use, processing, and disclosure of my sensitive personal data in accordance with
                            the Data Privacy Act of 2012.</p>
                    </div>
                    <div style="display: table-cell; width: 35%; vertical-align: top; text-align: center;">
                        <div style="border-top: 1px solid #333; margin-top: 50px; padding-top: 5px;">
                            <p style="font-size: 10px; margin: 0;">Signature / Thumb Mark over Printed Name</p>
                            <p style="font-size: 10px; font-weight: bold; margin: 2px 0;">Applicant</p>
                            <p style="font-size: 10px; margin: 10px 0 2px;">Date: <input type="text" name="applicant_date"
                                    style="border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px; width: 80px;" />
                            </p>
                            <p style="font-size: 9px; color: #555;">(mm/dd/yyyy)</p>
                        </div>
                    </div>
                </div>
                <div style="padding: 8px; display: table; width: 100%; border-top: 1px solid #ccc;">
                    <div style="display: table-cell; width: 65%; padding-right: 15px; font-size: 10px;">
                        <p style="font-weight: bold; margin: 0 0 3px;">Parental consent for minor applicants only</p>
                        <p style="margin: 0;"><input type="checkbox" /> By signing, I'm allowing my child to avail the ADSS
                            Insurance.</p>
                    </div>
                    <div style="display: table-cell; width: 35%; text-align: center; vertical-align: top;">
                        <div style="border-top: 1px solid #333; margin-top: 30px; padding-top: 5px;">
                            <p style="font-size: 10px; margin: 0;">Signature over Printed Name</p>
                            <p style="font-size: 10px; font-weight: bold; margin: 2px 0;">Parent/Guardian</p>
                            <p style="font-size: 10px; margin: 10px 0 2px;">Date: <input type="text" name="guardian_date"
                                    style="border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px; width: 80px;" />
                            </p>
                            <p style="font-size: 9px; color: #555;">(mm/dd/yyyy)</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section C --}}
            <div style="border: 1px solid #333;">
                <div
                    style="background: #e8e8e8; padding: 3px 6px; font-weight: bold; font-size: 10px; border-bottom: 1px solid #333;">
                    C. FOR PCIC USE ONLY
                </div>
                <div style="display: table; width: 100%;">
                    <div style="display: table-cell; width: 20%; border-right: 1px solid #333; padding: 5px;">
                        <p style="font-size: 9px; font-weight: bold; margin: 0 0 2px;">C.1 COC Number</p>
                        <input type="text" name="coc_number"
                            style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px;" />
                    </div>
                    <div style="display: table-cell; width: 20%; border-right: 1px solid #333; padding: 5px;">
                        <p style="font-size: 9px; font-weight: bold; margin: 0 0 2px;">C.2 Date Issued</p>
                        <input type="text" name="date_issued"
                            style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px;" />
                        <p style="font-size: 8px; color: #555; margin: 1px 0;">(mm/dd/yyyy)</p>
                    </div>
                    <div style="display: table-cell; width: 20%; border-right: 1px solid #333; padding: 5px;">
                        <p style="font-size: 9px; font-weight: bold; margin: 0 0 2px;">C.3 Sum Insured</p>
                        <input type="text" name="sum_insured"
                            style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px;" />
                        <p style="font-size: 8px; color: #555; margin: 1px 0;">(PhP)</p>
                    </div>
                    <div style="display: table-cell; width: 20%; border-right: 1px solid #333; padding: 5px;">
                        <p style="font-size: 9px; font-weight: bold; margin: 0 0 2px;">C.4 Premium Amount</p>
                        <input type="text" name="premium_amount"
                            style="width: 100%; border: none; border-bottom: 1px solid #333; outline: none; font-size: 10px;" />
                        <p style="font-size: 8px; color: #555; margin: 1px 0;">(PhP)</p>
                    </div>
                    <div style="display: table-cell; width: 20%; padding: 5px;">
                        <p style="font-size: 9px; font-weight: bold; margin: 0 0 2px;">C.5 Period of Cover</p>
                        <p style="font-size: 9px; margin: 0;">From: <input type="text" name="cover_from"
                                style="border: none; border-bottom: 1px solid #333; outline: none; font-size: 9px; width: 60px;" />
                        </p>
                        <p style="font-size: 9px; margin: 3px 0;">To: <input type="text" name="cover_to"
                                style="border: none; border-bottom: 1px solid #333; outline: none; font-size: 9px; width: 60px;" />
                        </p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Buttons --}}
        <div class="flex gap-3 mt-4 justify-center" style="width: 816px; margin: 16px auto 0;">
            <button type="submit"
                class="bg-primary hover:bg-primary-dark text-white font-semibold px-8 py-3 rounded transition flex items-center gap-2">
                <i class="fa-solid fa-file-pdf"></i> Download as PDF
            </button>
            <a href="{{ route('forms.index') }}"
                class="border border-gray-300 text-gray-600 hover:bg-gray-50 font-medium px-8 py-3 rounded transition">
                Cancel
            </a>
        </div>

    </form>

@endsection
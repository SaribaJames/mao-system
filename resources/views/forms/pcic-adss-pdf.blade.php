<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>PCIC ADSS Form</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #333;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
        }

        .header h1 {
            font-size: 13px;
            font-weight: bold;
        }

        .header p {
            font-size: 9px;
        }

        .title {
            text-align: center;
            margin-bottom: 8px;
        }

        .title h2 {
            font-size: 12px;
            font-weight: bold;
            text-decoration: underline;
        }

        .title p {
            font-size: 10px;
        }

        .section {
            border: 1px solid #333;
            margin-bottom: 8px;
        }

        .section-header {
            background: #e8e8e8;
            padding: 3px 6px;
            font-weight: bold;
            font-size: 9px;
            border-bottom: 1px solid #333;
        }

        .section-body {
            padding: 6px;
        }

        .row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .cell {
            display: table-cell;
            vertical-align: top;
        }

        .field-label {
            font-size: 8px;
            color: #555;
            border-top: 1px solid #333;
            padding-top: 1px;
            text-align: center;
        }

        .field-value {
            font-size: 10px;
            font-weight: bold;
            min-height: 15px;
            border-bottom: 1px solid #333;
            padding-bottom: 1px;
        }

        .checkbox-area {
            font-size: 9px;
            margin-bottom: 4px;
        }

        /* Font-independent checkbox — a plain CSS square instead of a
           unicode glyph, since DomPDF's font substitution doesn't
           reliably support ✓ and was rendering it as "?". */
        .chk {
            display: inline-block;
            width: 8px;
            height: 8px;
            border: 1px solid #333;
            margin-right: 3px;
            vertical-align: middle;
            background: #fff;
        }

        .chk.checked {
            background: #333;
        }

        .signature-box {
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #333;
            margin-top: 35px;
            padding-top: 3px;
            font-size: 9px;
        }

        .for-pcic {
            display: table;
            width: 100%;
        }

        .for-pcic-cell {
            display: table-cell;
            border-right: 1px solid #333;
            padding: 5px;
        }

        .for-pcic-cell:last-child {
            border-right: none;
        }

        .date-info {
            font-size: 9px;
            text-align: right;
            margin-bottom: 5px;
        }

        table.beneficiary {
            width: 100%;
            border-collapse: collapse;
        }

        table.beneficiary td {
            padding: 2px 3px;
            font-size: 9px;
        }
    </style>
</head>

<body>

    {{-- Header --}}
    <div class="header">
        <p>Republic of the Philippines</p>
        <h1>PHILIPPINE CROP INSURANCE CORPORATION</h1>
        <p>Regional Office No. ___</p>
    </div>

    <div class="title">
        <h2>APPLICATION FOR INSURANCE</h2>
        <p>Accident and Dismemberment Security Scheme (ADSS)</p>
        <p style="font-size:8px; text-align:right;">ADSS Form No. 1 | Rev. 2025/Sep</p>
    </div>

    <div style="display:table; width:100%; margin-bottom:5px;">
        <div style="display:table-cell; font-size:9px;">Kindly fill out all entries and tick all boxes as
            appropriate</div>
        <div style="display:table-cell; text-align:right; font-size:9px;">Date of Application:
            <strong>{{ $request->date_application }}</strong></div>
    </div>

    {{-- Section A --}}
    <div class="section">
        <div class="section-header">A. BASIC APPLICANT INFORMATION</div>
        <div class="section-body">

            {{-- Name --}}
            <div class="row" style="margin-bottom:5px;">
                <div class="cell" style="width:70%;">
                    <p style="font-size:9px; font-weight:bold; margin-bottom:3px;">A.1 Name</p>
                    <div class="row">
                        <div class="cell" style="width:33%; padding-right:5px;">
                            <div class="field-value">{{ $request->last_name }}</div>
                            <div class="field-label">Last Name</div>
                        </div>
                        <div class="cell" style="width:33%; padding-right:5px;">
                            <div class="field-value">{{ $request->first_name }}</div>
                            <div class="field-label">First Name</div>
                        </div>
                        <div class="cell" style="width:22%; padding-right:5px;">
                            <div class="field-value">{{ $request->middle_name }}</div>
                            <div class="field-label">Middle Name</div>
                        </div>
                        <div class="cell" style="width:12%;">
                            <div class="field-value">{{ $request->suffix }}</div>
                            <div class="field-label">Suffix</div>
                        </div>
                    </div>
                </div>
                <div class="cell" style="width:30%; padding-left:10px;">
                    <p style="font-size:9px; font-weight:bold; margin-bottom:3px;">A.2 Contact Number</p>
                    <div class="field-value">{{ $request->contact_number }}</div>
                </div>
            </div>

            {{-- Address --}}
            <div class="row" style="margin-bottom:5px;">
                <div class="cell" style="width:70%;">
                    <p style="font-size:9px; font-weight:bold; margin-bottom:3px;">A.3 Address</p>
                    <div class="row">
                        <div class="cell" style="width:30%; padding-right:5px;">
                            <div class="field-value">{{ $request->street }}</div>
                            <div class="field-label">No. & Street/Sitio</div>
                        </div>
                        <div class="cell" style="width:25%; padding-right:5px;">
                            <div class="field-value">{{ $request->barangay }}</div>
                            <div class="field-label">Barangay</div>
                        </div>
                        <div class="cell" style="width:25%; padding-right:5px;">
                            <div class="field-value">{{ $request->municipality }}</div>
                            <div class="field-label">Municipality/City</div>
                        </div>
                        <div class="cell" style="width:20%;">
                            <div class="field-value">{{ $request->province }}</div>
                            <div class="field-label">Province</div>
                        </div>
                    </div>
                </div>
                <div class="cell" style="width:30%; padding-left:10px;">
                    <p style="font-size:9px; font-weight:bold; margin-bottom:3px;">A.4 Date of Birth</p>
                    <div class="field-value">{{ $request->date_of_birth }}</div>
                    <div class="field-label">(mm/dd/yyyy)</div>
                </div>
            </div>

            {{-- Sex & Special Sector --}}
            <div class="checkbox-area">
                <strong>A.5 Sex:</strong>
                <span class="chk {{ $request->sex_male ? 'checked' : '' }}"></span> Male &nbsp;
                <span class="chk {{ $request->sex_female ? 'checked' : '' }}"></span> Female &nbsp;&nbsp;
                <strong>A.6 Special Sector:</strong>
                <span class="chk {{ $request->is_pwd ? 'checked' : '' }}"></span> PWD &nbsp;
                <span class="chk"></span> Senior Citizen &nbsp;
                <span class="chk"></span> Youth (18-30 y/o) &nbsp;
                <span class="chk {{ $request->is_indigenous ? 'checked' : '' }}"></span> Indigenous People
            </div>

            {{-- Civil Status --}}
            <div class="checkbox-area">
                <strong>A.7 Civil Status:</strong>
                <span class="chk"></span> Single &nbsp;
                <span class="chk"></span> Married &nbsp;
                <span class="chk"></span> Widow/er &nbsp;
                <span class="chk"></span> Separated &nbsp;&nbsp;
                If married, Name of Spouse: <strong>{{ $request->spouse_name }}</strong>
            </div>

            {{-- Occupation --}}
            <div class="checkbox-area">
                <strong>A.8 Occupation/Livelihood:</strong> {{ $request->occupation }}
                &nbsp;&nbsp; Work Address: {{ $request->work_address }}
            </div>

            {{-- Insurance Plans --}}
            <div style="margin-bottom:5px;">
                <p style="font-size:9px; font-weight:bold; margin-bottom:3px;">A.9 Desired Insurance Coverage (in
                    thousand pesos)</p>
                <div style="font-size:9px;">
                    @foreach(['15t', '20t', '25t', '30t', '35t', '40t', '45t', '50t', '55t', '60t', '65t', '70t', '75t', '80t', '85t', '90t', '95t', '100t'] as $plan)
                        <span class="chk {{ $request->input('plan_' . $plan) ? 'checked' : '' }}"></span> Plan {{ strtoupper($plan) }} &nbsp;
                    @endforeach
                </div>
            </div>

            {{-- Beneficiaries --}}
            <div style="margin-bottom:5px;">
                <p style="font-size:9px; font-weight:bold; margin-bottom:3px;">A.10 Name of Legal Beneficiary/ies</p>
                <table class="beneficiary">
                    <tr>
                        <td style="width:15%; font-weight:bold;">Primary:</td>
                        <td style="width:25%;">
                            <div class="field-value">{{ $request->primary_last }}</div>
                            <div class="field-label">Last Name</div>
                        </td>
                        <td style="width:25%;">
                            <div class="field-value">{{ $request->primary_first }}</div>
                            <div class="field-label">First Name</div>
                        </td>
                        <td style="width:20%;">
                            <div class="field-value">{{ $request->primary_middle }}</div>
                            <div class="field-label">Middle Name</div>
                        </td>
                        <td style="width:15%;">Rel: {{ $request->primary_relationship }}<br>BD:
                            {{ $request->primary_birthdate }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">Secondary:</td>
                        <td>
                            <div class="field-value">{{ $request->secondary_last }}</div>
                            <div class="field-label">Last Name</div>
                        </td>
                        <td>
                            <div class="field-value">{{ $request->secondary_first }}</div>
                            <div class="field-label">First Name</div>
                        </td>
                        <td>
                            <div class="field-value">{{ $request->secondary_middle }}</div>
                            <div class="field-label">Middle Name</div>
                        </td>
                        <td>Rel: {{ $request->secondary_relationship }}<br>BD: {{ $request->secondary_birthdate }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight:bold;">Guardian:</td>
                        <td>
                            <div class="field-value">{{ $request->guardian_last }}</div>
                            <div class="field-label">Last Name</div>
                        </td>
                        <td>
                            <div class="field-value">{{ $request->guardian_first }}</div>
                            <div class="field-label">First Name</div>
                        </td>
                        <td>
                            <div class="field-value">{{ $request->guardian_middle }}</div>
                            <div class="field-label">Middle Name</div>
                        </td>
                        <td>Rel: {{ $request->guardian_relationship }}</td>
                    </tr>
                </table>
            </div>

            {{-- Payment Method --}}
            <div class="checkbox-area">
                <strong>A.11 Preferred Payment Method:</strong>
                <span class="chk {{ $request->payment_landbank ? 'checked' : '' }}"></span> LandBank or DBP &nbsp;
                <span class="chk {{ $request->payment_palawan ? 'checked' : '' }}"></span> Palawan Express &nbsp;
                <span class="chk {{ $request->payment_gcash ? 'checked' : '' }}"></span> GCash &nbsp;
                <span class="chk {{ $request->payment_others ? 'checked' : '' }}"></span> Others: {{ $request->payment_others_specify }}
            </div>

            {{-- A.12 --}}
            <div class="checkbox-area">
                <strong>A.12 Family member of insured farmer?</strong>
                <span class="chk {{ $request->is_family_yes ? 'checked' : '' }}"></span> Yes &nbsp;
                <span class="chk {{ $request->is_family_no ? 'checked' : '' }}"></span> No &nbsp;
                Name of Farmer: {{ $request->farmer_name_insured }} &nbsp;
                Relationship: {{ $request->farmer_relationship }}
            </div>
        </div>
    </div>

    {{-- Section B --}}
    <div class="section">
        <div class="section-header">B. CERTIFICATION AND DATA PRIVACY CONSENT STATEMENT</div>
        <div class="section-body">
            <div style="display:table; width:100%;">
                <div style="display:table-cell; width:65%; padding-right:10px; font-size:9px; line-height:1.4;">
                    <p style="margin-bottom:5px;">
                        <span class="chk {{ $request->has('cert_true') ? 'checked' : '' }}"></span>
                        I hereby certify that the foregoing answers and statements are complete, true and correct. If
                        the application is approved, the insurance shall be deemed based upon the statements
                        contained herein. I further agree that PCIC reserves the right to reject and/or void the
                        insurance if found that there is fraud/concealment/misrepresentation on this statement
                        material to the risk.</p>
                    <p>
                        <span class="chk {{ $request->has('cert_privacy') ? 'checked' : '' }}"></span>
                        By submitting this application, I hereby consent to the collection, use, processing, and
                        disclosure of my sensitive personal data in accordance with the Data Privacy Act of 2012.</p>
                </div>
                <div style="display:table-cell; width:35%; text-align:center; vertical-align:bottom;">
                    <div class="signature-line">
                        Signature / Thumb Mark over Printed Name<br>
                        <strong>Applicant</strong><br>
                        Date: {{ $request->applicant_date }}
                    </div>
                </div>
            </div>
            <div style="display:table; width:100%; margin-top:10px; border-top:1px solid #ccc; padding-top:5px;">
                <div style="display:table-cell; width:65%; font-size:9px;">
                    <strong>Parental consent for minor applicants only</strong><br>
                    <span class="chk"></span> By signing, I'm allowing my child to avail the ADSS Insurance.
                </div>
                <div style="display:table-cell; width:35%; text-align:center;">
                    <div class="signature-line">
                        Signature over Printed Name<br>
                        <strong>Parent/Guardian</strong><br>
                        Date: {{ $request->guardian_date }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Section C --}}
    <div class="section">
        <div class="section-header">C. FOR PCIC USE ONLY</div>
        <div class="for-pcic">
            <div class="for-pcic-cell" style="width:20%;">
                <p style="font-size:8px; font-weight:bold;">C.1 COC Number</p>
                <div class="field-value">{{ $request->coc_number }}</div>
            </div>
            <div class="for-pcic-cell" style="width:20%;">
                <p style="font-size:8px; font-weight:bold;">C.2 Date Issued</p>
                <div class="field-value">{{ $request->date_issued }}</div>
                <div class="field-label">(mm/dd/yyyy)</div>
            </div>
            <div class="for-pcic-cell" style="width:20%;">
                <p style="font-size:8px; font-weight:bold;">C.3 Sum Insured</p>
                <div class="field-value">{{ $request->sum_insured }}</div>
                <div class="field-label">(PhP)</div>
            </div>
            <div class="for-pcic-cell" style="width:20%;">
                <p style="font-size:8px; font-weight:bold;">C.4 Premium Amount</p>
                <div class="field-value">{{ $request->premium_amount }}</div>
                <div class="field-label">(PhP)</div>
            </div>
            <div class="for-pcic-cell" style="width:20%;">
                <p style="font-size:8px; font-weight:bold;">C.5 Period of Cover</p>
                <p style="font-size:9px;">From: {{ $request->cover_from }}</p>
                <p style="font-size:9px;">To: {{ $request->cover_to }}</p>
            </div>
        </div>
    </div>

</body>

</html>
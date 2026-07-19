<?php

namespace App\Http\Controllers;

use App\Models\Farmer;
use App\Models\Barangay;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FarmerController extends Controller
{
    public function index(Request $request)
    {
        $query = Farmer::with('barangay');

        // Barangay user can only see their own barangay's farmers
        if (Auth::user()->isBarangayUser()) {
            $barangayId = Auth::user()->barangayAccount?->barangay_id;
            if ($barangayId) {
                $query->where('barangay_id', $barangayId);
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        // Search
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('surname', 'like', '%' . $search . '%');
            });
        }

        // Filter by barangay (admin/staff only)
        if ($request->barangay && !Auth::user()->isBarangayUser()) {
            $query->where('barangay_id', $request->barangay);
        }

        $farmers = $query->latest()->paginate(15);
        return view('farmers.index', compact('farmers'));
    }

    public function create()
    {
        $barangays = Barangay::orderBy('name')->get();
        return view('farmers.create', compact('barangays'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'surname' => 'required|string|max:100',
            'first_name' => 'required|string|max:100',
            'middle_name' => 'nullable|string|max:100',
            'extension_name' => 'nullable|string|max:20',
            'sex' => 'required|in:male,female',
            'date_of_birth' => 'required|date',
            'mobile_number' => 'nullable|string|max:20',
            'barangay_id' => 'nullable|exists:barangays,id',
        ]);

        // Generate reference number
        $refNumber = 'FMR-' . date('Y') . '-' . str_pad(Farmer::count() + 1, 5, '0', STR_PAD_LEFT);

        // If barangay rep, automatically assign their barangay
        $barangayId = $request->barangay_id;
        if (Auth::user()->isBarangayUser()) {
            $barangayId = Auth::user()->barangayAccount?->barangay_id;
        }

        $farmer = Farmer::create(array_merge(
            $request->except('_token'),
            [
                'reference_number' => $refNumber,
                'registered_by' => Auth::id(),
                'barangay_id' => $barangayId,
                'religion' => $request->religion === 'others' ? $request->religion_other : $request->religion,
                'is_household_head' => $request->boolean('is_household_head'),
                'is_pwd' => $request->boolean('is_pwd'),
                'is_4ps_beneficiary' => $request->boolean('is_4ps_beneficiary'),
                'is_indigenous' => $request->boolean('is_indigenous'),
                'has_government_id' => $request->boolean('has_government_id'),
                'is_farmers_association_member' => $request->boolean('is_farmers_association_member'),
                'farming_rice' => $request->boolean('farming_rice'),
                'farming_corn' => $request->boolean('farming_corn'),
                'farming_other_crops' => $request->boolean('farming_other_crops'),
                'farming_livestock' => $request->boolean('farming_livestock'),
                'farming_poultry' => $request->boolean('farming_poultry'),
                'farmwork_land_preparation' => $request->boolean('farmwork_land_preparation'),
                'farmwork_planting' => $request->boolean('farmwork_planting'),
                'farmwork_cultivation' => $request->boolean('farmwork_cultivation'),
                'farmwork_harvesting' => $request->boolean('farmwork_harvesting'),
                'farmwork_others' => $request->boolean('farmwork_others'),
                'fishing_capture' => $request->boolean('fishing_capture'),
                'fishing_aquaculture' => $request->boolean('fishing_aquaculture'),
                'fishing_processing' => $request->boolean('fishing_processing'),
                'fishing_vending' => $request->boolean('fishing_vending'),
                'fishing_gleaning' => $request->boolean('fishing_gleaning'),
                'fishing_others' => $request->boolean('fishing_others'),
                'agri_youth_farming_household' => $request->boolean('agri_youth_farming_household'),
                'agri_youth_formal_course' => $request->boolean('agri_youth_formal_course'),
                'agri_youth_nonformal_course' => $request->boolean('agri_youth_nonformal_course'),
                'agri_youth_participated_program' => $request->boolean('agri_youth_participated_program'),
                'agri_youth_others' => $request->boolean('agri_youth_others'),
            ]
        ));

        $this->saveCoconutProfile($request, $farmer);

        return redirect()->route('farmers.index')
            ->with('success', "Farmer {$farmer->full_name} registered successfully! Reference: {$refNumber}");
    }

    public function show(Farmer $farmer)
    {
        $farmer->load(['barangay', 'registeredBy', 'coconutFarmProfile.coconutTreeRecords', 'coconutFarmProfile.farmIncomeRecords']);
        return view('farmers.show', compact('farmer'));
    }

    public function edit(Farmer $farmer)
    {
        $barangays = Barangay::orderBy('name')->get();
        $farmer->load('coconutFarmProfile.coconutTreeRecords', 'coconutFarmProfile.farmIncomeRecords');
        return view('farmers.edit', compact('farmer', 'barangays'));
    }

    public function update(Request $request, Farmer $farmer)
    {
        $farmer->update(array_merge(
            $request->except('_token', '_method'),
            [
                'religion' => $request->religion === 'others' ? $request->religion_other : $request->religion,
                'is_household_head' => $request->boolean('is_household_head'),
                'is_pwd' => $request->boolean('is_pwd'),
                'is_4ps_beneficiary' => $request->boolean('is_4ps_beneficiary'),
                'is_indigenous' => $request->boolean('is_indigenous'),
                'has_government_id' => $request->boolean('has_government_id'),
                'is_farmers_association_member' => $request->boolean('is_farmers_association_member'),
                'farming_rice' => $request->boolean('farming_rice'),
                'farming_corn' => $request->boolean('farming_corn'),
                'farming_other_crops' => $request->boolean('farming_other_crops'),
                'farming_livestock' => $request->boolean('farming_livestock'),
                'farming_poultry' => $request->boolean('farming_poultry'),
                'farmwork_land_preparation' => $request->boolean('farmwork_land_preparation'),
                'farmwork_planting' => $request->boolean('farmwork_planting'),
                'farmwork_cultivation' => $request->boolean('farmwork_cultivation'),
                'farmwork_harvesting' => $request->boolean('farmwork_harvesting'),
                'farmwork_others' => $request->boolean('farmwork_others'),
                'fishing_capture' => $request->boolean('fishing_capture'),
                'fishing_aquaculture' => $request->boolean('fishing_aquaculture'),
                'fishing_processing' => $request->boolean('fishing_processing'),
                'fishing_vending' => $request->boolean('fishing_vending'),
                'fishing_gleaning' => $request->boolean('fishing_gleaning'),
                'fishing_others' => $request->boolean('fishing_others'),
                'agri_youth_farming_household' => $request->boolean('agri_youth_farming_household'),
                'agri_youth_formal_course' => $request->boolean('agri_youth_formal_course'),
                'agri_youth_nonformal_course' => $request->boolean('agri_youth_nonformal_course'),
                'agri_youth_participated_program' => $request->boolean('agri_youth_participated_program'),
                'agri_youth_others' => $request->boolean('agri_youth_others'),
            ]
        ));

        $this->saveCoconutProfile($request, $farmer);

        return redirect()->route('farmers.show', $farmer)
            ->with('success', 'Farmer record updated successfully!');
    }

    public function destroy(Farmer $farmer)
    {
        $farmer->delete();
        return redirect()->route('farmers.index')
            ->with('success', 'Farmer record deleted successfully!');
    }

    public function print(Farmer $farmer)
    {
        $farmer->load(['barangay', 'registeredBy', 'coconutFarmProfile.coconutTreeRecords', 'coconutFarmProfile.farmIncomeRecords']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('farmers.print', compact('farmer'))
    ->setPaper([0, 0, 595, 893], 'portrait');
        return $pdf->stream('Farmer-' . $farmer->reference_number . '.pdf');
    }

    /**
     * Saves the Page 2 coconut supplement (single-value profile fields) plus
     * its two repeatable-row tables (coconut trees, farm incomes/expenses).
     * Only runs if the form actually submitted coconut-related data — a
     * farmer with no coconut activity simply won't have a profile row.
     */
    protected function saveCoconutProfile(Request $request, Farmer $farmer): void
    {
        if (!$request->filled('coconut_land_holding_status') && !$request->has('tree_variety_code')) {
            return;
        }

        $profile = $farmer->coconutFarmProfile()->updateOrCreate(
            ['farmer_id' => $farmer->id],
            [
                'coconut_land_holding_status' => $request->coconut_land_holding_status,
                'parcel_no' => $request->parcel_no,
                'coconut_farm_location_province' => $request->coconut_farm_location_province,
                'coconut_farm_location_municipality' => $request->coconut_farm_location_municipality,
                'coconut_farm_location_barangay' => $request->coconut_farm_location_barangay,
                'land_area_absolute' => $request->land_area_absolute,
                'land_area_coconut' => $request->land_area_coconut,
                'land_area_intercrop' => $request->land_area_intercrop,
                'land_area_other_crop' => $request->land_area_other_crop,
                'land_area_idle' => $request->land_area_idle,
                'area_classification' => $request->area_classification,
                'organic_certified' => $request->boolean('organic_certified'),
                'gap_certified' => $request->boolean('gap_certified'),
                'processing_dryer' => $request->boolean('processing_dryer'),
                'processing_dryer_specify' => $request->processing_dryer_specify,
                'processing_charcoal_kiln' => $request->boolean('processing_charcoal_kiln'),
                'processing_decort_machine' => $request->boolean('processing_decort_machine'),
                'processing_others' => $request->boolean('processing_others'),
                'processing_others_specify' => $request->processing_others_specify,
                'distance_to_market_km' => $request->distance_to_market_km,
                'coco_harvesting_cycle' => $request->coco_harvesting_cycle,
                'coco_harvesting_cycle_others_specify' => $request->coco_harvesting_cycle_others_specify,
                'husks_utilization_codes' => $request->husks_utilization_codes,
                'shell_utilization_codes' => $request->shell_utilization_codes,
                'water_utilization_codes' => $request->water_utilization_codes,
                'sold_to_whom_codes' => $request->sold_to_whom_codes,
                'sold_where_codes' => $request->sold_where_codes,
                'doc_certificate_land_transfer' => $request->boolean('doc_certificate_land_transfer'),
                'doc_emancipation_patent' => $request->boolean('doc_emancipation_patent'),
                'doc_individual_cloa' => $request->boolean('doc_individual_cloa'),
                'doc_collective_cloa' => $request->boolean('doc_collective_cloa'),
                'doc_co_ownership_cloa' => $request->boolean('doc_co_ownership_cloa'),
                'doc_agricultural_sales_patent' => $request->boolean('doc_agricultural_sales_patent'),
                'doc_homestead_patent' => $request->boolean('doc_homestead_patent'),
                'doc_free_patent' => $request->boolean('doc_free_patent'),
                'doc_extrajudicial_partition' => $request->boolean('doc_extrajudicial_partition'),
                'doc_certificate_of_title' => $request->boolean('doc_certificate_of_title'),
                'doc_ancestral_domain_title' => $request->boolean('doc_ancestral_domain_title'),
                'doc_ancestral_land_title' => $request->boolean('doc_ancestral_land_title'),
                'doc_tax_declaration' => $request->boolean('doc_tax_declaration'),
                'doc_deed_of_sale' => $request->boolean('doc_deed_of_sale'),
                'doc_dar_id' => $request->boolean('doc_dar_id'),
                'owner_or_tenant_lastname' => $request->owner_or_tenant_lastname,
                'owner_or_tenant_firstname' => $request->owner_or_tenant_firstname,
                'owner_or_tenant_middlename' => $request->owner_or_tenant_middlename,
                'owner_or_tenant_extension' => $request->owner_or_tenant_extension,
                'worker_farm_location_province' => $request->worker_farm_location_province,
                'worker_farm_location_municipality' => $request->worker_farm_location_municipality,
                'worker_farm_location_barangay' => $request->worker_farm_location_barangay,
                'kind_of_work_codes' => $request->kind_of_work_codes,
                'monthly_income' => $request->monthly_income,
                'days_working_per_week' => $request->days_working_per_week,
                'days_working_per_month' => $request->days_working_per_month,
                'days_working_per_year' => $request->days_working_per_year,
                'interviewed_by' => $request->interviewed_by,
                'encoded_by' => $request->encoded_by,
                'date_applied' => $request->date_applied,
            ]
        );

        // Repeatable rows — simplest reliable approach for an edit form:
        // wipe and recreate from whatever was submitted this time.
        $profile->coconutTreeRecords()->delete();
        if ($request->has('tree_variety_code')) {
            foreach ($request->tree_variety_code as $i => $variety) {
                if (blank($variety)) {
                    continue;
                }
                $profile->coconutTreeRecords()->create([
                    'variety_code' => $variety,
                    'year_planted' => $request->tree_year_planted[$i] ?? null,
                    'planting_pattern_code' => $request->tree_planting_pattern_code[$i] ?? null,
                    'planting_distance_code' => $request->tree_planting_distance_code[$i] ?? null,
                    'no_of_trees' => $request->tree_no_of_trees[$i] ?? null,
                    'ave_nut_per_tree_year' => $request->tree_ave_nut_per_tree_year[$i] ?? null,
                ]);
            }
        }

        $profile->farmIncomeRecords()->delete();
        if ($request->has('income_type_code')) {
            foreach ($request->income_type_code as $i => $type) {
                if (blank($type)) {
                    continue;
                }
                $profile->farmIncomeRecords()->create([
                    'income_type_code' => $type,
                    'quantity_per_hectare_year' => $request->income_quantity[$i] ?? null,
                    'unit' => $request->income_unit[$i] ?? null,
                    'unit_other_specify' => $request->income_unit_other_specify[$i] ?? null,
                    'unit_price' => $request->income_unit_price[$i] ?? null,
                    'expense_type_code' => $request->expense_type_code[$i] ?? null,
                    'expense_amount' => $request->expense_amount[$i] ?? null,
                ]);
            }
        }
    }

    public function createDb()
    {
        $barangays = Barangay::orderBy('name')->get();
        return view('farmers.create_db', compact('barangays'));
    }




}
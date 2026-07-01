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
            $query->where(function($q) use ($search) {
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
        'surname'       => 'required|string|max:100',
        'first_name'    => 'required|string|max:100',
        'middle_name'   => 'nullable|string|max:100',
        'extension_name'=> 'nullable|string|max:20',
        'sex'           => 'required|in:male,female',
        'date_of_birth' => 'required|date',
        'mobile_number' => 'nullable|string|max:20',
        'barangay_id'   => 'nullable|exists:barangays,id',
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
            'reference_number'              => $refNumber,
            'registered_by'                 => Auth::id(),
            'barangay_id'                   => $barangayId,
            'is_household_head'             => $request->boolean('is_household_head'),
            'is_pwd'                        => $request->boolean('is_pwd'),
            'is_4ps_beneficiary'            => $request->boolean('is_4ps_beneficiary'),
            'is_indigenous'                 => $request->boolean('is_indigenous'),
            'has_government_id'             => $request->boolean('has_government_id'),
            'is_farmers_association_member' => $request->boolean('is_farmers_association_member'),
            'farming_rice'                  => $request->boolean('farming_rice'),
            'farming_corn'                  => $request->boolean('farming_corn'),
            'farming_other_crops'           => $request->boolean('farming_other_crops'),
            'farming_livestock'             => $request->boolean('farming_livestock'),
            'farming_poultry'               => $request->boolean('farming_poultry'),
            'farmwork_land_preparation'     => $request->boolean('farmwork_land_preparation'),
            'farmwork_planting'             => $request->boolean('farmwork_planting'),
            'farmwork_cultivation'          => $request->boolean('farmwork_cultivation'),
            'farmwork_harvesting'           => $request->boolean('farmwork_harvesting'),
            'farmwork_others'               => $request->boolean('farmwork_others'),
        ]
    ));

    return redirect()->route('farmers.index')
        ->with('success', "Farmer {$farmer->full_name} registered successfully! Reference: {$refNumber}");
}

    public function show(Farmer $farmer)
    {
        $farmer->load(['barangay', 'registeredBy']);
        return view('farmers.show', compact('farmer'));
    }

    public function edit(Farmer $farmer)
    {
        $barangays = Barangay::orderBy('name')->get();
        return view('farmers.edit', compact('farmer', 'barangays'));
    }

    public function update(Request $request, Farmer $farmer)
    {
        $farmer->update(array_merge(
            $request->except('_token', '_method'),
            [
                'is_household_head'             => $request->boolean('is_household_head'),
                'is_pwd'                        => $request->boolean('is_pwd'),
                'is_4ps_beneficiary'            => $request->boolean('is_4ps_beneficiary'),
                'is_indigenous'                 => $request->boolean('is_indigenous'),
                'has_government_id'             => $request->boolean('has_government_id'),
                'is_farmers_association_member' => $request->boolean('is_farmers_association_member'),
                'farming_rice'                  => $request->boolean('farming_rice'),
                'farming_corn'                  => $request->boolean('farming_corn'),
                'farming_other_crops'           => $request->boolean('farming_other_crops'),
                'farming_livestock'             => $request->boolean('farming_livestock'),
                'farming_poultry'               => $request->boolean('farming_poultry'),
                'farmwork_land_preparation'     => $request->boolean('farmwork_land_preparation'),
                'farmwork_planting'             => $request->boolean('farmwork_planting'),
                'farmwork_cultivation'          => $request->boolean('farmwork_cultivation'),
                'farmwork_harvesting'           => $request->boolean('farmwork_harvesting'),
                'farmwork_others'               => $request->boolean('farmwork_others'),
            ]
        ));

        return redirect()->route('farmers.show', $farmer)
            ->with('success', 'Farmer record updated successfully!');
    }

    public function destroy(Farmer $farmer)
    {
        $farmer->delete();
        return redirect()->route('farmers.index')
            ->with('success', 'Farmer record deleted successfully!');
    }
}
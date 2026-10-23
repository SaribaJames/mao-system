<?php

namespace App\Http\Controllers;

use App\Models\Farmer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FormController extends Controller
{
    public function index()
    {
        return view('forms.index');
    }

    public function pcicAdss(Request $request)
    {
        $farmers = $this->selectableFarmers()->get();
        $farmer = null;

        if ($request->farmer_id) {
            $farmer = Farmer::with('barangay')->find($request->farmer_id);
        }

        return view('forms.pcic-adss', compact('farmers', 'farmer'));
    }

    /**
     * Barangay representatives fill forms for the farmers they registered, so
     * their picker is limited to their own barangay. MAO staff and admins see
     * everyone.
     */
    protected function selectableFarmers()
    {
        $query = Farmer::orderBy('surname');

        if (Auth::user()->isBarangayUser()) {
            $barangayId = Auth::user()->barangayAccount?->barangay_id;
            $barangayId ? $query->where('barangay_id', $barangayId) : $query->whereRaw('1 = 0');
        }

        return $query;
    }

    public function pcicAdssPDF(Request $request)
    {
        $farmer = null;
        if ($request->farmer_id) {
            $farmer = Farmer::with('barangay')->find($request->farmer_id);
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('forms.pcic-adss-pdf', compact('farmer', 'request'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('PCIC-ADSS-Form.pdf');
    }
}
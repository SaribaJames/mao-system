<?php

namespace App\Http\Controllers;

use App\Models\Farmer;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public function index()
    {
        return view('forms.index');
    }

    public function pcicAdss(Request $request)
    {
        $farmers = Farmer::orderBy('surname')->get();
        $farmer = null;

        if ($request->farmer_id) {
            $farmer = Farmer::with('barangay')->find($request->farmer_id);
        }

        return view('forms.pcic-adss', compact('farmers', 'farmer'));
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
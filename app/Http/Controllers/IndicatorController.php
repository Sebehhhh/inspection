<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Indicator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class IndicatorController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua equipment untuk dropdown filter
        $allEquipments = Equipment::all();

        // Buat query indikator
        $query = Indicator::query();

        // Jika ada parameter equipment_id terenkripsi, lakukan dekripsi dan filter
        if ($request->filled('equipment_id')) {

            $decryptedEquipmentId = Crypt::decrypt($request->input('equipment_id'));
            $query->where('equipment_id', $decryptedEquipmentId);
        }

        // Ambil indikator dengan pagination 10 per halaman
        $indicators = $query->paginate(10);

        return view('c_panel.indicators.index', compact('indicators', 'allEquipments'));
    }


    public function create()
    {
        $equipments = Equipment::all();
        return view('c_panel.indicators.create', compact('equipments'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name'         => 'required|string|max:255',
            'equipment_id' => 'required|integer',
            'unit'         => 'required|string|max:50',
            'baseline'     => 'required|numeric',
        ]);

        Indicator::create($validatedData);
        return redirect()->route('indicator.index')->with('success', 'Indicator created successfully.');
    }

    public function edit($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $indicator = Indicator::findOrFail($id);
        $equipments = Equipment::all();
        return view('c_panel.indicators.edit', compact('indicator', 'equipments'));
    }

    public function update(Request $request, Indicator $indicator)
    {
        $validatedData = $request->validate([
            'name'         => 'required|string|max:255',
            'equipment_id' => 'required|integer',
            'unit'         => 'required|string|max:50',
            'baseline'     => 'required|numeric',
        ]);

        $indicator->update($validatedData);
        return redirect()->route('indicator.index')->with('success', 'Indicator updated successfully.');
    }

    public function destroy(Indicator $indicator)
    {
        $indicator->delete();
        return redirect()->route('indicator.index')->with('success', 'Indicator deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Indicator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class IndicatorController extends Controller
{
    public function index()
    {
        $indicators = Indicator::all();
        return view('c_panel.indicators.index', compact('indicators'));
    }

    public function create()
    {
        $equipments = Equipment::all();
        return view('c_panel.indicators.create', compact('equipments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'equipment_id' => 'required|integer',
            'unit' => 'required|string|max:50',
        ]);

        Indicator::create($request->all());
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
        $request->validate([
            'name' => 'required|string|max:255',
            'equipment_id' => 'required|integer',
            'unit' => 'required|string|max:50',
        ]);

        $indicator->update($request->all());
        return redirect()->route('indicator.index')->with('success', 'Indicator updated successfully.');
    }

    public function destroy(Indicator $indicator)
    {
        $indicator->delete();
        return redirect()->route('indicator.index')->with('success', 'Indicator deleted successfully.');
    }
}

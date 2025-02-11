<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipments = Equipment::all();
        return view('c_panel.equipments.index', compact('equipments'));
    }

    public function create()
    {
        return view('c_panel.equipments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Equipment::create($request->all());

        return redirect()->route('equipment.index');
    }

    public function edit($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $equipment = Equipment::findOrFail($id);
        return view('c_panel.equipments.edit', compact('equipment'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $equipment->update($request->all());

        return redirect()->route('equipment.index');
    }

    public function destroy(Equipment $equipment)
    {
        $equipment->delete();
        return redirect()->route('equipment.index');
    }
}

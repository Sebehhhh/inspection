<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\EquipmentImport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EquipmentController extends Controller
{
    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        try {
            Excel::import(new class implements ToModel, WithHeadingRow {
                public function model(array $row)
                {
                    return new Equipment([
                        'id'          => $row['id'],
                        'name'        => $row['name'],
                        'description' => $row['description'],
                        'created_at'  => now(),
                        'updated_at'  => now(),
                    ]);
                }
            }, $request->file('file'));

            return redirect()->route('equipment.index')->with('success', 'Equipment data imported successfully.');
        } catch (\Exception $e) {
            return redirect()->route('equipment.index')->with('error', 'Failed to import data. Please check for duplicates or errors.');
        }
    }

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
        $validatedData = $request->validate([
            'name'        => 'required|string|max:255|unique:equipments,name',
            'description' => 'nullable|string',
        ]);

        try {
            $maxId = Equipment::max('id') ?? 0;
            $validatedData['id'] = $maxId + 1;
            Equipment::create($validatedData);

            return redirect()->route('equipment.index')->with('success', 'Equipment created successfully.');
        } catch (\Exception $e) {
            return redirect()->route('equipment.index')->with('error', 'Failed to create equipment. Please try again.');
        }
    }

    public function edit($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $equipment = Equipment::findOrFail($id);
        return view('c_panel.equipments.edit', compact('equipment'));
    }

    public function update(Request $request, Equipment $equipment)
    {
        $validatedData = $request->validate([
            'name'        => 'required|string|max:255|unique:equipments,name,' . $equipment->id,
            'description' => 'nullable|string',
        ]);

        try {
            $equipment->update($validatedData);
            return redirect()->route('equipment.index')->with('success', 'Equipment updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('equipment.index')->with('error', 'Failed to update equipment. Please try again.');
        }
    }

    public function destroy(Equipment $equipment)
    {
        try {
            $equipment->delete();
            return redirect()->route('equipment.index')->with('success', 'Equipment deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('equipment.index')->with('error', 'Failed to delete equipment. Please try again.');
        }
    }
}

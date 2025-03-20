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
    
        Excel::import(new class implements ToModel, WithHeadingRow {
            public function model(array $row)
            {
                return new Equipment([
                    'id'          => $row['id'], // Menggunakan ID dari file Excel
                    'name'        => $row['name'],
                    'description' => $row['description'],
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ]);
            }
        }, $request->file('file'));
    
        return redirect()->route('equipment.index')->with('success', 'Equipment data imported successfully.');
    }

    public function index()
    {
        // Menggunakan paginate(10) untuk menampilkan 10 data per halaman
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
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Equipment::create($validatedData);
        return redirect()->route('equipment.index')->with('success', 'Equipment created successfully.');
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
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $equipment->update($validatedData);
        return redirect()->route('equipment.index')->with('success', 'Equipment updated successfully.');
    }

    public function destroy(Equipment $equipment)
    {
        $equipment->delete();
        return redirect()->route('equipment.index')->with('success', 'Equipment deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Indicator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\IndicatorImport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class IndicatorController extends Controller
{
    public function importExcel(Request $request)
    {
        try {
            $request->validate([
                'file' => 'required|mimes:xlsx,csv',
            ]);

            Excel::import(new class implements ToModel, WithHeadingRow {
                public function model(array $row)
                {
                    try {
                        if (!isset($row['equipment_id'], $row['name'], $row['unit'], $row['baseline'])) {
                            throw new \Exception("Format file Excel tidak sesuai. Pastikan kolom: 'equipment_id', 'name', 'unit', dan 'baseline' tersedia.");
                        }

                        return new Indicator([
                            'equipment_id' => $row['equipment_id'],
                            'name'         => $row['name'],
                            'unit'         => $row['unit'],
                            'baseline'     => $row['baseline'],
                            'created_at'   => now(),
                            'updated_at'   => now(),
                        ]);
                    } catch (\Exception $e) {
                        session()->flash('error', $e->getMessage());
                        return null;
                    }
                }
            }, $request->file('file'));

            return redirect()->route('indicator.index');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessage = "Gagal mengimpor data:\n";
            foreach ($failures as $failure) {
                $errorMessage .= "Baris " . $failure->row() . ": " . implode(', ', $failure->errors()) . "\n";
            }
            return redirect()->route('indicator.index')->with('error', $errorMessage);
        } catch (\Exception $e) {
            return redirect()->route('indicator.index')->with('error', 'Terjadi kesalahan saat mengimpor data. Pastikan format file Excel sesuai.');
        }
    }

    public function index(Request $request)
    {
        try {
            $allEquipments = Equipment::all();
            $query = Indicator::query();

            if ($request->filled('equipment_id')) {
                $decryptedEquipmentId = Crypt::decrypt($request->input('equipment_id'));
                $query->where('equipment_id', $decryptedEquipmentId);
            }

            $indicators = $query->orderBy('equipment_id', 'asc')->paginate(10);

            return view('c_panel.indicators.index', compact('indicators', 'allEquipments'));
        } catch (\Exception $e) {
            return redirect()->route('indicator.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }


    public function create()
    {
        $equipments = Equipment::all();
        return view('c_panel.indicators.create', compact('equipments'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name'         => 'required|string|max:255',
                'equipment_id' => 'required|integer',
                'unit'         => 'required|string|max:50',
                'baseline'     => 'required|numeric',
            ]);

            Indicator::create($validatedData);
            return redirect()->route('indicator.index')->with('success', 'Indicator created successfully.');
        } catch (\Exception $e) {
            abort(500, 'Internal Server Error');
        }
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
        try {
            $validatedData = $request->validate([
                'name'         => 'required|string|max:255',
                'equipment_id' => 'required|integer',
                'unit'         => 'required|string|max:50',
                'baseline'     => 'required|numeric',
            ]);

            $indicator->update($validatedData);
            return redirect()->route('indicator.index')->with('success', 'Indicator updated successfully.');
        } catch (\Exception $e) {
            abort(500, 'Internal Server Error');
        }
    }

    public function destroy(Indicator $indicator)
    {
        try {
            $indicator->delete();
            return redirect()->route('indicator.index')->with('success', 'Indicator deleted successfully.');
        } catch (\Exception $e) {
            abort(500, 'Internal Server Error');
        }
    }
}

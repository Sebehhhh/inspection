<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Indicator;
use App\Models\Inspection;
use App\Models\Problem;
use Illuminate\Http\Request;

class InspectionController extends Controller
{
    public function index(Request $request)
    {
        $equipments = Equipment::all();
        $equipmentId = $request->query('equipment_id');

        $indicators = collect();
        $problems = collect();
        $inspections = collect();

        if ($equipmentId) {
            $indicators = Indicator::where('equipment_id', $equipmentId)->get();
            $problems = Problem::where('equipment_id', $equipmentId)->get();
            $inspections = Inspection::with(['equipment', 'indicator', 'problem'])
                ->where('equipment_id', $equipmentId)
                ->get();
        }

        return view('c_panel.inspections.index', compact('equipments', 'indicators', 'problems', 'inspections'));
    }

    // In your InspectionController

    public function editMatrix(Request $request)
    {
        $equipmentId = $request->query('equipment_id');

        if (!$equipmentId) {
            return redirect()->route('inspection.index')->with('error', 'Equipment must be selected.');
        }

        $equipments = Equipment::all();
        $indicators = Indicator::where('equipment_id', $equipmentId)->get();
        $problems   = Problem::where('equipment_id', $equipmentId)->get();
        $inspections = Inspection::where('equipment_id', $equipmentId)->get();

        return view('c_panel.inspections.edit', compact('equipments', 'indicators', 'problems', 'inspections'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'equipment_id' => 'required|integer',
            'inspections'  => 'nullable|array',
        ]);

        $equipmentId = $request->equipment_id;
        $inspectionsInput = $request->input('inspections', []);

        // Loop through each problem (row) in the matrix
        foreach ($inspectionsInput as $problemId => $indicatorValues) {
            // Loop through each indicator (column) for the given problem
            foreach ($indicatorValues as $indicatorId => $realValue) {
                // Check if an inspection record exists for this combination
                $inspection = \App\Models\Inspection::where('equipment_id', $equipmentId)
                    ->where('problem_id', $problemId)
                    ->where('indicator_id', $indicatorId)
                    ->first();

                if ($inspection) {
                    // Update the existing record with the new real value
                    $inspection->update(['real' => $realValue]);
                } else {
                    // Create a new record if one does not exist
                    Inspection::create([
                        'equipment_id' => $equipmentId,
                        'problem_id'   => $problemId,
                        'indicator_id' => $indicatorId,
                        'real'         => $realValue,
                    ]);
                }
            }
        }

        return redirect()->route('inspection.index')->with('success', 'Inspection Matrix updated successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Problem;
use Illuminate\Http\Request;

class SolutionController extends Controller
{
    public function index(Request $request)
    {
        $equipments = Equipment::all();
        $equipmentId = $request->query('equipment_id');
        $problems = collect();

        if ($equipmentId) {
            // Retrieve problems along with their associated solution (if exists)
            $problems = Problem::with('solution')->where('equipment_id', $equipmentId)->get();
        }

        return view('c_panel.solutions.index', compact('equipments', 'problems'));
    }

    public function editMatrix(Request $request)
    {
        $equipmentId = $request->query('equipment_id');

        if (!$equipmentId) {
            return redirect()->route('solution.index')->with('error', 'Equipment must be selected.');
        }

        $equipments = Equipment::all();
        $problems = Problem::with('solution')->where('equipment_id', $equipmentId)->get();

        return view('c_panel.solutions.edit', compact('equipments', 'problems'));
    }


    public function update(Request $request)
    {
        $request->validate([
            'equipment_id' => 'required|integer',
            'solutions'    => 'nullable|array',
        ]);

        $equipmentId = $request->equipment_id;
        $solutionsInput = $request->input('solutions', []);

        // Loop through each problem's solution data
        foreach ($solutionsInput as $problemId => $solutionData) {
            // Retrieve the problem that belongs to the selected equipment
            $problem = Problem::where('id', $problemId)
                ->where('equipment_id', $equipmentId)
                ->first();

            if ($problem) {
                // Check if a solution exists for the problem, update or create accordingly
                if ($problem->solution) {
                    $problem->solution->update($solutionData);
                } else {
                    $problem->solution()->create($solutionData);
                }
            }
        }

        return redirect()->route('solution.index', ['equipment_id' => $equipmentId])
            ->with('success', 'Solutions updated successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Problem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use App\Imports\ProblemImport;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Facades\Excel;

class ProblemController extends Controller
{
    public function index(Request $request)
    {
        try {
            $allEquipments = Equipment::all();
            $query = Problem::query();

            if ($request->filled('equipment_id')) {
                $decryptedEquipmentId = Crypt::decrypt($request->input('equipment_id'));
                $query->where('equipment_id', $decryptedEquipmentId);
            }

            $problems = $query->orderBy('equipment_id', 'asc')->paginate(10);

            return view('c_panel.problems.index', compact('problems', 'allEquipments'));
        } catch (\Exception $e) {
            return redirect()->route('problem.index')->with('error', 'Terjadi kesalahan saat mengambil data.');
        }
    }

    public function create()
    {
        $equipments = Equipment::all();
        $parentProblems = Problem::whereNull('parent_problem_id')->get();
        return view('c_panel.problems.create', compact('equipments', 'parentProblems'));
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name'                => 'required|string|max:255',
                'equipment_id'        => 'required|integer',
                'parent_problem_id'   => 'nullable|integer',
                'further_testing'     => 'nullable|string',
                'corrective_action'   => 'nullable|string',
            ]);

            if (!empty($validatedData['parent_problem_id'])) {
                $parentProblem = Problem::find($validatedData['parent_problem_id']);
                if ($parentProblem) {
                    $validatedData['further_testing'] = $parentProblem->further_testing;
                    $validatedData['corrective_action'] = $parentProblem->corrective_action;
                }
            }

            Problem::create($validatedData);
            return redirect()->route('problem.index')->with('success', 'Problem created successfully.');
        } catch (\Exception $e) {
            return redirect()->route('problem.index')->with('error', 'Terjadi kesalahan saat menyimpan data.');
        }
    }

    public function edit($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $problem = Problem::findOrFail($id);
        $equipments = Equipment::all();
        $parentProblems = Problem::where('id', '!=', $id)->get();
        return view('c_panel.problems.edit', compact('problem', 'equipments', 'parentProblems'));
    }

    public function update(Request $request, Problem $problem)
    {
        try {
            $validatedData = $request->validate([
                'name'                => 'required|string|max:255',
                'equipment_id'        => 'required|integer',
                'parent_problem_id'   => 'nullable|integer',
                'further_testing'     => 'nullable|string',
                'corrective_action'   => 'nullable|string',
            ]);

            $problem->update($validatedData);
            return redirect()->route('problem.index')->with('success', 'Problem updated successfully.');
        } catch (\Exception $e) {
            return redirect()->route('problem.index')->with('error', 'Terjadi kesalahan saat memperbarui data.');
        }
    }

    public function destroy(Problem $problem)
    {
        try {
            $problem->delete();
            return redirect()->route('problem.index')->with('success', 'Problem deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('problem.index')->with('error', 'Terjadi kesalahan saat menghapus data.');
        }
    }

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
                        if (!isset($row['equipment_id'], $row['name'], $row['further_testing'], $row['corrective_action'])) {
                            throw new \Exception("Format file Excel tidak sesuai. Pastikan kolom: 'equipment_id', 'name', 'further_testing', dan 'corrective_action' tersedia.");
                        }

                        return new Problem([
                            'equipment_id'       => $row['equipment_id'],
                            'parent_problem_id'  => $row['parent_problem_id'],
                            'name'               => $row['name'],
                            'further_testing'    => $row['further_testing'],
                            'corrective_action'  => $row['corrective_action'],
                            'action_taken'       => $row['action_taken'],
                            'possible_cause'     => $row['possible_cause'],
                            'created_at'         => now(),
                            'updated_at'         => now(),
                        ]);
                    } catch (\Exception $e) {
                        session()->flash('error', $e->getMessage());
                        return null;
                    }
                }
            }, $request->file('file'));

            return redirect()->route('problem.index');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessage = "Gagal mengimpor data:\n";
            foreach ($failures as $failure) {
                $errorMessage .= "Baris " . $failure->row() . ": " . implode(', ', $failure->errors()) . "\n";
            }
            return redirect()->route('problem.index')->with('error', $errorMessage);
        } catch (\Exception $e) {
            return redirect()->route('problem.index')->with('error', 'Terjadi kesalahan saat mengimpor data. Pastikan format file Excel sesuai.');
        }
    }
}

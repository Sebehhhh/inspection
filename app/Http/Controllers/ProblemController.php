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
        // Ambil semua equipment untuk dropdown filter
        $allEquipments = Equipment::all();

        // Buat query untuk Problem
        $query = Problem::query();

        // Jika ada parameter equipment_id terenkripsi, lakukan dekripsi dan filter berdasarkan equipment tersebut
        if ($request->filled('equipment_id')) {
            $decryptedEquipmentId = Crypt::decrypt($request->input('equipment_id'));
            $query->where('equipment_id', $decryptedEquipmentId);
        }

        // Menggunakan paginate(10) agar data ditampilkan 10 per halaman
        $problems = $query->paginate(10);

        return view('c_panel.problems.index', compact('problems', 'allEquipments'));
    }

    public function create()
    {
        $equipments = Equipment::all();
        // Ambil parent problem yang hanya merupakan masalah utama
        $parentProblems = Problem::whereNull('parent_problem_id')->get();
        return view('c_panel.problems.create', compact('equipments', 'parentProblems'));
    }

    public function store(Request $request)
    {
        // Validasi data dan simpan hasil validasi ke variabel
        $validatedData = $request->validate([
            'name'                => 'required|string|max:255',
            'equipment_id'        => 'required|integer',
            'parent_problem_id'   => 'nullable|integer',
            'further_testing'     => 'nullable|string',
            'corrective_action'   => 'nullable|string',
        ]);

        // Jika problem memiliki parent, ambil nilai further_testing dan corrective_action dari parent
        if (!empty($validatedData['parent_problem_id'])) {
            $parentProblem = Problem::find($validatedData['parent_problem_id']);
            if ($parentProblem) {
                $validatedData['further_testing'] = $parentProblem->further_testing;
                $validatedData['corrective_action'] = $parentProblem->corrective_action;
            }
        }

        Problem::create($validatedData);
        return redirect()->route('problem.index')->with('success', 'Problem created successfully.');
    }

    public function edit($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $problem = Problem::findOrFail($id);
        $equipments = Equipment::all();
        // Mengambil parent problems kecuali masalah yang sedang diedit agar tidak terjadi rekursif
        $parentProblems = Problem::where('id', '!=', $id)->get();
        return view('c_panel.problems.edit', compact('problem', 'equipments', 'parentProblems'));
    }

    public function update(Request $request, Problem $problem)
    {
        $validatedData = $request->validate([
            'name'                => 'required|string|max:255',
            'equipment_id'        => 'required|integer',
            'parent_problem_id'   => 'nullable|integer',
            'further_testing'     => 'nullable|string',
            'corrective_action'   => 'nullable|string',
        ]);

        $problem->update($validatedData);
        return redirect()->route('problem.index')->with('success', 'Problem updated successfully.');
    }

    public function destroy(Problem $problem)
    {
        $problem->delete();
        return redirect()->route('problem.index')->with('success', 'Problem deleted successfully.');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv',
        ]);

        Excel::import(new class implements ToModel, WithHeadingRow {
            public function model(array $row)
            {
                return new Problem([
                    'id'                 => $row['id'], // Menggunakan ID dari file Excel
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
            }
        }, $request->file('file'));

        return redirect()->route('problem.index')->with('success', 'Problem data imported successfully.');
    }
}

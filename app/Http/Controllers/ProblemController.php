<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Problem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class ProblemController extends Controller
{
    public function index()
    {
        $problems = Problem::all();
        return view('c_panel.problems.index', compact('problems'));
    }

    public function create()
    {
        $equipments = Equipment::all();
        $parentProblems = Problem::whereNull('parent_problem_id')->get();
        return view('c_panel.problems.create', compact('equipments', 'parentProblems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'equipment_id' => 'required|integer',
            'parent_problem_id' => 'nullable|integer',
        ]);

        Problem::create($request->all());
        return redirect()->route('problem.index')->with('success', 'Problem created successfully.');
    }

    public function edit($encryptedId)
    {
        $id = Crypt::decrypt($encryptedId);
        $problem = Problem::findOrFail($id);
        $equipments = Equipment::all();
        // Jika diperlukan, ambil juga masalah utama (parent problems) untuk opsi sub-kategori
        $parentProblems = Problem::where('id', '!=', $id)->get();
        return view('c_panel.problems.edit', compact('problem', 'equipments', 'parentProblems'));
    }

    public function update(Request $request, Problem $problem)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'equipment_id' => 'required|integer',
            'parent_problem_id' => 'nullable|integer',
        ]);

        $problem->update($request->all());
        return redirect()->route('problem.index')->with('success', 'Problem updated successfully.');
    }

    public function destroy(Problem $problem)
    {
        $problem->delete();
        return redirect()->route('problem.index')->with('success', 'Problem deleted successfully.');
    }
}

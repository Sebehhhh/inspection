<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inspection;
use App\Models\Problem;

class ResultController extends Controller
{
    public function index(Request $request)
    {
        $equipmentId = $request->query('equipment_id');
        $equipments = \App\Models\Equipment::all();
        $problems = collect();

        if ($equipmentId) {
            // Ambil semua inspeksi beserta relasi indicator dan problem untuk equipment tertentu
            $inspections = Inspection::with(['indicator', 'problem'])
                ->where('equipment_id', $equipmentId)
                ->get();

            // Filter inspeksi yang tercentang: nilai real berbeda dengan baseline indikator
            $checkedInspections = $inspections->filter(function ($inspection) {
                return $inspection->real != $inspection->indicator->baseline;
            });
            // dd($checkedInspections);

            // Ambil daftar unik problem_id dari inspeksi tercentang
            $checkedProblemIds = $checkedInspections->pluck('problem_id')->unique();

            // Ambil data problem beserta relasi solution (jika ada) yang problem_id-nya tercentang
            $problems = \App\Models\Problem::with('solution')
                ->whereIn('id', $checkedProblemIds)
                ->get();
        }

        return view('c_panel.results.index', compact('equipments', 'problems'));
    }
}

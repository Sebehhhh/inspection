<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Indicator;
use App\Models\Problem;
use App\Models\Rule;
use Illuminate\Http\Request;

class RuleController extends Controller
{
    
    public function index(Request $request)
    {
        // Ambil semua equipment untuk dropdown
        $allEquipments = Equipment::all();

        // Ambil equipment_id dari request, atau gunakan equipment pertama sebagai default
        $equipmentId = $request->get('equipment_id');
        if (!$equipmentId) {
            $equipment = $allEquipments->first();
            if (!$equipment) {
                return redirect()->back()->with('error', 'No equipment found.');
            }
        } else {
            $equipment = Equipment::findOrFail($equipmentId);
        }

        // Ambil data indikator dan masalah (problem) berdasarkan equipment yang dipilih
        $indicators = Indicator::where('equipment_id', $equipment->id)->get();
        $problems   = Problem::where('equipment_id', $equipment->id)->get();

        // Ambil rules yang berelasi dengan indikator dan problem tersebut
        $rules = Rule::whereIn('indicator_id', $indicators->pluck('id'))
            ->whereIn('problem_id', $problems->pluck('id'))
            ->get();

        return view('c_panel.rules.index', compact('equipment', 'indicators', 'problems', 'rules', 'allEquipments'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'equipment_id' => 'required|integer|exists:equipments,id',
        ]);

        $equipmentId = $request->input('equipment_id');

        // Ambil daftar indikator dan problem untuk equipment yang dipilih
        $indicatorIds = Indicator::where('equipment_id', $equipmentId)->pluck('id')->toArray();
        $problemIds   = Problem::where('equipment_id', $equipmentId)->pluck('id')->toArray();

        // Data rules yang dikirimkan dari form (format: rules[problem_id][indicator_id] => 'on')
        $submittedRules = $request->input('rules', []);

        // Buat array kunci unik untuk rules yang disubmit, misal "problemId-indicatorId"
        $submittedKeys = [];
        foreach ($submittedRules as $problemId => $indicators) {
            foreach ($indicators as $indicatorId => $value) {
                $submittedKeys[] = $problemId . '-' . $indicatorId;
                // Gunakan updateOrCreate untuk membuat rule jika belum ada (jika sudah ada, record tidak berubah)
                Rule::updateOrCreate(
                    [
                        'problem_id'   => $problemId,
                        'indicator_id' => $indicatorId,
                    ],
                    [] // Tidak ada field lain yang perlu diupdate
                );
            }
        }

        // Ambil semua rule yang ada untuk equipment ini
        $existingRules = Rule::whereIn('indicator_id', $indicatorIds)
            ->whereIn('problem_id', $problemIds)
            ->get();

        // Hapus rule yang sudah ada namun tidak disubmit
        foreach ($existingRules as $rule) {
            $key = $rule->problem_id . '-' . $rule->indicator_id;
            if (!in_array($key, $submittedKeys)) {
                $rule->delete();
            }
        }

        return redirect()->route('rules.index', ['equipment_id' => $equipmentId])
            ->with('success', 'Rules updated successfully.');
    }
}

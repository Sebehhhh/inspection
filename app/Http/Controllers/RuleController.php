<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Indicator;
use App\Models\Problem;
use App\Models\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class RuleController extends Controller
{

    public function index(Request $request)
    {
        // Ambil semua equipment untuk dropdown
        $allEquipments = Equipment::all();

        // Ambil equipment_id terenkripsi dari request, atau gunakan equipment pertama sebagai default
        if ($request->filled('equipment_id')) {

            $decryptedEquipmentId = Crypt::decrypt($request->input('equipment_id'));
            $equipment = Equipment::findOrFail($decryptedEquipmentId);
        } else {
            $equipment = $allEquipments->first();
            if (!$equipment) {
                return redirect()->back()->with('error', 'No equipment found.');
            }
        }

        // Ambil data indikator dan masalah (problem) berdasarkan equipment yang dipilih
        $indicators = Indicator::where('equipment_id', $equipment->id)->get();
        $problems   = Problem::where('equipment_id', $equipment->id)->get();
        // dd($problems);
        // Ambil rules yang berelasi dengan indikator dan problem tersebut
        $rules = Rule::whereIn('indicator_id', $indicators->pluck('id'))
            ->whereIn('problem_id', $problems->pluck('id'))
            ->get();

        return view('c_panel.rules.index', compact('equipment', 'indicators', 'problems', 'rules', 'allEquipments'));
    }



    public function store(Request $request)
    {
        // Dekripsi equipment_id terlebih dahulu dan masukkan kembali ke request
        $encryptedEquipmentId = $request->input('equipment_id');
        try {
            $decryptedEquipmentId = Crypt::decrypt($encryptedEquipmentId);
            // Merge nilai yang sudah didekripsi ke dalam request sehingga validasi bisa menganggapnya sebagai integer
            $request->merge(['equipment_id' => $decryptedEquipmentId]);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Invalid equipment id.');
        }

        // Validasi setelah equipment_id sudah didekripsi
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

        // Kembalikan kembali equipment_id dalam bentuk terenkripsi
        $encryptedReturnEquipmentId = Crypt::encrypt($equipmentId);

        return redirect()->route('rules.index', ['equipment_id' => $encryptedReturnEquipmentId])
            ->with('success', 'Rules updated successfully.');
    }
}

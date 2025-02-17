<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\History;
use App\Models\Indicator;
use App\Models\Inspection; // Pastikan model ini sudah ada
use App\Models\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class InspectController extends Controller
{
    public function index(Request $request)
    {
        // Ambil semua equipment untuk dropdown filter
        $allEquipments = Equipment::all();

        // Mulai query History dengan relasi equipment dan indicator
        $query = History::with('equipment', 'indicator');

        // Jika ada parameter equipment_id terenkripsi, dekripsi dan filter berdasarkan equipment tersebut
        if ($request->filled('equipment_id')) {

            $decryptedEquipmentId = Crypt::decrypt($request->input('equipment_id'));
            $query->where('equipment_id', $decryptedEquipmentId);
        }

        // Ambil histories dengan pagination 10 per halaman
        $histories = $query->paginate(10);

        // Ambil daftar unik indicator_id dari history pada halaman ini
        $indicatorIds = $histories->pluck('indicator_id')->unique();

        // Ambil rules untuk indikator-indikator tersebut, beserta relasi problem, lalu group by indicator_id
        $rules = Rule::with('problem')
            ->whereIn('indicator_id', $indicatorIds)
            ->get()
            ->groupBy('indicator_id');

        return view('c_panel.inspects.index', compact('histories', 'rules', 'allEquipments'));
    }


    /**
     * Tampilkan form inspeksi.
     */
    public function create(Request $request)
    {
        // Jika parameter equipment_id terenkripsi ada, coba dekripsi, jika gagal gunakan equipment pertama
        if ($request->filled('equipment_id')) {

            $decryptedEquipmentId = Crypt::decrypt($request->input('equipment_id'));
            $equipment = Equipment::findOrFail($decryptedEquipmentId);
        } else {
            $equipment = Equipment::first();
        }

        if (!$equipment) {
            return redirect()->back()->with('error', 'No equipment found.');
        }

        // Ambil semua equipment untuk dropdown filter
        $allEquipments = Equipment::all();

        // Ambil indikator yang dimiliki oleh equipment terpilih
        $indicators = Indicator::where('equipment_id', $equipment->id)->get();

        return view('c_panel.inspects.create', compact('equipment', 'allEquipments', 'indicators'));
    }

    /**
     * Simpan data inspeksi.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_id'  => 'required|string', // terenkripsi
            'actual_values' => 'required|array',
        ]);

        // Dekripsi equipment_id
        $decryptedEquipmentId = Crypt::decrypt($validated['equipment_id']);

        // Pastikan equipment ada
        $equipment = Equipment::findOrFail($decryptedEquipmentId);

        // Dapatkan daftar indicator_id yang dikirim dalam submission
        $submittedIndicatorIds = array_keys($validated['actual_values']);

        // Hapus record inspeksi lama untuk equipment ini yang tidak termasuk dalam submission baru
        History::where('equipment_id', $equipment->id)
            ->whereNotIn('indicator_id', $submittedIndicatorIds)
            ->delete();

        // Loop setiap indicator dan update atau create record inspeksi
        foreach ($validated['actual_values'] as $indicatorId => $actualValue) {
            if (!empty($actualValue)) {
                // Ambil data indikator untuk mendapatkan baseline
                $indicator = Indicator::find($indicatorId);
                if ($indicator) {
                    // Bandingkan actual dengan baseline, status disimpan sebagai boolean:
                    // true jika actual sama dengan baseline (normal), false jika tidak (terdapat masalah)
                    $status = ($actualValue == $indicator->baseline);

                    // Update record jika sudah ada, atau buat baru jika belum ada
                    History::updateOrCreate(
                        [
                            'equipment_id' => $equipment->id,
                            'indicator_id' => $indicatorId,
                        ],
                        [
                            'actual_value' => $actualValue,
                            'status'       => $status,
                        ]
                    );
                }
            } else {
                // Jika nilai actual kosong, kita bisa memilih untuk menghapus record yang sudah ada
                History::where('equipment_id', $equipment->id)
                    ->where('indicator_id', $indicatorId)
                    ->delete();
            }
        }

        return redirect()->route('inspect.index')->with('success', 'Inspection submitted successfully.');
    }
}

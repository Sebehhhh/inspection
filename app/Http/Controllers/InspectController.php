<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\History;
use App\Models\Indicator;
use App\Models\Inspection;
use App\Models\Rule;
use App\Models\Problem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InspectionExport;

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

        // Jika ada parameter inspection_date, filter berdasarkan tanggal inspeksi
        if (!$request->filled('inspection_date')) {
            $query->whereDate('created_at', now());
        } else {
            $query->whereDate('created_at', $request->input('inspection_date'));
        }

        // Ambil histories tanpa pagination
        $histories = $query->get();

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
            'inspection_date' => 'nullable|date', // Tambahkan validasi tanggal inspeksi
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
                // Ambil data indikator untuk mendapatkan baseline dan unit
                $indicator = Indicator::find($indicatorId);
                if ($indicator) {
                    // Tentukan status berdasarkan unit dan baseline
                    if ($indicator->unit === 'Low') {
                        $status = ($actualValue < $indicator->baseline) ? 'problem detected' : 'normal';
                    } else { // Jika unit adalah "High"
                        $status = ($actualValue > $indicator->baseline) ? 'problem detected' : 'normal';
                    }

                    // Tambahkan record baru dengan timestamp inspeksi
                    History::create([
                        'equipment_id'    => $equipment->id,
                        'indicator_id'    => $indicatorId,
                        'actual_value'    => $actualValue,
                        'status'          => $status,
                        'inspection_date' => now(), // Tambahkan tanggal inspeksi
                    ]);
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

    public function update(Request $request, $id)
    {
        // Validasi input dari form
        $validated = $request->validate([
            'problem_id'    => 'required|integer',
            'action_taken'  => 'nullable|string',
            'possible_cause' => 'required|array',
        ]);

        // Cari data problem berdasarkan id
        $problem = Problem::findOrFail($id);

        // Update kolom action_taken dan possible_cause
        $problem->action_taken = $request->input('action_taken');
        $possibleCauseArray = $request->input('possible_cause');
        $problem->possible_cause = isset($possibleCauseArray[$id]) ? $possibleCauseArray[$id] : null;

        $problem->save();

        return redirect()->route('inspect.index')->with('success', 'Data inspeksi berhasil diperbarui.');
    }

    public function printHistory(Request $request)
    {
        $query = History::with('equipment', 'indicator');

        if ($request->filled('equipment_id')) {
            $decryptedEquipmentId = Crypt::decrypt($request->input('equipment_id'));
            $query->where('equipment_id', $decryptedEquipmentId);
        }

        if (!$request->filled('inspection_date')) {
            $query->whereDate('created_at', now()->toDateString());
        } else {
            $query->whereDate('created_at', $request->input('inspection_date'));
        }

        // Ambil semua data matching tanpa pagination agar PDF mencakup seluruh data
        $histories = $query->get();

        $indicatorIds = $histories->pluck('indicator_id')->unique();
        $rules = Rule::with('problem')
            ->whereIn('indicator_id', $indicatorIds)
            ->get()
            ->groupBy('indicator_id');

        $data = ['histories' => $histories, 'rules' => $rules];
        $pdf = PDF::loadView('c_panel.inspects.pdf', $data);
        return $pdf->download('inspection-history.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new InspectionExport($request), 'inspection-history.xlsx');
    }

    public function deleteAll(Request $request)
    {
        $query = History::query();

        if ($request->filled('equipment_id')) {
            $decryptedEquipmentId = Crypt::decrypt($request->equipment_id);
            $query->where('equipment_id', $decryptedEquipmentId);
        }

        if ($request->filled('inspection_date')) {
            $query->whereDate('created_at', $request->inspection_date);
        }

        $query->delete();

        return redirect()->route('inspect.index')->with('success', 'Selected inspections deleted successfully.');
    }
}

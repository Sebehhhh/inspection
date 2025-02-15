<?php

namespace App\Http\Controllers;

use App\Models\Equipment;
use App\Models\Indicator;
use App\Models\Inspection;
use App\Models\Problem;
use Illuminate\Http\Request;

class InspectionController extends Controller
{
    public function index()
    {
        $inspections = Inspection::with(['equipment', 'indicator', 'problem'])->get();
        return view('c_panel.inspections.index', compact('inspections'));
    }

    public function create(Request $request)
    {
        $equipments = Equipment::all();
        $equipmentId = $request->query('equipment_id');

        $indicators = collect();
        $problems = collect();

        // Jika equipment dipilih, filter indikator dan problem berdasarkan equipment tersebut
        if ($equipmentId) {
            $indicators = Indicator::where('equipment_id', $equipmentId)->get();
            $problems = Problem::where('equipment_id', $equipmentId)->get();
        }

        return view('c_panel.inspections.create', compact('equipments', 'indicators', 'problems'));
    }
}

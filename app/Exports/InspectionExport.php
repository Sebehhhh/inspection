<?php

namespace App\Exports;

use App\Models\History;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class InspectionExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = History::with(['equipment', 'indicator']);

        if ($this->request->filled('inspection_date')) {
            $query->whereDate('created_at', $this->request->input('inspection_date')); // Gunakan created_at di DB
        }

        if ($this->request->filled('equipment_id')) {
            $query->where('equipment_id', decrypt($this->request->input('equipment_id')));
        }

        return $query->get()->map(function ($history, $index) {
            return [
                'No' => $index + 1,
                'Equipment' => $history->equipment->name,
                'Indicator' => $history->indicator->name,
                'Baseline' => $history->indicator->baseline,
                'Actual Value' => $history->actual_value,
                'Status' => ucfirst($history->status),
                'Inspection Date' => $history->created_at ? $history->created_at->format('Y-m-d') : 'N/A', // Gunakan created_at
            ];
        });
    }

    public function headings(): array
    {
        return ['No', 'Equipment', 'Indicator', 'Baseline', 'Actual Value', 'Status', 'Inspection Date'];
    }
}

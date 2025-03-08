<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inspection Report - PLTU Asam-Asam</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        .letterhead {
            text-align: center;
            padding: 10px 0;
            border-bottom: 2px solid #000;
            margin-bottom: 20px;
        }
        .letterhead h1 {
            margin: 0;
            font-size: 24px;
        }
        .letterhead h2 {
            margin: 5px 0 0 0;
            font-size: 16px;
            font-weight: normal;
        }
        .letterhead p {
            margin: 5px 0 0 0;
            font-size: 12px;
        }
        .printed-info {
            text-align: right;
            font-size: 10px;
            margin-bottom: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        th {
            background: #cce5ff;
        }
        @page {
            margin: 20mm;
        }
    </style>
</head>
<body>
    <div class="letterhead">
        <h1>PLTU ASAM-ASAM</h1>
        <h2>Inspection Report: Heat Loss Caused</h2>
        <p>Jl. Contoh Alamat No. 123, Kota Contoh</p>
    </div>
    <div class="printed-info">
        Tanggal Cetak: {{ date('d-m-Y H:i') }}<br>
        Dicetak Oleh: {{ $printedBy ?? 'Admin' }}
    </div>
    @if(request()->except('page'))
      <div class="filter-info" style="margin-bottom: 10px; font-size: 12px;">
          <strong>Filtered by:</strong>
          @foreach(request()->except('page') as $key => $value)
              @if($key == 'equipment_id')
                  @php
                      $decryptedEquipmentId = \Illuminate\Support\Facades\Crypt::decrypt($value);
                      $equipment = \App\Models\Equipment::find($decryptedEquipmentId);
                  @endphp
                  <span style="margin-right: 10px;">Equipment: {{ $equipment ? $equipment->name : 'N/A' }}</span>
              @else
                  <span style="margin-right: 10px;">{{ ucfirst(str_replace('_', ' ', $key)) }}: {{ $value }}</span>
              @endif
          @endforeach
      </div>
    @else
      <div class="filter-info" style="margin-bottom: 10px; font-size: 12px;">
          <strong>Menampilkan semua data</strong>
      </div>
    @endif
    <table>
        <thead>
            <tr>
                <th>No.</th>
                <th>Equipment</th>
                <th>Indicator</th>
                <th>Actual Value</th>
                <th>Status</th>
                <th>Problem Details</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($histories as $index => $history)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $history->equipment->name }}</td>
                    <td>{{ $history->indicator->name }}</td>
                    <td>{{ $history->actual_value }}</td>
                    <td>
                        @if ($history->status)
                            Normal
                        @else
                            Problem Detected
                        @endif
                    </td>
                    <td>
                        @if (!$history->status && isset($rules[$history->indicator_id]))
                            @php
                                $problemData = [];
                                foreach ($rules[$history->indicator_id] as $rule) {
                                    if ($rule->problem) {
                                        $problemData[] = [
                                            'name' => $rule->problem->name,
                                            'further_testing' => $rule->problem->further_testing,
                                            'corrective_action' => $rule->problem->corrective_action,
                                        ];
                                    }
                                }
                            @endphp
                            @if (count($problemData) > 0)
                                <table style="width: 100%; border-collapse: collapse;">
                                    <thead>
                                        <tr>
                                            <th>Problem</th>
                                            <th>Further Testing</th>
                                            <th>Corrective Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($problemData as $data)
                                            <tr>
                                                <td>{{ $data['name'] }}</td>
                                                <td>{{ $data['further_testing'] }}</td>
                                                <td>{{ $data['corrective_action'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                -
                            @endif
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
@extends('c_panel.layouts.app')
@section('title', 'Inspection History')

@section('content')
    @php
        use Illuminate\Support\Facades\Crypt;
        // Dekripsi equipment_id dari parameter request (jika ada)
        $selectedEquipmentId = null;
        if (request()->filled('equipment_id')) {
            try {
                $selectedEquipmentId = Crypt::decrypt(request('equipment_id'));
            } catch (\Exception $e) {
                $selectedEquipmentId = null;
            }
        }
    @endphp
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <!-- Judul dan filter equipment -->
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Inspection History</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Inspection History</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- Form Filter by Equipment -->
            <div class="row mb-3">
                <div class="col-12">
                    <form method="GET" action="{{ route('inspect.index') }}">
                        <div class="form-group">
                            <label for="equipment_filter">Filter by Equipment</label>
                            <select name="equipment_id" id="equipment_filter" class="form-control"
                                onchange="this.form.submit()">
                                <option value="">All Equipment</option>
                                @foreach ($allEquipments as $equip)
                                    <option value="{{ Crypt::encrypt($equip->id) }}"
                                        {{ $selectedEquipmentId == $equip->id ? 'selected' : '' }}>
                                        {{ $equip->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabel History Inspeksi -->
        <section class="section">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4>Inspection History</h4>
                            <!-- Tombol Create Inspection -->
                            <a href="{{ route('inspect.create') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-search"></i> Start Inspection
                            </a>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <!-- Tabel dengan outer spacing -->
                                <div class="table-responsive">
                                    <table class="table table-bordered">
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
                                                    <td>{{ $histories->firstItem() + $index }}</td>
                                                    <td>{{ $history->equipment->name }}</td>
                                                    <td>{{ $history->indicator->name }}</td>
                                                    <td>{{ $history->actual_value }}</td>
                                                    <td>
                                                        @if ($history->status)
                                                            <span class="badge bg-success">Normal</span>
                                                        @else
                                                            <span class="badge bg-danger">Problem Detected</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if (!$history->status && isset($rules[$history->indicator_id]))
                                                            @php
                                                                // Kumpulkan data problem, further_testing, dan corrective_action untuk indikator ini
                                                                $problemData = [];
                                                                foreach ($rules[$history->indicator_id] as $rule) {
                                                                    if ($rule->problem) {
                                                                        $problemData[] = [
                                                                            'name' => $rule->problem->name,
                                                                            'further_testing' =>
                                                                                $rule->problem->further_testing,
                                                                            'corrective_action' =>
                                                                                $rule->problem->corrective_action,
                                                                        ];
                                                                    }
                                                                }
                                                            @endphp
                                                            @if (count($problemData) > 0)
                                                                <table class="table table-bordered mb-0">
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
                                </div>
                                <!-- Pagination links -->
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $histories->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

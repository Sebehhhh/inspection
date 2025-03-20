@extends('c_panel.layouts.app')
@section('title', 'Inspection History')

@section('content')
    @php
        use Illuminate\Support\Facades\Crypt;
        // Dekripsi equipment_id dari parameter request (jika ada)
        $selectedEquipmentId = null;
        if (request()->filled('equipment_id')) {
            $selectedEquipmentId = Crypt::decrypt(request('equipment_id'));
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
            <div class="d-flex justify-content-end mb-3">
                <a href="{{ route('inspect.printHistory', request()->query()) }}" target="_blank" class="btn btn-primary">Print</a>
                <a href="{{ route('inspect.exportExcel', request()->query()) }}" class="btn btn-success ms-2">Export to Excel</a>
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
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        <div class="card-content">
                            <div class="card-body">
                                <!-- Filter berdasarkan tanggal inspeksi -->
                                <form method="GET" action="{{ route('inspect.index') }}" class="mb-3">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label for="inspection_date">Filter by Inspection Date</label>
                                            <input type="date" name="inspection_date" id="inspection_date"
                                                class="form-control"
                                                value="{{ request('inspection_date', now()->toDateString()) }}"
                                                onchange="this.form.submit()">
                                        </div>
                                        <!-- Pastikan equipment_id tetap ada jika sudah difilter sebelumnya -->
                                        @if (request()->filled('equipment_id'))
                                            <input type="hidden" name="equipment_id"
                                                value="{{ request('equipment_id') }}">
                                        @endif
                                    </div>
                                </form>
                                <!-- Tabel dengan outer spacing -->
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Equipment</th>
                                                <th>Indicator</th>
                                                <th>Baseline</th>
                                                <th>Actual Value</th>
                                                <th>Status</th>
                                                <th>Problem</th>
                                                <th>Further Testing</th>
                                                <th>Corrective Action</th>
                                                <th>Action Taken</th>
                                                <th>Possible Cause</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $rowspanData = [];
                                                foreach ($histories as $history) {
                                                    if (
                                                        $history->status != 'normal' &&
                                                        isset($rules[$history->indicator_id])
                                                    ) {
                                                        $problemCount = count($rules[$history->indicator_id]);
                                                        $rowspanData[$history->indicator_id] = $problemCount;
                                                    }
                                                }
                                            @endphp
                                            @foreach ($histories as $index => $history)
                                                @if ($history->status != 'normal' && isset($rules[$history->indicator_id]))
                                                    @php
                                                        $problemData = [];
                                                        foreach ($rules[$history->indicator_id] as $rule) {
                                                            if ($rule->problem) {
                                                                $problemData[] = [
                                                                    'id' => $rule->problem->id, // Tambahkan ID Problem
                                                                    'name' => $rule->problem->name,
                                                                    'further_testing' =>
                                                                        $rule->problem->further_testing,
                                                                    'corrective_action' =>
                                                                        $rule->problem->corrective_action,
                                                                    'action_taken' => $rule->problem->action_taken,
                                                                    'possible_cause' => $rule->problem->possible_cause,
                                                                ];
                                                            }
                                                        }
                                                        $firstRow = true;
                                                    @endphp
                                                    @foreach ($problemData as $data)
                                                        <tr>
                                                            @if ($firstRow)
                                                                <td rowspan="{{ count($problemData) }}">
                                                                    {{ $loop->parent->index + 1 }}</td>
                                                                <td rowspan="{{ count($problemData) }}">
                                                                    {{ $history->equipment->name }}</td>
                                                                <td rowspan="{{ count($problemData) }}">
                                                                    {{ $history->indicator->name }}</td>
                                                                <td rowspan="{{ count($problemData) }}">
                                                                    {{ $history->indicator->baseline }}</td>
                                                                <td rowspan="{{ count($problemData) }}">
                                                                    {{ $history->actual_value }}</td>
                                                                <td rowspan="{{ count($problemData) }}">
                                                                    @if ($history->status == 'normal')
                                                                        <span class="badge bg-success">Normal</span>
                                                                    @else
                                                                        <span
                                                                            class="badge bg-danger">{{ ucfirst($history->status) }}</span>
                                                                    @endif
                                                                </td>
                                                                @php $firstRow = false; @endphp
                                                            @endif
                                                            <td>{{ $data['name'] }}</td>
                                                            <td>{{ $data['further_testing'] }}</td>
                                                            <td>{{ $data['corrective_action'] }}</td>
                                                            <td>{{ $data['action_taken'] }}</td>
                                                            <td>{{ $data['possible_cause'] == 1 ? 'Ya' : ($data['possible_cause'] == 0 ? 'Tidak' : '-') }}
                                                            </td>
                                                            <td>
                                                                <!-- Tombol Edit untuk setiap problem -->
                                                                <button class="btn btn-sm btn-warning"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#editModal{{ $history->id }}-{{ $data['id'] }}">
                                                                    Edit
                                                                </button>

                                                                <!-- Modal Edit -->
                                                                <div class="modal fade"
                                                                    id="editModal{{ $history->id }}-{{ $data['id'] }}"
                                                                    tabindex="-1" aria-labelledby="editModalLabel"
                                                                    aria-hidden="true">
                                                                    <div class="modal-dialog">
                                                                        <div class="modal-content">
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title" id="editModalLabel">
                                                                                    Edit Inspection Data for
                                                                                    {{ $data['name'] }}</h5>
                                                                                <button type="button" class="btn-close"
                                                                                    data-bs-dismiss="modal"
                                                                                    aria-label="Close"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <form method="POST"
                                                                                    action="{{ route('inspect.update', $data['id']) }}">
                                                                                    @csrf
                                                                                    @method('PUT')
                                                                                    <input type="hidden" name="problem_id"
                                                                                        value="{{ $data['id'] }}">
                                                                                    <div class="mb-3">
                                                                                        <label for="action_taken"
                                                                                            class="form-label">Tindakan yang
                                                                                            Sudah Dilakukan</label>
                                                                                        <input type="text"
                                                                                            class="form-control"
                                                                                            name="action_taken"
                                                                                            value="{{ $data['action_taken'] }}">
                                                                                    </div>
                                                                                    <div class="mb-3">
                                                                                        <label for="possible_cause"
                                                                                            class="form-label">Kemungkinan
                                                                                            Penyebab</label>
                                                                                        <select
                                                                                            name="possible_cause[{{ $data['id'] }}]"
                                                                                            class="form-control">

                                                                                            <option value="0"
                                                                                                {{ $data['possible_cause'] == 0 ? 'selected' : '' }}>
                                                                                                Tidak</option>
                                                                                            <option value="1"
                                                                                                {{ $data['possible_cause'] == 1 ? 'selected' : '' }}>
                                                                                                Ya</option>
                                                                                        </select>
                                                                                    </div>
                                                                                    <button type="submit"
                                                                                        class="btn btn-primary">Simpan
                                                                                        Perubahan</button>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $history->equipment->name }}</td>
                                                        <td>{{ $history->indicator->name }}</td>
                                                        <td>{{ $history->indicator->baseline }}</td>
                                                        <td>{{ $history->actual_value }}</td>
                                                        <td>
                                                            <span class="badge bg-success">Normal</span>
                                                        </td>
                                                        <td colspan="6" class="text-center">No issues detected</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Pagination dihapus karena semua data ditampilkan -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

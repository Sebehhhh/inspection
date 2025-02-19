@extends('c_panel.layouts.app')
@section('title', 'Heat Loss Caused')

@section('content')
    @php
        use Illuminate\Support\Facades\Crypt;
        // Coba dekripsi equipment_id dari request jika ada, agar dapat dibandingkan
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
                    <h3>Heat Loss Caused Management</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Heat Loss Caused Management</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- Form filter by Equipment -->
            <div class="row mb-3">
                <div class="col-12">
                    <form method="GET" action="{{ route('problem.index') }}">
                        <div class="form-group">
                            <label for="equipment_filter">Filter by Equipment</label>
                            <select name="equipment_id" id="equipment_filter" class="form-control"
                                onchange="this.form.submit()">
                                <option value="">All Equipment</option>
                                @foreach ($allEquipments as $equipment)
                                    <option value="{{ Crypt::encrypt($equipment->id) }}"
                                        {{ $selectedEquipmentId == $equipment->id ? 'selected' : '' }}>
                                        {{ $equipment->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Basic Tables start -->
        <section class="section">
            <div class="row" id="basic-table">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ route('problem.create') }}" class="btn btn-success btn-sm float-end">
                                <i class="bi bi-plus"></i> Add
                            </a>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <!-- Table with outer spacing -->
                                <div class="table-responsive">
                                    <table class="table table-lg">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Equipment</th>
                                                <th>Name</th>
                                                <th>Parent Problem</th>
                                                <th>Further Testing</th>
                                                <th>Corrective Action</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($problems as $index => $problem)
                                                <tr>
                                                    <td class="text-bold-500">{{ $problems->firstItem() + $index }}</td>
                                                    <td>{{ $problem->equipment->name }}</td>
                                                    <td class="text-bold-500">{{ $problem->name }}</td>
                                                    <td>{{ optional($problem->parentProblem)->name }}</td>
                                                    <td>{{ $problem->further_testing }}</td>
                                                    <td>{{ $problem->corrective_action }}</td>
                                                    <td>
                                                        <a href="{{ route('problem.edit', encrypt($problem->id)) }}"
                                                            class="btn btn-primary btn-sm">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form action="{{ route('problem.destroy', $problem->id) }}"
                                                            method="POST" style="display:inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger btn-sm">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Pagination links -->
                                <div class="d-flex justify-content-center mt-3">
                                    {{ $problems->links('pagination::bootstrap-4') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

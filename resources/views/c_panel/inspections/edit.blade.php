@extends('c_panel.layouts.app')
@section('title', 'Edit Inspection Matrix')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Inspection Matrix</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('inspection.index') }}">Inspections Matrix</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Inspection Matrix</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Equipment Selection (optional, if you want to allow re-selecting equipment) -->
        <section id="equipment-selection">
            <form action="{{ route('inspection.editMatrix') }}" method="GET">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="equipment_id" class="form-label">Select Equipment</label>
                        <select name="equipment_id" id="equipment_id" class="form-control" onchange="this.form.submit()"
                            required>
                            <option value="">-- Select Equipment --</option>
                            @foreach ($equipments as $equipment)
                                <option value="{{ $equipment->id }}"
                                    {{ request('equipment_id') == $equipment->id ? 'selected' : '' }}>
                                    {{ $equipment->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </section>

        @if (request('equipment_id'))
            <!-- Editable Matrix Form: Rows = Problems, Columns = Indicators -->
            <form action="{{ route('inspection.updateMatrix') }}" method="POST">
                @csrf
                @method('PUT')
                <!-- Pass the selected equipment_id -->
                <input type="hidden" name="equipment_id" value="{{ request('equipment_id') }}" required>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Heat Loss Caused \ Heat Loss Mode</th>
                                @foreach ($indicators as $indicator)
                                    <th>
                                        {{ $indicator->name }}<br>
                                        <small>Baseline: {{ $indicator->baseline }}</small>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($problems as $problem)
                                <tr>
                                    <td>{{ $problem->name }}</td>
                                    @foreach ($indicators as $indicator)
                                        <td class="text-center">
                                            @php
                                                // Retrieve the existing inspection record for this problem and indicator, if any.
                                                $inspection = $inspections->firstWhere(function ($item) use (
                                                    $problem,
                                                    $indicator,
                                                ) {
                                                    return $item->problem_id == $problem->id &&
                                                        $item->indicator_id == $indicator->id;
                                                });
                                            @endphp
                                            <input type="number" step="any" class="form-control" placeholder="Real"
                                                required name="inspections[{{ $problem->id }}][{{ $indicator->id }}]"
                                                value="{{ $inspection ? $inspection->real : '' }}">
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary">Update Matrix</button>
                </div>
            </form>
        @endif
    </div>
@endsection

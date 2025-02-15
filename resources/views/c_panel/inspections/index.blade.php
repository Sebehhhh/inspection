@extends('c_panel.layouts.app')
@section('title', 'Inspections Matrix')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Inspections Matrix</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Inspections Matrix</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Equipment Selection -->
        <section id="equipment-selection">
            <form action="{{ route('inspection.index') }}" method="GET">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label for="equipment_id" class="form-label">Select Equipment</label>
                        <select name="equipment_id" id="equipment_id" class="form-control" onchange="this.form.submit()">
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
            <!-- Edit Matrix Button -->
            <div class="mb-3">
                <a href="{{ route('inspection.editMatrix', ['equipment_id' => request('equipment_id')]) }}"
                    class="btn btn-warning">
                    <i class="bi bi-pencil-square"></i> Edit Matrix
                </a>
            </div>
            <!-- Matrix Table: Rows = Problems, Columns = Indicators -->
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
                                            // Find the inspection record for this problem and indicator (if exists)
                                            $inspection = $inspections->firstWhere(function ($item) use (
                                                $problem,
                                                $indicator,
                                            ) {
                                                return $item->problem_id == $problem->id &&
                                                    $item->indicator_id == $indicator->id;
                                            });
                                        @endphp
                                        <input type="checkbox" disabled
                                            @if ($inspection && $inspection->real != $indicator->baseline) checked style="accent-color: green;" @endif>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection

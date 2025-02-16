@extends('c_panel.layouts.app')
@section('title', 'Solutions')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Solutions</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Solutions</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Equipment Selection (Optional Filter) -->
        <section id="equipment-selection">
            <form action="{{ route('solution.index') }}" method="GET">
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
            <!-- Editable Solutions Form: List of Problems with Further Testing and Corrective Actions -->
            <form action="{{ route('solution.updateMatrix') }}" method="POST">
                @csrf
                @method('PUT')
                <!-- Pass the selected equipment_id -->
                <input type="hidden" name="equipment_id" value="{{ request('equipment_id') }}" required>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Problem</th>
                                <th>Further Testing</th>
                                <th>Corrective Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($problems as $problem)
                                <tr>
                                    <td>{{ $problem->name }}</td>
                                    <td>
                                        <textarea class="form-control" name="solutions[{{ $problem->id }}][further_testing]" rows="3"
                                            placeholder="Enter further testing details">{{ isset($problem->solution) ? $problem->solution->further_testing : '' }}</textarea>
                                    </td>
                                    <td>
                                        <textarea class="form-control" name="solutions[{{ $problem->id }}][corrective_actions]" rows="3"
                                            placeholder="Enter corrective actions details">{{ isset($problem->solution) ? $problem->solution->corrective_actions : '' }}</textarea>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-3">
                    <button type="submit" class="btn btn-primary">Update Solutions</button>
                </div>
            </form>
        @endif
    </div>
@endsection

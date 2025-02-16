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
            <!-- Edit Matrix Button for Solutions -->
            <div class="mb-3">
                <a href="{{ route('solution.editMatrix', ['equipment_id' => request('equipment_id')]) }}"
                    class="btn btn-warning">
                    <i class="bi bi-pencil-square"></i> Edit Matrix
                </a>
            </div>
            <!-- Solutions Table: List of Problems with Further Testing and Corrective Actions -->
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
                                    @if ($problem->solution && $problem->solution->further_testing)
                                        <ul>
                                            @foreach (explode(',', $problem->solution->further_testing) as $item)
                                                <li>{{ trim($item) }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if ($problem->solution && $problem->solution->corrective_actions)
                                        <ul>
                                            @foreach (explode(',', $problem->solution->corrective_actions) as $action)
                                                <li>{{ trim($action) }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection

@extends('c_panel.layouts.app')
@section('title', 'Solutions (Result)')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <!-- Judul Halaman -->
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Solutions (Result)</h3>
                </div>
                <!-- Breadcrumb -->
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Solutions (Result)</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Equipment Selection (Optional Filter) -->
        <section id="equipment-selection">
            <form action="{{ route('result.index') }}" method="GET">
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
            @if ($problems->isEmpty())
                <div class="alert alert-info">
                    Tidak ada data solution untuk inspeksi tercentang.
                </div>
            @else
                <!-- Tabel Menampilkan Data Problem dan Solusinya (hanya yang tercentang) -->
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
        @endif
    </div>
@endsection

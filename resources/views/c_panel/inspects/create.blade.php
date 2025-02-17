@extends('c_panel.layouts.app')
@section('title', 'Inspect')

@section('content')
    @php
        use Illuminate\Support\Facades\Crypt;
    @endphp
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <!-- Judul dan filter equipment -->
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>
                        Inspection for Equipment: {{ $equipment->name }}
                    </h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('inspect.index') }}">Inspect</a></li>
                            <li class="breadcrumb-item active" aria-current="page">New Inspection</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- Form filter by Equipment -->
            <div class="row mb-3">
                <div class="col-12">
                    <form method="GET" action="{{ route('inspect.create') }}">
                        <div class="form-group">
                            <label for="equipment_id">Select Equipment</label>
                            <select name="equipment_id" id="equipment_id" class="form-control"
                                onchange="this.form.submit()">
                                @foreach ($allEquipments as $equip)
                                    <option value="{{ Crypt::encrypt($equip->id) }}"
                                        {{ $equip->id == $equipment->id ? 'selected' : '' }}>
                                        {{ $equip->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Inspection Form Section -->
        <section class="section">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Inspection Form</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <!-- Form untuk menyimpan actual_value tiap indicator -->
                                <form action="{{ route('inspect.store') }}" method="POST">
                                    @csrf
                                    <!-- Kirim equipment_id dalam bentuk terenkripsi -->
                                    <input type="hidden" name="equipment_id" value="{{ Crypt::encrypt($equipment->id) }}">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Indicator</th>
                                                    <th>Unit</th>
                                                    <th>Baseline</th>
                                                    <th>Actual Value</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($indicators as $index => $indicator)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $indicator->name }}</td>
                                                        <td>{{ $indicator->unit }}</td>
                                                        <td>{{ $indicator->baseline }}</td>
                                                        <td>
                                                            <input type="number"
                                                                name="actual_values[{{ $indicator->id }}]"
                                                                class="form-control" placeholder="Enter actual value"
                                                                value="{{ old('actual_values.' . $indicator->id) }}">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">Submit Inspection</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@extends('c_panel.layouts.app')
@section('title', 'Edit Indicator')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Indicator</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('indicator.index') }}">Indicator Management</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Indicator</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <section class="section">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <form action="{{ route('indicator.update', $indicator->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name', $indicator->name) }}">
                                        @error('name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="equipment_id">Equipment</label>
                                        <select id="equipment_id"
                                            class="form-control @error('equipment_id') is-invalid @enderror"
                                            name="equipment_id" required>
                                            <option value="">Select Equipment</option>
                                            @foreach ($equipments as $equipment)
                                                <option value="{{ $equipment->id }}"
                                                    {{ old('equipment_id', $indicator->equipment_id) == $equipment->id ? 'selected' : '' }}>
                                                    {{ $equipment->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('equipment_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="unit">Unit</label>
                                        <input type="text" class="form-control @error('unit') is-invalid @enderror"
                                            id="unit" name="unit" value="{{ old('unit', $indicator->unit) }}">
                                        @error('unit')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="baseline">Baseline</label>
                                        <input type="number" class="form-control @error('baseline') is-invalid @enderror"
                                            id="baseline" name="baseline"
                                            value="{{ old('baseline', $indicator->baseline) }}">
                                        @error('baseline')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                        <a href="{{ route('indicator.index') }}" class="btn btn-secondary">Back</a>
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

@extends('c_panel.layouts.app')
@section('title', 'Edit Problem')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Edit Problem</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('problem.index') }}">Problem Management</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Problem</li>
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
                                <form action="{{ route('problem.update', $problem->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')

                                    <div class="form-group">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                            id="name" name="name" value="{{ old('name', $problem->name) }}">
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
                                                    {{ old('equipment_id', $problem->equipment_id) == $equipment->id ? 'selected' : '' }}>
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
                                        <label for="parent_problem_id">Parent Problem (Optional)</label>
                                        <select id="parent_problem_id"
                                            class="form-control @error('parent_problem_id') is-invalid @enderror"
                                            name="parent_problem_id">
                                            <option value="">Select Parent Problem</option>
                                            @foreach ($parentProblems as $parentProblem)
                                                <option value="{{ $parentProblem->id }}"
                                                    {{ old('parent_problem_id', $problem->parent_problem_id) == $parentProblem->id ? 'selected' : '' }}>
                                                    {{ $parentProblem->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('parent_problem_id')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Field untuk Further Testing -->
                                    <div class="form-group">
                                        <label for="further_testing">Further Testing</label>
                                        <textarea class="form-control @error('further_testing') is-invalid @enderror" id="further_testing"
                                            name="further_testing" placeholder="Enter further testing details">{{ old('further_testing', $problem->further_testing) }}</textarea>
                                        @error('further_testing')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Field untuk Corrective Action -->
                                    <div class="form-group">
                                        <label for="corrective_action">Corrective Action</label>
                                        <textarea class="form-control @error('corrective_action') is-invalid @enderror" id="corrective_action"
                                            name="corrective_action" placeholder="Enter corrective action details">{{ old('corrective_action', $problem->corrective_action) }}</textarea>
                                        @error('corrective_action')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>


                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                        <a href="{{ route('problem.index') }}" class="btn btn-secondary">Back</a>
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

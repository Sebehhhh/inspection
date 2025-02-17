@extends('c_panel.layouts.app')
@section('title', 'Add Problem')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Add Problem</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('problem.index') }}">Problem Management</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add Problem</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Basic multiple Column Form section start -->
        <section id="multiple-column-form">
            <div class="row match-height">
                <div class="col-12">
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <form class="form" action="{{ route('problem.store') }}" method="POST">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="name">Name</label>
                                                <input type="text" id="name" class="form-control" name="name"
                                                    placeholder="Problem Name" required>
                                                @error('name')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="equipment_id">Equipment</label>
                                                <select id="equipment_id" class="form-control" name="equipment_id" required>
                                                    <option value="">Select Equipment</option>
                                                    @foreach ($equipments as $equipment)
                                                        <option value="{{ $equipment->id }}">{{ $equipment->name }}</option>
                                                    @endforeach
                                                </select>
                                                @error('equipment_id')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12">
                                            <div class="form-group">
                                                <label for="parent_problem_id">Parent Problem (Optional)</label>
                                                <select id="parent_problem_id" class="form-control"
                                                    name="parent_problem_id">
                                                    <option value="">Select Parent Problem</option>
                                                    @foreach ($parentProblems as $parentProblem)
                                                        <option value="{{ $parentProblem->id }}">{{ $parentProblem->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('parent_problem_id')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <!-- Tambahan field Further Testing -->
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="further_testing">Further Testing</label>
                                                <textarea id="further_testing" class="form-control" name="further_testing" placeholder="Enter further testing details">{{ old('further_testing') }}</textarea>
                                                @error('further_testing')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <!-- Tambahan field Corrective Action -->
                                        <div class="col-md-6 col-12">
                                            <div class="form-group">
                                                <label for="corrective_action">Corrective Action</label>
                                                <textarea id="corrective_action" class="form-control" name="corrective_action"
                                                    placeholder="Enter corrective action details">{{ old('corrective_action') }}</textarea>
                                                @error('corrective_action')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="col-12 d-flex justify-content-end">
                                            <button type="submit" class="btn btn-primary me-1 mb-1">Submit</button>
                                            <button type="reset" class="btn btn-light-secondary me-1 mb-1">Reset</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- // Basic multiple Column Form section end -->
    </div>
@endsection

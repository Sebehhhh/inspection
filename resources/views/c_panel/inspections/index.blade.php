@extends('c_panel.layouts.app')
@section('title', 'Inspections Management')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Inspections Management</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Inspections Management</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Basic Tables start -->
        <section class="section">
            <div class="row" id="basic-table">
                <div class="col-12 col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <a href="{{ route('inspection.create') }}" class="btn btn-success btn-sm float-end">
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
                                                <th>Indicator</th>
                                                <th>Problem</th>
                                                <th>Baseline</th>
                                                <th>Real</th>
                                                <th>Status</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($inspections as $index => $inspection)
                                                <tr>
                                                    <td class="text-bold-500">{{ $index + 1 }}</td>
                                                    <td>{{ $inspection->equipment->name }}</td>
                                                    <td>{{ $inspection->indicator->name }}</td>
                                                    <td>{{ optional($inspection->problem)->name }}</td>
                                                    <td>{{ $inspection->baseline }}</td>
                                                    <td>{{ $inspection->real }}</td>
                                                    <td>{{ $inspection->status ? 'Yes' : 'No' }}</td>
                                                    <td>
                                                        <a href="{{ route('inspection.edit', \Illuminate\Support\Facades\Crypt::encrypt($inspection->id)) }}" class="btn btn-primary btn-sm">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form action="{{ route('inspection.destroy', $inspection->id) }}" method="POST" style="display:inline;">
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

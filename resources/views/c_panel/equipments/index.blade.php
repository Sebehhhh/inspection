@extends('c_panel.layouts.app')
@section('title', 'Equipment')

@section('content')
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>Equipment Management</h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Equipment Management</li>
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
                            <a href="{{ route('equipment.create') }}" class="btn btn-success btn-sm float-end">
                                <i class="bi bi-plus"></i> Add
                            </a>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <!-- Table with outer spacing -->
                                <div class="table-responsive">
                                    <table id="equipmentTable" class="table table-lg">
                                        <thead>
                                            <tr>
                                                <th>No.</th>
                                                <th>Name</th>
                                                <th>Description</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($equipments as $index => $equipment)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>{{ $equipment->name }}</td>
                                                    <td>{{ $equipment->description }}</td>
                                                    <td>
                                                        <a href="{{ route('equipment.edit', encrypt($equipment->id)) }}"
                                                            class="btn btn-primary btn-sm">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <form action="{{ route('equipment.destroy', $equipment->id) }}"
                                                            method="POST" style="display:inline;">
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
                                <!-- DataTables akan meng-handle paging, sorting, dan pencarian -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
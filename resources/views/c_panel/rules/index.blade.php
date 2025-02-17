@extends('c_panel.layouts.app')
@section('title', 'Rules Management')

@section('content')
    @php
        use Illuminate\Support\Facades\Crypt;
    @endphp
    <div class="page-heading">
        <div class="page-title">
            <div class="row">
                <!-- Dropdown untuk memilih Equipment -->
                <div class="col-12 col-md-6 order-md-1 order-last">
                    <h3>
                        Rules Management for Equipment:
                        {{ $equipment->name }}
                    </h3>
                </div>
                <div class="col-12 col-md-6 order-md-2 order-first">
                    <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('rules.index') }}">Rules</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Manage Rules</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!-- Form untuk memilih equipment -->
        <section class="section">
            <div class="row mb-4">
                <div class="col-12">
                    <form method="GET" action="{{ route('rules.index') }}">
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
        </section>

        <!-- Rules Matrix Table start -->
        <section class="section">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Rules Matrix</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">

                                @if ($errors->any())
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <!-- Form untuk menyimpan rules -->
                                <form action="{{ route('rules.store') }}" method="POST">
                                    @csrf
                                    <!-- Hidden field untuk mengirim equipment_id terenkripsi -->
                                    <input type="hidden" name="equipment_id" value="{{ Crypt::encrypt($equipment->id) }}">
                                    <div class="table-responsive">
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Problem \ Indicator</th>
                                                    @foreach ($indicators as $indicator)
                                                        <th>{{ $indicator->name }}</th>
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
                                                                    // Cek apakah rule untuk kombinasi problem dan indicator sudah ada
                                                                    $exists = $rules->contains(function ($rule) use (
                                                                        $problem,
                                                                        $indicator,
                                                                    ) {
                                                                        return $rule->problem_id == $problem->id &&
                                                                            $rule->indicator_id == $indicator->id;
                                                                    });
                                                                @endphp
                                                                <input type="checkbox"
                                                                    name="rules[{{ $problem->id }}][{{ $indicator->id }}]"
                                                                    {{ $exists ? 'checked' : '' }}>
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="mt-3">
                                        <button type="submit" class="btn btn-primary">Save Rules</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Rules Matrix Table end -->
    </div>
@endsection

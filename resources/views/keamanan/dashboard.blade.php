@extends('layout')
@section('content')
<div class="d-flex justify-content-between my-3">
    <div class="row">
        <div class="col-6">
            <p>Mulai</p>
            <input data-provide="datepicker" data-date-autoclose="true" class="form-control" data-date-format="dd/mm/yyyy">
        </div>
        <div class="col-6">
            <p>Sampai</p>
            <input data-provide="datepicker" data-date-autoclose="true" class="form-control" data-date-format="dd/mm/yyyy">
        </div>
    </div>
</div>
<div class="row clearfix">
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card">
            <div class="body">
                <h3 class="mb-1">{{ $pelanggar }}</h3>
                <div>Santri Melanggar Bulan Ini</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card">
            <div class="body">
                <h3 class="mb-1">{{ $pulang }}</h3>
                <div>Santri Izin Pulang Bulan Ini</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card">
            <div class="body">
                <h3 class="mb-1">{{ $keluar }}</h3>
                <div>Santri Izin Keluar Bulan Ini</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card">
            <div class="body">
                {{-- <h3 class="mb-1">{{ $sp1 }}</h3> --}}
                <h3 class="mb-1">0</h3>
                <div>Santri dengan SP 1</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card">
            <div class="body">
                {{-- <h3 class="mb-1">{{ $sp2 }}</h3> --}}
                <h3 class="mb-1">0</h3>
                <div>Santri dengan SP 2</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card">
            <div class="body">
                {{-- <h3 class="mb-1">{{ $sp3 }}</h3> --}}
                <h3 class="mb-1">0</h3>
                <div>Santri dengan SP 3</div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('script')
<script src="{{ asset('vendor/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
@endsection
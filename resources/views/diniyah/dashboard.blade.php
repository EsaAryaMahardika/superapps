@extends('layout')
@section('content')
<div class="row clearfix">
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card">
            <div class="body">
                {{-- <h3 class="mb-1">{{ $pengajar }}</h3> --}}
                <div>Total Pengajar</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card">
            <div class="body">
                {{-- <h3 class="mb-1">{{ $santri }}</h3> --}}
                <div>Pengajar Luar</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card">
            <div class="body">
                {{-- <h3 class="mb-1">{{ $pengurus }}</h3> --}}
                <div>Pengajar Alumni</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card">
            <div class="body">
                {{-- <h3 class="mb-1">{{ $kepkam }}</h3> --}}
                <div>Pengajar Mahad Aly</div>
            </div>
        </div>
    </div>
</div>
@endsection
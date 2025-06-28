@extends('layout')
@section('content')
<div class="row clearfix">
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card">
            <div class="body">
                {{-- <h3 class="mb-1">{{ $asrama }}</h3> --}}
                <div>Asrama</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card">
            <div class="body">
                {{-- <h3 class="mb-1">{{ $santri }}</h3> --}}
                <div>Total Santri Putra</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card">
            <div class="body">
                {{-- <h3 class="mb-1">{{ $pengurus }}</h3> --}}
                <div>Total Pengurus</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card">
            <div class="body">
                {{-- <h3 class="mb-1">{{ $kepkam }}</h3> --}}
                <div>Total Kepala Kamar</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card">
            <div class="body">
                {{-- <h3 class="mb-1">{{ $nokepkam }}</h3> --}}
                <div>Total Non Kepala Kamar</div>
            </div>
        </div>
    </div>
</div>
@endsection
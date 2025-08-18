@extends('layout')
@section('content')
<div class="row clearfix">
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card">
            <div class="body">
                <h3 class="mb-1">{{ count($boyong) }}</h3>
                <div>Total Santri Boyong</div>
            </div>
        </div>
    </div>
</div>
@endsection
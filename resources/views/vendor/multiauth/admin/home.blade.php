@extends('multiauth::layouts.admin')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ ucfirst(config('multiauth.prefix')) }} Dashboard</div>
                <div class="card-body">
                    @include('multiauth::message')
                    Da completare con grafici/statistiche che possono essere interessanti.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
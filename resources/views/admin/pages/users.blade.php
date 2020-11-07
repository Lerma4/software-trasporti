@extends('multiauth::layouts.admin')
@section('content')
<div class="col-12 pages-content">
    <div class="card">
        <div class="card-body">
            @include('multiauth::message')
            You are logged in to {{ config('multiauth.prefix') }} side!
        </div>
    </div>
</div>
@endsection
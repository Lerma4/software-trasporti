@extends('layouts.app')

@section('styles')
@livewireStyles
@endsection

@section('content')
<div class="col-12 pages-content">
    <div class="card">
        <div class="card-body">
            @livewire('reports-form')
        </div>
    </div>
</div>
@endsection

@section('scripts')

@livewireScripts
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.6.0/dist/alpine.min.js" defer></script>

@endsection
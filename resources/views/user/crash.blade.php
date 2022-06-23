@extends('layouts.app')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{asset('jqueryui/jquery-ui.min.css')}}">

<!-- NECESSARI PER MEDIALIBRARY PRO -->
@livewireStyles
<link rel="stylesheet" type="text/css" href="{{ asset('medialibrary_css/styles.css') }}" />
@endsection

@section('content')
<div class="col-12 pages-content">
    <div class="card">
        <div class="card-body">

            @include('multiauth::message')

            <form method="POST" action="{{route('crash.store')}}" id="document" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label for="date">@lang('Date'):</label>
                    <input type="date" class="form-control" name="date"
                        max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>
                </div>
                <div class="form-group">
                    <label for="plate">@lang("Truck's plate"):</label>
                    <select name="plate" class="form-control" required>
                        <option value=""></option>
                        @foreach ($plates as $plate)
                        <option value="{{ $plate->plate }}">{{ $plate->plate }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="plate_s">@lang("Targa semirimorchio") (@lang("Optional")):</label>
                    <select name="plate_s" class="form-control">
                        <option value=""></option>
                        @foreach ($plates_semi as $plate)
                        <option value="{{ $plate->plate }}">{{ $plate->plate }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="description">@lang('Incident description'):</label>
                    <textarea class="form-control" name="description" rows="3" maxlength="20000" minlength="10"
                        required></textarea>
                </div>

                <label>@lang("Incident photos"):</label>

                <x-media-library-attachment multiple max-items="5" rules="mimes:png,jpg,jpeg,heif|max:10000"
                    name="photos" />

                <br>

                <hr>

                <button type="submit" class="btn btn-primary submit-document btn-block">
                    <span class="spinner-border spinner-border-sm loader-submit hidden" role="status"
                        aria-hidden="true"></span>
                    @lang('Submit')
                </button>

            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<!-- LIBRERIE NECESSARIE PER MEDIALIBRARY PRO -->
@livewireScripts
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.6.0/dist/alpine.min.js" defer></script>

{{-- <script src="{{ asset('js/fileupload/vendor/jquery.ui.widget.js') }}" defer></script>
<script src="{{ asset('js/fileupload/jquery.iframe-transport.js') }}" defer></script>
<script src="{{ asset('js/fileupload/jquery.fileupload.js') }}" defer></script>
<script src="{{ asset('js/fileupload/jquery.fileupload-process.js') }}" defer></script>
<script src="{{ asset('js/fileupload/jquery.fileupload-image.js') }}" defer></script> --}}

<script>
    $(document).ready(function() {
        // FADE OUT DEI MESSAGGI DAI CONTROLLER
    
        $('.message').delay(4000).fadeOut();
    });
</script>

@endsection
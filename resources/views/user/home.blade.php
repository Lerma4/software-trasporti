@extends('layouts.app')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{asset('jqueryui/jquery-ui.min.css')}}">
@endsection

@section('content')
<div class="col-12 pages-content">
    <div class="card">
        <div class="card-body">

            <div id="form-result"></div>

            <div class="form-group">
                <label>@lang('Seleziona la tipologia di viaggio:')</label>
                <select class="form-control trip" id="type" name="type" required>
                    <option value="merci">@lang('Carico/scarico merci')</option>
                    <option value="officina">@lang('Officina/gommista')</option>
                    <option value="vuoto">@lang('A vuoto')</option>
                </select>
            </div>

            <form id="merci">
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
                        <option value="{{ $plate->plate }}">{{ $plate->plate }} (km: {{ $plate->km }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="start">@lang('Città di partenza'):</label>
                    <input type="text" class="form-control autocomplete" name="start" required>
                </div>
                <div class="form-group">
                    <label id="label-stop">@lang('Stops') (@lang("Optional")):</label>
                    <div id="row-stop" class="row justify-content-center">
                        <div class="col-auto">
                            <button id="buttonAdd" type="button" class="btn btn-success btn-license">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div class="col-auto">
                            <button id="buttonRemove" type="button" class="btn btn-danger btn-license">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="destination">@lang('Destination'):</label>
                    <input type="text" class="form-control autocomplete" name="destination" required>
                </div>
                <div class="form-group">
                    <label for="km">@lang('Km finali'):</label>
                    <input type="number" class="form-control" name="km" min="0" required>
                </div>
                <div class="form-group">
                    <label for="petrol_station">@lang('Luogo rifornimento'):</label>
                    <select name="petrol_station" class="form-control">
                        <option selected value="not done">@lang('Not done')</option>
                        <option value="petrol_station">@lang('Petrol station')</option>
                        <option value="tank">@lang('Tank')</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="fuel">@lang('Fuel') (@lang('litres')):</label>
                    <input type="number" class="form-control" name="fuel" min="0" value="0" required>
                </div>
                <div class="form-group">
                    <label for="cost">@lang('Fuel cost'):</label>
                    <input type="number" class="form-control" name="cost" min="0" value="0" required>
                </div>
                <div class="form-group">
                    <label for="plate_s">@lang("Semitrailer's plate") (@lang("Optional")):</label>
                    <select name="plate_s" class="form-control">
                        <option value=""></option>
                        @foreach ($plates_semi as $plate)
                        <option value="{{ $plate->plate }}">{{ $plate->plate }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="container">@lang('Sigla container') (@lang("Optional")):</label>
                    <input type="text" class="form-control" name="container">
                </div>
                <div class="form-group">
                    <label for="note">@lang('Note') (@lang("Optional")):</label>
                    <textarea class="form-control" name="note" rows="3" max="200"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary submit-merci">
                        <span class="spinner-border spinner-border-sm loader-submit hidden" role="status"
                            aria-hidden="true"></span>
                        @lang('Submit')
                    </button>
                </div>
            </form>

            <form id="officina">
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
                        <option value="{{ $plate->plate }}">{{ $plate->plate }} (km: {{ $plate->km }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="start">@lang('Città di partenza'):</label>
                    <input type="text" class="form-control autocomplete" name="start" required>
                </div>
                <div class="form-group">
                    <label for="destination">@lang('Destinazione'):</label>
                    <input type="text" class="form-control autocomplete" name="destination" required>
                </div>
                <div class="form-group">
                    <label for="garage">@lang('Garage'):</label>
                    <input type="text" class="form-control" name="garage" required>
                </div>
                <div class="form-group">
                    <label for="km">@lang('Km finali'):</label>
                    <input type="number" class="form-control" name="km" min="0" required>
                </div>
                <div class="form-group">
                    <label for="petrol_station">@lang('Luogo rifornimento'):</label>
                    <select name="petrol_station" class="form-control">
                        <option selected value="not done">@lang('Not done')</option>
                        <option value="petrol_station">@lang('Petrol station')</option>
                        <option value="tank">@lang('Tank')</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="fuel">@lang('Fuel') (@lang('litres')):</label>
                    <input type="number" class="form-control" name="fuel" min="0" value="0" required>
                </div>
                <div class="form-group">
                    <label for="cost">@lang('Fuel cost'):</label>
                    <input type="number" class="form-control" name="cost" min="0" value="0" required>
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
                    <label for="container">@lang('Note') (@lang("Optional")):</label>
                    <textarea class="form-control" name="note" rows="3"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary submit-officina">
                        <span class="spinner-border spinner-border-sm loader-submit hidden" role="status"
                            aria-hidden="true"></span>
                        @lang('Submit')
                    </button>
                </div>
            </form>

            <form id="vuoto">
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
                        <option value="{{ $plate->plate }}">{{ $plate->plate }} (km: {{ $plate->km }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="start">@lang('Città di partenza'):</label>
                    <input type="text" class="form-control autocomplete" name="start" required>
                </div>
                <div class="form-group">
                    <label for="destination">@lang('Destinazione'):</label>
                    <input type="text" class="form-control autocomplete" name="destination" required>
                </div>
                <div class="form-group">
                    <label for="km">@lang('Km finali'):</label>
                    <input type="number" class="form-control" name="km" min="0" required>
                </div>
                <div class="form-group">
                    <label for="petrol_station">@lang('Luogo rifornimento'):</label>
                    <select name="petrol_station" class="form-control">
                        <option selected value="not done">@lang('Not done')</option>
                        <option value="petrol_station">@lang('Petrol station')</option>
                        <option value="tank">@lang('Tank')</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="fuel">@lang('Fuel') (@lang('litres')):</label>
                    <input type="number" class="form-control" name="fuel" min="0" value="0" required>
                </div>
                <div class="form-group">
                    <label for="cost">@lang('Fuel cost'):</label>
                    <input type="number" class="form-control" name="cost" min="0" value="0" required>
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
                    <label for="container">@lang('Note') (@lang("Optional")):</label>
                    <textarea class="form-control" name="note" rows="3"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary submit-vuoto">
                        <span class="spinner-border spinner-border-sm loader-submit hidden" role="status"
                            aria-hidden="true"></span>
                        @lang('Submit')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{asset('jqueryui/jquery-ui.min.js')}}" type="text/javascript" defer></script>

<script>
    $(document).ready(function() {
        // SCELTA DELLA TIPOLOGIA DI VIAGGIO

        $('.trip').on('change', function() {
            switch ($(this).val()) {
                case 'officina':
                    $('#merci').slideUp();
                    $('#vuoto').slideUp();
                    $('#officina').slideDown();
                    break;

                case 'merci':
                    $('#officina').slideUp();
                    $('#vuoto').slideUp();
                    $('#merci').slideDown();
                    break;

                case 'vuoto':
                    $('#officina').slideUp();
                    $('#merci').slideUp();
                    $('#vuoto').slideDown();
                    break;

                default:
                    $('#officina').slideUp();
                    $('#merci').slideUp();
                    $('#vuoto').slideUp();
                    break;
            }
        });

        // GESTIONE TAPPE

        $('#buttonAdd').on('click', function(event) {
            var lastInput = $('#row-stop').prev();
            var id;
            var idNew = [];
            if (lastInput.attr('id') == 'label-stop') {
                id = 1;
            } else {
                idNew = lastInput.attr('id').split("_");
                idNew[1] = parseInt(idNew[1]);
                id = idNew[1] + 1;
            }

            var html = '<input type="text" class="form-control autocomplete input-stop" id="stop_' + id + '" name="stop_' + id + '" required>';

            $('#row-stop').before(html);
        });

        $('#buttonRemove').on('click', function(event) {
            var lastInput = $('#row-stop').prev();

            if (lastInput.attr('id') != 'label-stop') {
                lastInput.remove();
            }
        });

        // AUTOCOMPLETAMENTO CITTA'

        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $(document).on('keydown.autocomplete', '.autocomplete', function() {
            $(this).autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "{{route('autocomplete')}}",
                        type: 'post',
                        dataType: "json",
                        data: {
                            _token: CSRF_TOKEN,
                            search: request.term
                        },
                        success: function(data) {
                            response(data);
                        }
                    });
                },
                select: function(event, ui) {
                    $(this).val(ui.item.label);
                    return false;
                },
                minLength: 2
            });
        });

        // SUBMIT FORMS

        // CARICO SCARICO MERCI

        $('#merci').on('submit', function(event) {
            event.preventDefault();
            var form = $(this).closest('form');

            $('#form-result').text('');
            $('#form-result').fadeIn();

            $.ajax({
                url: "{{route('tripmerci')}}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: $(form).serialize(),
                dataType: "json",
                beforeSend: function() {
                    $('.loader-submit').removeClass('hidden');
                    $('.submit-merci').contents().last().replaceWith('@lang("Loading...")');
                },
                success: function(data) {
                    var html = '';
                    if (data.errors) {
                        html = '<div class="alert alert-danger">';
                        for (var count = 0; count < data.errors.length; count++) {
                            html += '<p>' + data.errors[count] + '</p>';
                        }
                        html += '</div>';
                    }
                    if (data.success) {
                        html = '<div class="alert alert-success">' + data.success + '</div>';
                        var lastInput = $('#row-stop').prev();
                        while (lastInput.attr('id') != 'label-stop') {
                            lastInput.remove();
                            lastInput = $('#row-stop').prev();
                        }
                        $('#merci')[0].reset();
                    }
                    $(window).scrollTop(0);
                    $('#form-result').html(html);
                    $('#form-result').delay(4000).fadeOut();
                },
                complete: function() {
                    $('.loader-submit').addClass('hidden');
                    $('.submit-merci').contents().last().replaceWith('@lang("Submit")');
                },
            });
        });

        // OFFICINA

        $('#officina').on('submit', function(event) {
            event.preventDefault();
            var form = $(this).closest('form');

            $('#form-result').text('');
            $('#form-result').fadeIn();

            $.ajax({
                url: "{{route('tripofficina')}}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: $(form).serialize(),
                dataType: "json",
                beforeSend: function() {
                    $('.loader-submit').removeClass('hidden');
                    $('.submit-officina').contents().last().replaceWith('@lang("Loading...")');
                },
                success: function(data) {
                    var html = '';
                    if (data.errors) {
                        html = '<div class="alert alert-danger">';
                        for (var count = 0; count < data.errors.length; count++) {
                            html += '<p>' + data.errors[count] + '</p>';
                        }
                        html += '</div>';
                    }
                    if (data.success) {
                        html = '<div class="alert alert-success">' + data.success + '</div>';
                        $('#officina')[0].reset();
                    }
                    $(window).scrollTop(0);
                    $('#form-result').html(html);
                    $('#form-result').delay(4000).fadeOut();
                },
                complete: function() {
                    $('.loader-submit').addClass('hidden');
                    $('.submit-officina').contents().last().replaceWith('@lang("Submit")');
                },
            });
        });

        // A VUOTO

        $('#vuoto').on('submit', function(event) {
            event.preventDefault();
            var form = $(this).closest('form');

            $('#form-result').text('');
            $('#form-result').fadeIn();

            $.ajax({
                url: "{{route('tripvuoto')}}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: $(form).serialize(),
                dataType: "json",
                beforeSend: function() {
                    $('.loader-submit').removeClass('hidden');
                    $('.submit-vuoto').contents().last().replaceWith('@lang("Loading...")');
                },
                success: function(data) {
                    var html = '';
                    if (data.errors) {
                        html = '<div class="alert alert-danger">';
                        for (var count = 0; count < data.errors.length; count++) {
                            html += '<p>' + data.errors[count] + '</p>';
                        }
                        html += '</div>';
                    }
                    if (data.success) {
                        html = '<div class="alert alert-success">' + data.success + '</div>';
                        $('#vuoto')[0].reset();
                    }
                    $(window).scrollTop(0);
                    $('#form-result').html(html);
                    $('#form-result').delay(4000).fadeOut();
                },
                complete: function() {
                    $('.loader-submit').addClass('hidden');
                    $('.submit-vuoto').contents().last().replaceWith('@lang("Submit")');
                },
            });
        });
    });
</script>

@endsection
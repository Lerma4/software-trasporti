@extends('layouts.app')

@section('content')
<div class="col-12 pages-content">
    <div class="card">
        <div class="card-body">

            <div id="form-falied" class="alert alert-danger" role="alert"></div>

            <div id="form-success" class="alert alert-success" role="alert"></div>

            <div class="form-group">
                <label>@lang('Seleziona la tipologia di viaggio:')</label>
                <select class="form-control trip" id="type" name="type" required>
                    <option value="merci">@lang('Carico/scarico merce')</option>
                    <option value="officina">@lang('Officina/gommista')</option>
                    <option value="vuoto">@lang('A vuoto')</option>
                </select>
            </div>

            <form id="merci">
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
                    <label for="destination">@lang('Destinazione finale'):</label>
                    <input type="text" class="form-control" name="destination" required>
                </div>
                <div class="form-group">
                    <label for="km">@lang('Km finali'):</label>
                    <input type="number" class="form-control" name="km" min="0" required>
                </div>
                <div class="form-group">
                    <label for="fuel">@lang('Fuel'):</label>
                    <input type="number" class="form-control" name="fuel" min="0" required>
                </div>
                <div class="form-group">
                    <label for="fuel_cost">@lang('Fuel cost'):</label>
                    <input type="number" class="form-control" name="fuel_cost" min="0" required>
                </div>
                <div class="form-group">
                    <label for="plate_semi">@lang("Targa semirimorchio") (@lang("Optional")):</label>
                    <select name="plate_semi" class="form-control">
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
                    <label for="container">@lang('Note') (@lang("Optional")):</label>
                    <textarea class="form-control" name="note" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <span class="spinner-border spinner-border-sm loader-submit hidden" role="status" aria-hidden="true"></span>
                    <button type="submit" class="btn btn-primary submit">@lang('Submit')</button>
                </div>
            </form>

            <form id="officina">
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
                    <label for="destination">@lang('Destinazione'):</label>
                    <input type="text" class="form-control" name="destination" required>
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
                    <label for="fuel">@lang('Fuel'):</label>
                    <input type="number" class="form-control" name="fuel" min="0" required>
                </div>
                <div class="form-group">
                    <label for="fuel_cost">@lang('Fuel cost'):</label>
                    <input type="number" class="form-control" name="fuel_cost" min="0" required>
                </div>
                <div class="form-group">
                    <label for="plate_semi">@lang("Targa semirimorchio") (@lang("Optional")):</label>
                    <select name="plate_semi" class="form-control">
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
                <div class="form-group">
                    <span class="spinner-border spinner-border-sm loader-submit hidden" role="status" aria-hidden="true"></span>
                    <button type="submit" class="btn btn-primary submit">@lang('Submit')</button>
                </div>
            </form>

            <form id="vuoto">
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
                    <label for="destination">@lang('Destinazione'):</label>
                    <input type="text" class="form-control" name="destination" required>
                </div>
                <div class="form-group">
                    <label for="km">@lang('Km finali'):</label>
                    <input type="number" class="form-control" name="km" min="0" required>
                </div>
                <div class="form-group">
                    <label for="fuel">@lang('Fuel'):</label>
                    <input type="number" class="form-control" name="fuel" min="0" required>
                </div>
                <div class="form-group">
                    <label for="fuel_cost">@lang('Fuel cost'):</label>
                    <input type="number" class="form-control" name="fuel_cost" min="0" required>
                </div>
                <div class="form-group">
                    <label for="plate_semi">@lang("Targa semirimorchio") (@lang("Optional")):</label>
                    <select name="plate_semi" class="form-control">
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
                <div class="form-group">
                    <span class="spinner-border spinner-border-sm loader-submit hidden" role="status" aria-hidden="true"></span>
                    <button type="submit" class="btn btn-primary submit">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')

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

            var html = '<input type="text" class="form-control input-stop" id="stop_' + id + '" name="stop_' + id + '" required>';

            $('#row-stop').before(html);
        });

        $('#buttonRemove').on('click', function(event) {
            var lastInput = $('#row-stop').prev();

            if (lastInput.attr('id') != 'label-stop') {
                lastInput.remove();
            }
        });

        // SUBMIT FORM

        $('#merci').on('submit', function(event) {
            event.preventDefault();
            var form = $(this).closest('form');

            $.ajax({
                url: url,
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: $(form).serialize(),
                dataType: "json",
                beforeSend: function() {
                    $('.loader-submit').removeClass('hidden');
                    $('.submit').contents().last().replaceWith('@lang("Loading...")');
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
                        if ($('#addUser').length != 0) {
                            $('#addUser')[0].reset(); //PER CONTROLLARE SE SONO IN EDIT O IN ADD E EVITARE ERRORI DEL JAVASCRIPT

                            var lastInput = $('#row-licenses').prev();
                            while (lastInput.attr('id') != 'license-title') {
                                lastInput.remove();
                                lastInput = $('#row-licenses').prev();
                            }
                        }
                        $('#datatable').DataTable().ajax.reload();
                    }
                    $('#form-result').html(html);
                    $('#form-result').delay(4000).fadeOut();
                },
                complete: function() {
                    $('.loader-submit').addClass('hidden');
                    $('.submit').contents().last().replaceWith('@lang("Submit")');
                },
            });
        });
    });
</script>

@endsection
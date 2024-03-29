@extends('multiauth::layouts.admin')

@section('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.5/css/buttons.bootstrap4.min.css">
<link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css"
    rel="stylesheet" />

<link rel="stylesheet" type="text/css" href="{{asset('jqueryui/jquery-ui.min.css')}}">
@endsection

@section('content')

<div class="col-12 pages-content">
    <div class="card">
        <div class="card-body">
            @include('multiauth::message')
            <div class="row justify-content-between page-row">
                <div class="col-sm">
                    <button id="btn-add" type="button" class="btn btn-primary" data-toggle="modal"
                        data-target="#modal-add">
                        @lang('New')
                    </button>
                    <button id="btn-edit" type="button" class="btn btn-secondary" data-toggle="modal"
                        data-target="#modal-edit" disabled>
                        @lang('Edit')
                    </button>
                    <button type="button" class="btn btn-danger" id="btn-delete" disabled>
                        @lang('Delete')
                    </button>
                </div>
                <div class="col-sm-auto">
                    <form>
                        <div class="form-inline">
                            <label id="search_type">@lang('From'):</label>
                            <input type="date" class="form-control select-input-date-from" data-column="3">
                            <label id="search_group">@lang('To'):</label>
                            <input type="date" class="form-control select-input-date-to" data-column="3">
                            <button class="btn btn-primary btn-reset" type="reset" id="btn-reset">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table cellspacing="0" class="table table-bordered nowrap" id="datatable" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>@lang('Type')</th>
                            <th>@lang('Date')</th>
                            <th>@lang('Name')</th>
                            <th>@lang('Email')</th>
                            <th>@lang("Plate")</th>
                            <th>@lang("Start")</th>
                            <th>@lang("Destination")</th>
                            <th>@lang("Km")</th>
                            <th>@lang("Fuel")</th>
                            <th>@lang("Cost")</th>
                            <th>@lang("Plate Semirimorchio")</th>
                            <th>@lang("Container")</th>
                            <th>@lang("Garage")</th>
                            <th>@lang("Stops")</th>
                            <th>@lang("Truck's km")</th>
                            <th>@lang("Notes")</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- FINESTRE MODAL -->

<!-- AGGIUNGI VIAGGI -->

<div class="modal fade" id="modal-add" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="modal-label-add" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-label-add">@lang('Add trip')</h5>
            </div>
            <div class="modal-body">

                <div id="form-result"></div>

                <div class="form-group">
                    <label>@lang('Seleziona la tipologia di viaggio:')</label>
                    <select class="form-control trip" id="type-add" name="type" required>
                        <option value="0">@lang('Carico/scarico merci')</option>
                        <option value="1">@lang('Officina/gommista')</option>
                        <option value="2">@lang('A vuoto')</option>
                    </select>
                </div>

                <form id="merci">
                    <input type="hidden" name="type" value="0">
                    <div class="form-group">
                        <label for="email">@lang('Email'):</label>
                        <select name="email" class="form-control" required>
                            <option value=""></option>
                            @foreach ($users as $user)
                            <option value="{{ $user->email }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
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
                        <input type="text" class="form-control autocompleteCity" name="start" required>
                    </div>
                    <div class="form-group">
                        <label class="label-stop">@lang('Stops') (@lang("Optional")):</label>
                        <div class="row justify-content-center row-stop">
                            <div class="col-auto">
                                <button type="button" class="btn btn-success btn-license buttonAdd">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-danger btn-license buttonRemove">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="destination">@lang('Destinazione finale'):</label>
                        <input type="text" class="form-control autocompleteCity" name="destination" required>
                    </div>
                    <div class="form-group">
                        <label for="km">@lang('Distance') (Km):</label>
                        <input type="number" class="form-control" name="km" min="1" max="1000" required>
                    </div>
                    <div class="form-group">
                        <label for="fuel">@lang('Fuel'):</label>
                        <input type="number" class="form-control" name="fuel" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="cost">@lang('Fuel cost'):</label>
                        <input type="number" class="form-control" name="cost" min="0" required>
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
                        <label for="container">@lang('Sigla container') (@lang("Optional")):</label>
                        <input type="text" class="form-control" name="container">
                    </div>
                    <div class="form-group">
                        <label for="note">@lang('Note') (@lang("Optional")):</label>
                        <textarea class="form-control" name="note" rows="3" max="200"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-primary submit">
                            <span class="spinner-border spinner-border-sm loader-submit hidden" role="status"
                                aria-hidden="true"></span>
                            @lang('Submit')
                        </button>
                    </div>
                </form>

                <form id="officina">
                    <input type="hidden" name="type" value="1">
                    <div class="form-group">
                        <label for="email">@lang('Email'):</label>
                        <select name="email" class="form-control" required>
                            <option value=""></option>
                            @foreach ($users as $user)
                            <option value="{{ $user->email }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
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
                        <input type="text" class="form-control autocompleteCity" name="start" required>
                    </div>
                    <div class="form-group">
                        <label for="destination">@lang('Destinazione'):</label>
                        <input type="text" class="form-control autocompleteCity" name="destination" required>
                    </div>
                    <div class="form-group">
                        <label for="garage">@lang('Garage'):</label>
                        <input type="text" class="form-control" name="garage" required>
                    </div>
                    <div class="form-group">
                        <label for="km">@lang('Distance') (Km):</label>
                        <input type="number" class="form-control" name="km" min="1" max="1000" required>
                    </div>
                    <div class="form-group">
                        <label for="fuel">@lang('Fuel'):</label>
                        <input type="number" class="form-control" name="fuel" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="cost">@lang('Fuel cost'):</label>
                        <input type="number" class="form-control" name="cost" min="0" required>
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
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-primary submit">
                            <span class="spinner-border spinner-border-sm loader-submit hidden" role="status"
                                aria-hidden="true"></span>
                            @lang('Submit')
                        </button>
                    </div>
                </form>

                <form id="vuoto">
                    <input type="hidden" name="type" value="2">
                    <div class="form-group">
                        <label for="email">@lang('Email'):</label>
                        <select name="email" class="form-control" required>
                            <option value=""></option>
                            @foreach ($users as $user)
                            <option value="{{ $user->email }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
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
                        <input type="text" class="form-control autocompleteCity" name="start" required>
                    </div>
                    <div class="form-group">
                        <label for="destination">@lang('Destinazione'):</label>
                        <input type="text" class="form-control autocompleteCity" name="destination" required>
                    </div>
                    <div class="form-group">
                        <label for="km">@lang('Distance') (Km):</label>
                        <input type="number" class="form-control" name="km" min="1" max="1000" required>
                    </div>
                    <div class="form-group">
                        <label for="fuel">@lang('Fuel'):</label>
                        <input type="number" class="form-control" name="fuel" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="cost">@lang('Fuel cost'):</label>
                        <input type="number" class="form-control" name="cost" min="0" required>
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
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-primary submit">
                            <span class="spinner-border spinner-border-sm loader-submit hidden" role="status"
                                aria-hidden="true"></span>
                            @lang('Submit')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- MODIFICA VIAGGI -->

<div class="modal fade" id="modal-edit" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="modal-label-edit" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-label-edit">@lang('Edit trip')</h5>
            </div>
            <div id="edit-form" class="modal-body">

                <div id="form-result-edit"></div>

                <div class="form-group">
                    <label>@lang('Seleziona la tipologia di viaggio:')</label>
                    <select class="form-control trip" id="type-edit" name="type" required>
                        <option value="0">@lang('Carico/scarico merci')</option>
                        <option value="1">@lang('Officina/gommista')</option>
                        <option value="2">@lang('A vuoto')</option>
                    </select>
                </div>

                <form id="merci-edit">
                    <input type="hidden" name="type" value="0">
                    <input type="hidden" name="id" value="" class="id-edit">
                    <input type="hidden" name="plate" value="" class="plate-edit">
                    <input type="hidden" name="date" value="" class="date-edit">
                    <div class="form-group">
                        <label for="email">@lang('Email'):</label>
                        <select name="email" class="form-control email-edit" required>
                            <option value=""></option>
                            @foreach ($users as $user)
                            <option value="{{ $user->email }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>@lang('Date'):</label>
                        <input type="date" class="form-control date-edit"
                            max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" disabled required>
                    </div>
                    <div class="form-group">
                        <label>@lang("Truck's plate"):</label>
                        <select class="form-control plate-edit" disabled required>
                            <option value=""></option>
                            @foreach ($plates as $plate)
                            <option value="{{ $plate->plate }}">{{ $plate->plate }} (km: {{ $plate->km }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="start">@lang('Città di partenza'):</label>
                        <input type="text" class="form-control autocompleteCity start-edit" name="start" required>
                    </div>
                    <div class="form-group">
                        <label class="label-stop">@lang('Stops') (@lang("Optional")):</label>
                        <div class="row justify-content-center row-stop row-stop-edit">
                            <div class="col-auto">
                                <button type="button" class="btn btn-success btn-license buttonAdd">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-danger btn-license buttonRemove">
                                    <i class="fas fa-minus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="destination">@lang('Destinazione finale'):</label>
                        <input type="text" class="form-control autocompleteCity destination-edit" name="destination"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="km">@lang('Distance') (Km):</label>
                        <input type="number" class="form-control km-edit" name="km" min="1" max="1000" required>
                    </div>
                    <div class="form-group">
                        <label for="fuel">@lang('Fuel'):</label>
                        <input type="number" class="form-control fuel-edit" name="fuel" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="cost">@lang('Fuel cost'):</label>
                        <input type="number" class="form-control cost-edit" name="cost" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="plate_s">@lang("Targa semirimorchio") (@lang("Optional")):</label>
                        <select name="plate_s" class="plate_s-edit form-control">
                            <option value=""></option>
                            @foreach ($plates_semi as $plate)
                            <option value="{{ $plate->plate }}">{{ $plate->plate }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="container">@lang('Sigla container') (@lang("Optional")):</label>
                        <input type="text" class="form-control container-edit" name="container">
                    </div>
                    <div class="form-group">
                        <label for="note">@lang('Note') (@lang("Optional")):</label>
                        <textarea class="form-control note-edit" name="note" rows="3" max="200"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-edit-close"
                            data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-primary submit">
                            <span class="spinner-border spinner-border-sm loader-submit hidden" role="status"
                                aria-hidden="true"></span>
                            @lang('Submit')
                        </button>
                    </div>
                </form>

                <form id="officina-edit" class="hidden">
                    <input type="hidden" name="type" value="1">
                    <input type="hidden" name="id" value="" class="id-edit">
                    <input type="hidden" name="plate" value="" class="plate-edit">
                    <input type="hidden" name="date" value="" class="date-edit">
                    <div class="form-group">
                        <label for="email">@lang('Email'):</label>
                        <select name="email" class="form-control email-edit" required>
                            <option value=""></option>
                            @foreach ($users as $user)
                            <option value="{{ $user->email }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>@lang('Date'):</label>
                        <input type="date" class="form-control date-edit"
                            max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" disabled required>
                    </div>
                    <div class="form-group">
                        <label>@lang("Truck's plate"):</label>
                        <select class="form-control plate-edit" disabled required>
                            <option value=""></option>
                            @foreach ($plates as $plate)
                            <option value="{{ $plate->plate }}">{{ $plate->plate }} (km: {{ $plate->km }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="start">@lang('Città di partenza'):</label>
                        <input type="text" class="form-control autocompleteCity start-edit" name="start" required>
                    </div>
                    <div class="form-group">
                        <label for="destination">@lang('Destinazione'):</label>
                        <input type="text" class="form-control autocompleteCity destination-edit" name="destination"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="garage">@lang('Garage'):</label>
                        <input type="text" class="form-control garage-edit" name="garage" required>
                    </div>
                    <div class="form-group">
                        <label for="km">@lang('Distance') (Km):</label>
                        <input type="number" class="form-control km-edit" name="km" min="1" max="1000" required>
                    </div>
                    <div class="form-group">
                        <label for="fuel">@lang('Fuel'):</label>
                        <input type="number" class="form-control fuel-edit" name="fuel" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="cost">@lang('Fuel cost'):</label>
                        <input type="number" class="form-control cost-edit" name="cost" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="plate_s">@lang("Targa semirimorchio") (@lang("Optional")):</label>
                        <select name="plate_s" class="plate_s-edit form-control">
                            <option value=""></option>
                            @foreach ($plates_semi as $plate)
                            <option value="{{ $plate->plate }}">{{ $plate->plate }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="container">@lang('Note') (@lang("Optional")):</label>
                        <textarea class="form-control note-edit" name="note" rows="3"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-edit-close"
                            data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-primary submit">
                            <span class="spinner-border spinner-border-sm loader-submit hidden" role="status"
                                aria-hidden="true"></span>
                            @lang('Submit')
                        </button>
                    </div>
                </form>

                <form id="vuoto-edit" class="hidden">
                    <input type="hidden" name="type" value="2">
                    <input type="hidden" name="id" value="" class="id-edit">
                    <input type="hidden" name="plate" value="" class="plate-edit">
                    <input type="hidden" name="date" value="" class="date-edit">
                    <div class="form-group">
                        <label for="email">@lang('Email'):</label>
                        <select name="email" class="form-control email-edit" required>
                            <option value=""></option>
                            @foreach ($users as $user)
                            <option value="{{ $user->email }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label>@lang('Date'):</label>
                        <input type="date" class="form-control date-edit"
                            max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" disabled required>
                    </div>
                    <div class="form-group">
                        <label>@lang("Truck's plate"):</label>
                        <select class="form-control plate-edit" disabled required>
                            <option value=""></option>
                            @foreach ($plates as $plate)
                            <option value="{{ $plate->plate }}">{{ $plate->plate }} (km: {{ $plate->km }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="start">@lang('Città di partenza'):</label>
                        <input type="text" class="form-control autocompleteCity start-edit" name="start" required>
                    </div>
                    <div class="form-group">
                        <label for="destination">@lang('Destinazione'):</label>
                        <input type="text" class="form-control autocompleteCity destination-edit" name="destination"
                            required>
                    </div>
                    <div class="form-group">
                        <label for="km">@lang('Distance') (Km):</label>
                        <input type="number" class="form-control km-edit" name="km" min="1" max="1000" required>
                    </div>
                    <div class="form-group">
                        <label for="fuel">@lang('Fuel'):</label>
                        <input type="number" class="form-control fuel-edit" name="fuel" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="cost">@lang('Fuel cost'):</label>
                        <input type="number" class="form-control cost-edit" name="cost" min="0" required>
                    </div>
                    <div class="form-group">
                        <label for="plate_s">@lang("Targa semirimorchio") (@lang("Optional")):</label>
                        <select name="plate_s" class="plate_s-edit form-control">
                            <option value=""></option>
                            @foreach ($plates_semi as $plate)
                            <option value="{{ $plate->plate }}">{{ $plate->plate }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="container">@lang('Note') (@lang("Optional")):</label>
                        <textarea class="form-control note-edit" name="note" rows="3"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-edit-close"
                            data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-primary submit">
                            <span class="spinner-border spinner-border-sm loader-submit hidden" role="status"
                                aria-hidden="true"></span>
                            @lang('Submit')
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"
    defer></script>

<script type="text/javascript" language="javascript"
    src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js" defer></script>

<script type="text/javascript" language="javascript"
    src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js" defer></script>
<script type="text/javascript" language="javascript"
    src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.bootstrap4.min.js" defer></script>

<script type="text/javascript" language="javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" defer></script>
<script type="text/javascript" language="javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" defer></script>
<script type="text/javascript" language="javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" defer></script>
<script type="text/javascript" language="javascript"
    src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js" defer></script>
<script type="text/javascript"
    src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js" defer></script>

<script src="{{asset('jqueryui/jquery-ui.min.js')}}" type="text/javascript" defer></script>

@switch(App::getLocale())
@case('it')
<script>
    var language = "{{ asset('datatable_languages/it.json') }}";
</script>
@break
@case('en')
<script>
    var language = "{{ asset('datatable_languages/en.json') }}";
</script>
@break
@default
<script>
    var language = "{{ asset('datatable_languages/en.json') }}";
</script>
@endswitch

<script>
    // FIX SIMBOLI IN JS

    function replaceSymbol(string) {
        return string.replace("&#039;", "'");
    }

    // FIX SIMBOLI IN JS

    function formatInternational(date) {
        var dateAr = date.split('-');
        var newDate = dateAr[2] + '-' + dateAr[1] + '-' + dateAr[0];
        return newDate;
    }

    function formatTripData(plate, container, garage, stops, km, note) {
        if (plate == null) {
            plate = '';
        }
        if (container == null) {
            container = '';
        }
        if (garage == null) {
            garage = '';
        }
        if (stops == null) {
            stops = '';
        }
        if (note == null) {
            note = '';
        }

        var html = '<table class="table table-sm table-borderless table-exp"><thead>' +
            '<tr> <th scope = "col" > @lang("Targa semirimorchio")' +
            '</th> <th scope = "col" > @lang("Container")' +
            '</th> <th scope = "col" > @lang("Garage")' +
            '</th> <th scope = "col" > @lang("Stops")' +
            `</th> <th scope = "col" > @lang("Truck's Km") ` +
            '</th> <th scope = "col" > @lang("Notes") </th> </thead>' +
            '<tbody>' +
            '<tr>' + '<td>' + plate + '</td>' +
            '<td>' + container + '</td>' +
            '<td>' + garage + '</td>' +
            '<td>' + stops + '</td>' +
            '<td>' + km + '</td>' +
            '<td>' + note + '</td>' + '</tr>';

        html += '</tbody></table>';
        return html;
    }

    $(document).ready(function() {
        // SCELTA DELLA TIPOLOGIA DI VIAGGIO

        $('#type-add').on('change', function() {
            switch ($(this).val()) {
                case '0':
                    $('#officina').slideUp();
                    $('#vuoto').slideUp();
                    $('#merci').slideDown();
                    break;

                case '1':
                    $('#merci').slideUp();
                    $('#vuoto').slideUp();
                    $('#officina').slideDown();
                    break;

                case '2':
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

        $('#type-edit').on('change', function() {
            switch ($(this).val()) {
                case '0':
                    $('#officina-edit').slideUp();
                    $('#vuoto-edit').slideUp();
                    $('#merci-edit').slideDown();
                    break;

                case '1':
                    $('#merci-edit').slideUp();
                    $('#vuoto-edit').slideUp();
                    $('#officina-edit').slideDown();
                    break;

                case '2':
                    $('#officina-edit').slideUp();
                    $('#merci-edit').slideUp();
                    $('#vuoto-edit').slideDown();
                    break;

                default:
                    $('#officina-edit').slideUp();
                    $('#merci-edit').slideUp();
                    $('#vuoto-edit').slideUp();
                    break;
            }
        });

        // GESTIONE TAPPE

        $('.buttonAdd').on('click', function(event) {
            var lastInput = $(this).closest('.row-stop').prev();
            var id;
            var idNew = [];
            if (lastInput.hasClass('label-stop')) {
                id = 1;
            } else {
                idNew = lastInput.attr('id').split("_");
                idNew[1] = parseInt(idNew[1]);
                id = idNew[1] + 1;
            }

            var html = '<input type="text" class="form-control autocompleteCity input-stop" id="stop_' + id + '" name="stop_' + id + '" required>';

            $(this).closest('.row-stop').before(html);
        });

        $('.buttonRemove').on('click', function(event) {
            var lastInput = $(this).closest('.row-stop').prev();

            if (!(lastInput.hasClass('label-stop'))) {
                lastInput.remove();
            }
        });

        // AUTOCOMPLETAMENTO CITTA'

        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $(document).on('keydown.autocompleteCity', '.autocompleteCity', function() {
            $(this).autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: "{{route('autocomplete.city')}}",
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
                minLength: 2,
                appendTo: $(this).closest("form")
            });
        });

        // DATATABLES

        var table = $('#datatable').DataTable({
            "dom": '<"row justify-content-between table-row"<"col-sm table-col"lB><"col-sm-auto"f>>rtip',
            "order": [
                [3, "desc"]
            ],
            buttons: {
                buttons: [{
                        extend: 'excelHtml5',
                        className: 'btn btn-success',
                        exportOptions: {
                            columns: [2, 3, 4, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16]
                        },
                        text: '@lang("Export EXCEL")'
                    },
                    {
                        extend: 'pdfHtml5',
                        className: 'btn btn-danger',
                        exportOptions: {
                            columns: [2, 3, 4, 6, 7, 8, 9, 10, 11]
                        },
                        text: '@lang("Export PDF")',
                        orientation: 'landscape',
                        customize: function(doc) {
                            doc.content[1].table.widths =
                                Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                        }, //LA FUNZIONE SERVE PER AVERE IL PDF FULL WIDTH
                    },
                ]
            },
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "@lang('All')"]
            ],
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('api.trips') }}",
            "columns": [{
                    data: 'id',
                    name: 'id'
                },
                {
                    "className": 'details-control',
                    "orderable": false,
                    "defaultContent": '',
                    "render": function(data, type, row) {
                        var html = "";

                        //if (row.plate_s != null || row.garage != null || row.container != null || row.stops != null) {
                            html = '<button id="btn-details" type="button" class="btn btn-sm btn-success">' +
                                '<i class="fas fa-plus"></i>' +
                                '</button>';
                        //}

                        return html;
                    },
                },
                {
                    "data": 'type',
                    "render": function(data, type, row) {
                        var html = "";

                        switch (data) {
                            case 0:
                                html = "@lang('Carico/scarico merci')"
                                break;
                            case 1:
                                html = "@lang('Officina/gommista')"
                                break;
                            case 2:
                                html = "@lang('A vuoto')"
                                break;
                            default:
                                break;
                        }

                        return html;
                    },
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'user_email',
                    name: 'user_email'
                },
                {
                    data: 'plate',
                    name: 'plate'
                },
                {
                    data: 'start',
                    name: 'start'
                },
                {
                    data: 'destination',
                    name: 'destination'
                },
                {
                    data: 'distance',
                    name: 'distance'
                },
                {
                    data: 'fuel',
                    name: 'fuel'
                },
                {
                    data: 'cost',
                    name: 'cost'
                },
                {
                    data: 'plate_s',
                    name: 'plate_s'
                },
                {
                    data: 'container',
                    name: 'container'
                },
                {
                    data: 'garage',
                    name: 'garage'
                },
                {
                    data: 'stops',
                    name: 'stops'
                },
                {
                    data: 'km',
                    name: 'km'
                },
                {
                    data: 'note',
                    name: 'note'
                },
            ],
            "columnDefs": [{
                    'targets': 0,
                    'checkboxes': {
                        'selectRow': true
                    },
                    'width': '1%'
                },
                {
                    'targets': 1,
                    "orderable": false,
                    "searchable": false,
                    'width': '1%'
                },
                {
                    'targets': [2, 9, 10, 11],
                    "orderable": false,
                    "searchable": false
                },
                {
                    'targets': [15],
                    'searchable': true,
                    'visible': false
                },
                {
                    'targets': [12, 13, 14, 16, 17],
                    "searchable": false,
                    'visible': false
                },
            ],
            'select': {
                'style': 'multi'
            },
            "language": {
                "url": language,
            },
            "responsive": true,
        });

        $('#datatable tbody').on('click', '#btn-details', function() {
            var tr = $(this).closest('tr');
            var row = table.row(tr);

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
                $(this).find('.fas').removeClass('fa-minus');
                $(this).find('.fas').addClass('fa-plus');
                $(this).removeClass('btn-danger');
                $(this).addClass('btn-success');
            } else {
                // Open this row
                rowData = row.data();
                row.child(formatTripData(rowData.plate_s, rowData.container, rowData.garage, rowData.stops, rowData.km, rowData.note)).show();
                tr.addClass('shown');
                $(this).find('.fas').removeClass('fa-plus');
                $(this).find('.fas').addClass('fa-minus');
                $(this).removeClass('btn-success');
                $(this).addClass('btn-danger');
            }
        });

        $('#datatable').on('draw.dt', function() {
            table.column(0).checkboxes.deselectAll();
            $('#btn-delete').prop('disabled', true);
            $('#btn-edit').prop('disabled', true);
        }); // DESELEZIONA LE CHECKBOX E I BUTTONS EDIT E DELETE

        $('#datatable').change(function() {
            switch ((table.column(0).checkboxes.selected().count())) {
                case 0:
                    $('#btn-delete').prop('disabled', true);
                    $('#btn-edit').prop('disabled', true);
                    break;
                case 1:
                    $('#btn-delete').prop('disabled', false);
                    $('#btn-edit').prop('disabled', false);
                    break;
                default:
                    $('#btn-delete').prop('disabled', false);
                    $('#btn-edit').prop('disabled', true);
                    break;
            }
        }); // DESELEZIONA I BUTTONS EDIT E DELETE

        $('.select-input-date-to').change(function() {
            var url = "{{ route('api.trips') }}";

            if ($(this).val() == '') {
                var date = 'nullValue';
            } else {
                var date = $(this).val();
            }
            url += "/" + date + "/" + $('.select-input-date-from').val();

            table.ajax.url(url).load();

            $('.select-input-date-from').attr({
                "max": $(this).val()
            })
        }); // FILTRO PER DATA

        $('.select-input-date-from').change(function() {
            var url = "{{ route('api.trips') }}";

            if ($('.select-input-date-to').val() == '') {
                var date = 'nullValue';
            } else {
                var date = $('.select-input-date-to').val();
            }
            url += "/" + date + "/" + $(this).val();

            table.ajax.url(url).load();

            $('.select-input-date-to').attr({
                "min": $(this).val()
            })
        }); // FILTRO PER DATA

        $('#btn-reset').click(function(e) {
            table.ajax.url("{{ route('api.trips') }}").load();

            $('.select-input-date-from').attr({
                "max": ""
            })
            $('.select-input-date-to').attr({
                "min": ""
            })
        }) //RESET DELLE DATE

        // SUBMIT FORMS

        // CARICO SCARICO MERCI

        $('#merci').on('submit', function(event) {
            event.preventDefault();
            var form = $(this).closest('form');

            $('#form-result').text('');
            $('#form-result').fadeIn();

            $.ajax({
                url: "{{route('api.trips.store')}}",
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
                        $('#merci')[0].reset();
                        var stops = $(this).find('.input-stop');
                        stops.remove();
                        $('#datatable').DataTable().ajax.reload();
                    }
                    $('#modal-add').scrollTop(0);
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
                url: "{{route('api.trips.store')}}",
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
                        $('#datatable').DataTable().ajax.reload();
                    }
                    $('#modal-add').scrollTop(0);
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
                url: "{{route('api.trips.store')}}",
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
                        $('#datatable').DataTable().ajax.reload();
                    }
                    $('#modal-add').scrollTop(0);
                    $('#form-result').html(html);
                    $('#form-result').delay(4000).fadeOut();
                },
                complete: function() {
                    $('.loader-submit').addClass('hidden');
                    $('.submit-vuoto').contents().last().replaceWith('@lang("Submit")');
                },
            });
        });

        // EDIT

        $('#btn-edit').on('click', function(e) {

            var rows_selected = table.column(0).checkboxes.selected();

            if (rows_selected.length == 1) {
                var row = table.row('#' + rows_selected[0]).data();

                $('#type-edit').val(row['type']);

                switch (row['type']) {
                    case 0:
                        $('#officina-edit').css('display', 'none');
                        $('#vuoto-edit').css('display', 'none');
                        $('#merci-edit').css('display', 'block');
                        break;

                    case 1:
                        $('#merci-edit').css('display', 'none');
                        $('#vuoto-edit').css('display', 'none');
                        $('#officina-edit').css('display', 'block');
                        break;

                    case 2:
                        $('#officina-edit').css('display', 'none');
                        $('#merci-edit').css('display', 'none');
                        $('#vuoto-edit').css('display', 'block');
                        break;

                    default:
                        $('#officina-edit').css('display', 'none');
                        $('#merci-edit').css('display', 'none');
                        $('#vuoto-edit').css('display', 'none');
                        break;
                }

                $('#edit-form').find('.id-edit').val(row['id']);
                $('#edit-form').find('.email-edit').val(row['user_email']);
                $('#edit-form').find('.date-edit').val(formatInternational(row['date']));
                $('#edit-form').find('.start-edit').val(replaceSymbol(row['start']));
                $('#edit-form').find('.destination-edit').val(replaceSymbol(row['destination']));
                $('#edit-form').find('.distance-edit').val(row['distance']);
                $('#edit-form').find('.fuel-edit').val(row['fuel']);
                $('#edit-form').find('.garage-edit').val(row['garage']);
                $('#edit-form').find('.km-edit').val(row['distance']);
                $('#edit-form').find('.note-edit').val(row['note']);
                $('#edit-form').find('.plate-edit').val(row['plate']);
                $('#edit-form').find('.cost-edit').val(row['cost']);
                $('#edit-form').find('.container-edit').val(row['container']);
                $('#edit-form').find('.plate_s-edit').val(row['plate_s']);

                // CREO GLI STOPS
                if (row['stops'] != '') {
                    var stops = row['stops'].replace(/\s+/g, '');
                    stops = stops.split(",");

                    stops.forEach(function(stop, i) {
                        var id = i + 1;

                        var html = '<input type="text" class="form-control autocompleteCity input-stop" id="stop_' + id + '" name="stop_' + id + '" required>';

                        $('.row-stop-edit').before(html);

                        $('#stop_' + id).val(replaceSymbol(stop));
                    });
                }
            }
        });

        // CARICO SCARICO MERCI

        $('#merci-edit').on('submit', function(event) {
            event.preventDefault();
            var form = $(this).closest('form');

            $('#form-result-edit').text('');
            $('#form-result-edit').fadeIn();

            $.ajax({
                url: "{{route('api.trips.edit')}}",
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
                        $('#merci-edit')[0].reset();
                        var stops = $(this).find('.input-stop');
                        stops.remove();
                        $('#datatable').DataTable().ajax.reload();
                    }
                    $('#modal-edit').scrollTop(0);
                    $('#form-result-edit').html(html);
                    $('#form-result-edit').delay(4000).fadeOut();
                },
                complete: function() {
                    $('.loader-submit').addClass('hidden');
                    $('.submit-merci').contents().last().replaceWith('@lang("Submit")');
                },
            });
        });

        // OFFICINA

        $('#officina-edit').on('submit', function(event) {
            event.preventDefault();
            var form = $(this).closest('form');

            $('#form-result-edit').text('');
            $('#form-result-edit').fadeIn();

            $.ajax({
                url: "{{route('api.trips.edit')}}",
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
                        $('#officina-edit')[0].reset();
                        $('#datatable').DataTable().ajax.reload();
                    }
                    $('#modal-edit').scrollTop(0);
                    $('#form-result-edit').html(html);
                    $('#form-result-edit').delay(4000).fadeOut();
                },
                complete: function() {
                    $('.loader-submit').addClass('hidden');
                    $('.submit-officina').contents().last().replaceWith('@lang("Submit")');
                },
            });
        });

        // A VUOTO

        $('#vuoto-edit').on('submit', function(event) {
            event.preventDefault();
            var form = $(this).closest('form');

            $('#form-result-edit').text('');
            $('#form-result-edit').fadeIn();

            $.ajax({
                url: "{{route('api.trips.edit')}}",
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
                        $('#vuoto-edit')[0].reset();
                        $('#datatable').DataTable().ajax.reload();
                    }
                    $('#modal-edit').scrollTop(0);
                    $('#form-result-edit').html(html);
                    $('#form-result-edit').delay(4000).fadeOut();
                },
                complete: function() {
                    $('.loader-submit').addClass('hidden');
                    $('.submit-vuoto').contents().last().replaceWith('@lang("Submit")');
                },
            });
        });

        $('.btn-edit-close').on('click', function(e){
            var form = $(this).parents('form');
            form[0].reset();

            var stops = form.find('.input-stop');
            stops.remove();
        });

        // DELETE

        $('#btn-delete').on('click', function(e) {

            var rows_selected = table.column(0).checkboxes.selected();
            var id = [];

            $.each(rows_selected, function(index, rowId) {
                id[index] = rowId;
            });

            $('#message-success').text('');
            $('#message-success').show();

            $.ajax({
                url: '{{ route("api.trips.delete") }}',
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    trips: id
                },
                success: function(data) {
                    var html = '';
                    $('#datatable').DataTable().ajax.reload();
                    $('html, body').animate({
                        scrollTop: 0
                    }, 'fast');
                    html = '<div class="alert alert-success">' + data.success + '</div>';
                    $('#message-success').html(html);
                    $('#message-success').delay(4000).fadeOut();
                }
            });

        });

    });
</script>

@endsection

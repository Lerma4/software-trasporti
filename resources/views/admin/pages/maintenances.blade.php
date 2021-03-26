@extends('multiauth::layouts.admin')

@section('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.bootstrap4.min.css">
<link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css"
    rel="stylesheet" />
@endsection

@section('content')
<div class="col-12 pages-content">
    <div class="card">
        <h5 class="card-header">@lang("Maintenance already done")</h5>

        <!-- MANUTENZIONE GIà FATTA -->

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
                            <button class="btn btn-primary btn-reset" type="reset"
                                id="btn-reset">@lang("Reset")</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table cellspacing="0" class="table table-bordered nowrap" id="maint_already" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>@lang('Date')</th>
                            <th>@lang('Plate')</th>
                            <th>@lang('Type')</th>
                            <th>@lang("Vehicle's km")</th>
                            <th>@lang('Garage')</th>
                            <th>@lang('Price')</th>
                            <th>@lang('Notes')</th>
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

    <!-- MANUTENZIONE ANCORA DA FARE -->

    <div class="card">
        <h5 class="card-header">@lang("Maintenance still to do")</h5>
        <div class="card-body">
            <div id="message-success-stillToDo"></div>
            <div class="row justify-content-between page-row">
                <div class="col-sm">
                    <button id="btn-add-stillToDo" type="button" class="btn btn-primary" data-toggle="modal"
                        data-target="#modal-add-stillToDo">
                        @lang('New')
                    </button>
                    <button id="btn-edit-stillToDo" type="button" class="btn btn-secondary" data-toggle="modal"
                        data-target="#modal-edit-stillToDo" disabled>
                        @lang('Edit')
                    </button>
                    <button type="button" class="btn btn-danger" id="btn-delete-stillToDo" disabled>
                        @lang('Delete')
                    </button>
                </div>
            </div>
            <div class="table-responsive">
                <table cellspacing="0" class="table table-bordered nowrap" id="maint_still" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>@lang("Km mancanti")</th>
                            <th>@lang('Plate')</th>
                            <th>@lang('Type')</th>
                            <th>@lang('Renew')</th>
                            <th>@lang('Notes')</th>
                            <th>@lang('Done')</th>
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
</div> <!-- FINE CARD -->

<!-- FINESTRE MODALI MANUTENZIONE GIà EFFETTUATA -->

<div class="modal fade" id="modal-add" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="modal-label-add" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-label-add">@lang('Add maintenance')</h5>
            </div>
            <div class="modal-body">
                <form id="addMaint">
                    <div class="form-group">
                        <label for="date">@lang('Date'):</label>
                        <input type="date" class="form-control" name="date"
                            max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="plate">@lang('Plate'):</label>
                        <select name="plate" class="custom-select" required>
                            <option value=""></option>
                            @foreach ($trucks as $plate)
                            <option value="{{ $plate->plate }}">{{ $plate->plate }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="type">@lang('Type'):</label>
                        <input type="text" class="form-control" name="type" required>
                        <small>@lang('Ad es. : sostituzione pastiglie, tagliando ecc..')</small>
                    </div>
                    <div class="form-group">
                        <label for="km">@lang("Truck's km") (@lang('Optional')):</label>
                        <input type="number" step="1" min="1" class="form-control" name="km">
                        <small>@lang('Km del mezzo al momento della manutenzione.')</small>
                    </div>
                    <div class="form-group">
                        <label for="garage">@lang('Garage') (@lang('Optional')):</label>
                        <input type="text" class="form-control" name="garage">
                    </div>
                    <div class="form-group">
                        <label for="price">@lang('Price') (@lang('Optional')):</label>
                        <input type="number" class="form-control" min="1" step="0.01" name="price">
                    </div>
                    <div class="form-group">
                        <label for="notes">@lang('Notes') (@lang('Optional')):</label>
                        <input type="text" class="form-control" maxlength="50" name="notes">
                        <small>@lang('Max 50 characters').</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-close"
                            data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-primary submit">
                            <span class="spinner-border spinner-border-sm loader-submit hidden" role="status"
                                aria-hidden="true"></span>
                            @lang('Submit')
                        </button>
                    </div>
                    <div class="form-group form-result"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-edit" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="modal-label-edit" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-label-edit">@lang('Edit maintenance')</h5>
            </div>
            <div class="modal-body">
                <form id="editMaint">
                    <input type="hidden" name="id" class="maint-id">
                    <div class="form-group">
                        <label for="date">@lang('Date'):</label>
                        <input type="date" class="form-control maint-date" name="date"
                            max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="plate">@lang('Plate'):</label>
                        <select name="plate" class="custom-select maint-plate" required>
                            <option value=""></option>
                            @foreach ($trucks as $plate)
                            <option value="{{ $plate->plate }}">{{ $plate->plate }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="type">@lang('Type'):</label>
                        <input type="text" class="form-control maint-type" name="type" required>
                        <small>@lang('Ad es. : sostituzione pastiglie, tagliando ecc..')</small>
                    </div>
                    <div class="form-group">
                        <label for="km">@lang("Truck's km") (@lang('Optional')):</label>
                        <input type="number" step="1" min="1" class="form-control maint-km" name="km">
                        <small>@lang('Km del mezzo al momento della manutenzione.')</small>
                    </div>
                    <div class="form-group">
                        <label for="garage">@lang('Garage') (@lang('Optional')):</label>
                        <input type="text" class="form-control maint-garage" name="garage">
                    </div>
                    <div class="form-group">
                        <label for="price">@lang('Price') (@lang('Optional')):</label>
                        <input type="number" class="form-control maint-price" min="1" step="0.01" name="price">
                    </div>
                    <div class="form-group">
                        <label for="notes">@lang('Notes') (@lang('Optional')):</label>
                        <input type="text" class="form-control maint-notes" maxlength="50" name="notes">
                        <small>@lang('Max 50 characters').</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-close"
                            data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-primary submit">
                            <span class="spinner-border spinner-border-sm loader-submit hidden" role="status"
                                aria-hidden="true"></span>
                            @lang('Submit')
                        </button>
                    </div>
                    <div class="form-group form-result"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- FINESTRE MODALI MANUTENZIONE ANCORA DA FARE -->

<div class="modal fade" id="modal-add-stillToDo" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="modal-label-add-stillToDo" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-label-add-stillToDo">@lang('Add maintenance')</h5>
            </div>
            <div class="modal-body">
                <form id="addMaint-stillToDo">
                    <div class="form-group">
                        <label for="plate">@lang('Plate'):</label>
                        <select name="plate" class="custom-select" required>
                            <option value=""></option>
                            @foreach ($trucks as $plate)
                            <option value="{{ $plate->plate }}">{{ $plate->plate }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="type">@lang('Type'):</label>
                        <input type="text" class="form-control" name="type" required>
                        <small>@lang('Ad es. : sostituzione pastiglie, tagliando ecc..')</small>
                    </div>
                    <div class="form-group">
                        <label for="km">@lang("Km mancanti"):</label>
                        <input type="number" step="1" min="1" max="1000000" class="form-control" name="km" required>
                        <small>@lang('Km mancanti alla prossima manutenzione (si riceverà una notifica 1000 km prima del
                            raggiungimento dei km).')</small>
                    </div>
                    <div class="form-group">
                        <label for="renew">@lang('Km rinnovo automatico') (@lang('Optional')):</label>
                        <input type="text" class="form-control" step="1" min="1" name="renew">
                        <small>@lang("Indicare in questo campo il numero di km necessari tra una manutenzione e
                            l'altra (che verrà automaticamente creata nel momento in cui la manutenzione precedente sarà
                            svolta).")</small>
                    </div>
                    <div class="form-group">
                        <label for="notes">@lang('Notes') (@lang('Optional')):</label>
                        <input type="text" class="form-control" maxlength="50" name="notes">
                        <small>@lang('Max 50 characters').</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-close"
                            data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-primary submit">
                            <span class="spinner-border spinner-border-sm loader-submit hidden" role="status"
                                aria-hidden="true"></span>
                            @lang('Submit')
                        </button>
                    </div>
                    <div class="form-group form-result"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-edit-stillToDo" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="modal-label-edit-stillToDo" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-label-edit-stillToDo">@lang('Edit maintenance')</h5>
            </div>
            <div class="modal-body">
                <form id="editMaint-stillToDo">
                    <input type="hidden" name="id" class="maint-id">
                    <div class="form-group">
                        <label for="plate">@lang('Plate'):</label>
                        <select name="plate" class="custom-select maint-plate" required>
                            <option value=""></option>
                            @foreach ($trucks as $plate)
                            <option value="{{ $plate->plate }}">{{ $plate->plate }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="type">@lang('Type'):</label>
                        <input type="text" class="form-control maint-type" name="type" required>
                        <small>@lang('Ad es. : sostituzione pastiglie, tagliando ecc..')</small>
                    </div>
                    <div class="form-group">
                        <label for="km">@lang("Km mancanti"):</label>
                        <input type="number" step="1" min="1" max="1000000" class="form-control maint-km" name="km"
                            required>
                        <small>@lang('Km mancanti alla prossima manutenzione (si riceverà una notifica 1000 km prima del
                            raggiungimento dei km).')</small>
                    </div>
                    <div class="form-group">
                        <label for="renew">@lang('Km rinnovo automatico') (@lang('Optional')):</label>
                        <input type="text" class="form-control maint-renew" step="1" min="1" name="renew">
                        <small>@lang("Indicare in questo campo il numero di km necessari tra una manutenzione e
                            l'altra (che verrà automaticamente creata nel momento in cui la manutenzione precedente sarà
                            svolta).")</small>
                        <br>
                        <small class="small-alert">
                            @lang("Per disattivare il rinnovo automatico è sufficiente cancellare il campo qui sopra.")
                        </small>
                    </div>
                    <div class="form-group">
                        <label for="notes">@lang('Notes') (@lang('Optional')):</label>
                        <input type="text" class="form-control maint-notes" maxlength="50" name="notes">
                        <small>@lang('Max 50 characters').</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-close"
                            data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-primary submit">
                            <span class="spinner-border spinner-border-sm loader-submit hidden" role="status"
                                aria-hidden="true"></span>
                            @lang('Submit')
                        </button>
                    </div>
                    <div class="form-group form-result"></div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-maintDone" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="modal-label-maintDone" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-label-maintDone">@lang('Maintenace done')</h5>
            </div>
            <div class="modal-body">
                <div id="confirm-error"></div>
                <form id="maintDone">
                    <input type="hidden" class="maintDone-id" name="id">
                    <div class="form-group">
                        <label for="date">@lang('Date'):</label>
                        <input type="date" class="form-control" name="date"
                            max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="km">@lang("Truck's km"):</label>
                        <input type="number" step="1" min="0" class="form-control" id="confirm-km" name="km" required>
                        <small>@lang('Km del mezzo al momento della manutenzione.')</small>
                        <br>
                        <small class="small-alert">@lang('Di default vengono inseriti i km del mezzo in questo
                            momento.')</small>
                    </div>
                    <div class="form-group">
                        <label for="garage">@lang('Garage') (@lang('Optional')):</label>
                        <input type="text" class="form-control" name="garage">
                    </div>
                    <div class="form-group">
                        <label for="price">@lang('Price') (@lang('Optional')):</label>
                        <input type="number" class="form-control" min="1" step="0.01" name="price">
                    </div>
                    <div class="form-group">
                        <label for="notes">@lang('Notes') (@lang('Optional')):</label>
                        <input type="text" class="form-control" maxlength="50" name="notes">
                        <small>@lang('Max 50 characters').</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-close-confirm"
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
    src="https://cdn.datatables.net/buttons/1.6.4/js/dataTables.buttons.min.js" defer></script>
<script type="text/javascript" language="javascript"
    src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.bootstrap4.min.js" defer></script>

<script type="text/javascript" language="javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" defer></script>
<script type="text/javascript" language="javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" defer></script>
<script type="text/javascript" language="javascript"
    src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" defer></script>
<script type="text/javascript" language="javascript"
    src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.html5.min.js" defer></script>
<script type="text/javascript"
    src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js" defer></script>

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

<!-- SALVO IN UNA VARIABILE PER JS I KM DEI MEZZI (MI SERVE NELLA CONFERMA DELLE MANUTENZIONI) -->

<script>
    var plates = [];
</script>
@foreach ($trucks as $truck)
<script>
    plates.push({ plate:"{{ $truck->plate }}", km:{{ $truck->km }} });
</script>
@endforeach

<!-- FINE -->

<script>
    function formatData(date) {
        var dateAr = date.split('-');
        var newDate = dateAr[2] + '-' + dateAr[1] + '-' + dateAr[0];
        return newDate;
    }

    function formatInternational(date) {
        var dateAr = date.split('-');
        var newDate = dateAr[2] + '-' + dateAr[1] + '-' + dateAr[0];
        return newDate;
    }
</script>

@include('admin.pages.js.maint-alreadyDone')
@include('admin.pages.js.maint-stillDone')

@endsection

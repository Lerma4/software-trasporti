@extends('multiauth::layouts.admin')

@section('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.bootstrap4.min.css">
<link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css" rel="stylesheet" />
@endsection

@section('content')
<div class="col-12 pages-content">
    <div class="card">
        <div class="card-body">
            @include('multiauth::message')
            <form method="post" enctype="multipart/form-data" action="">
                @csrf
                <div class="form-group">
                    <table class="table">
                        <tr>
                            <td width="40%" align="right"><label>@lang('Select File for Upload (limit of 1000 records, formats XLS and XLSX)') DA AGGIUNGERE REGOLE PASSWORD</label></td>
                            <td width="30">
                                <input type="file" name="import_file" required />
                            </td>
                            <td width="30%" align="left">
                                <input type="submit" name="upload" class="btn btn-primary" value="Upload">
                            </td>
                        </tr>
                        <tr>
                            <td width="40%" align="right"></td>
                            <td width="30"><span class="text-muted">.xls, .xslx</span></td>
                            <td width="30%" align="left"></td>
                        </tr>
                    </table>
                </div>
            </form>
            <div class="row justify-content-between page-row">
                <div class="col-sm">
                    <button id="btn-add" type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal-add">
                        @lang('New')
                    </button>
                    <button id="btn-edit" type="button" class="btn btn-secondary" data-toggle="modal" data-target="#modal-add" disabled>
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
                            <button class="btn btn-primary" type="reset" id="btn-reset">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table cellspacing="0" class="table table-bordered nowrap" id="datatable" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>@lang('Plate')</th>
                            <th>@lang('Type')</th>
                            <th>@lang('Date')</th>
                            <th>@lang('Period')</th>
                            <th>@lang("Vehicle's km")</th>
                            <th>@lang('Price')</th>
                            <th>@lang('Garage')</th>
                            <th>@lang('Description')</th>
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

<div class="modal fade" id="modal-add" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modal-label-add" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-label-add">@lang('Add maintenance')</h5>
            </div>
            <div class="modal-body">
                <form id="addTruck">
                    @csrf
                    <input type="hidden" id="id_truck" name="id_truck" value="">
                    <div class="form-group">
                        <label for="plate">@lang('Plate')</label>
                        <select name="plate" id="plate" class="custom-select" required>
                            @foreach ($trucks as $plate)
                            <option value="{{ $plate->plate }}">{{ $plate->plate }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="type">@lang('Type')</label>
                        <input type="text" id="type" class="form-control" name="type" required>
                    </div>
                    <div class="form-group">
                        <label for="date">@lang('Date')</label>
                        <input type="date" id="date" class="form-control" name="date" required>
                    </div>
                    <div class="form-group form-check">
                        <input type="checkbox" class="form-check-input" id="alert" name="alert" value="1">
                        <label class="form-check-label" for="alert">@lang('Alert') (@lang('Optional'))</label>
                    </div>
                    <div class="form-group">
                        <small>@lang('Spuntando la casella qui sopra, 15 giorni prima del giorno della manutenzione si riceverà un avviso.')</small>
                    </div>
                    <div class="form-group">
                        <label for="period">@lang('Rinnovo automatico') (@lang('Optional'))</label>
                        <input type="number" class="form-control" id="period" name="period" max="24" min="1">
                        <small>@lang('Indicare ogni quanti mesi (fino a un massimo di 24) ripetere la manutenzione.')</small>
                    </div>
                    <div class="form-group">
                        <label for="price">@lang('Price') (@lang('Optional'))</label>
                        <input type="number" class="form-control" id="price" name="price">
                    </div>
                    <div class="form-group">
                        <label for="garage">@lang('Garage') (@lang('Optional'))</label>
                        <input type="text" id="garage" class="form-control" name="garage">
                    </div>
                    <div class="form-group">
                        <label for="km">@lang('Km') (@lang('Optional'))</label>
                        <input type="number" step="0.01" class="form-control" id="km" name="km">
                    </div>
                    <div class="form-group">
                        <label for="description">@lang('Description') (@lang('Optional'))</label>
                        <input type="text" class="form-control" id="description" maxlength="150" name="description">
                        <small>@lang('Max 50 characters.')</small>
                    </div>
                    <div class="form-group" id="form-result"></div>
                    <div class="modal-footer">
                        <button id="btn-close" type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-primary submit">
                            <span class="spinner-border spinner-border-sm loader-submit hidden" role="status" aria-hidden="true"></span>
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
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js" defer></script>

<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js" defer></script>

<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.4/js/dataTables.buttons.min.js" defer></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.bootstrap4.min.js" defer></script>

<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" defer></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" defer></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" defer></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.html5.min.js" defer></script>
<script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js" defer></script>

<!-- LIBRERIA PER LE DATE -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous"></script>

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


    $(document).ready(function() {

        var table = $('#datatable').DataTable({
            "dom": '<"row justify-content-between table-row"<"col-sm table-col"lB><"col-sm-auto"f>>rtip',
            "order": [
                [1, "asc"]
            ],
            buttons: {
                buttons: [{
                        extend: 'excelHtml5',
                        className: 'btn btn-success',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 7, 8]
                        },
                        text: '@lang("Export EXCEL")'
                    }, // NON FUNZIONA (PER ORA) CON I NUMERI
                    {
                        extend: 'pdfHtml5',
                        className: 'btn btn-danger',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 6, 7, 8]
                        },
                        text: '@lang("Export PDF")',
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
            "ajax": "{{ route('api.maint') }}",
            "columns": [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'plate',
                    name: 'plate'
                },
                {
                    data: 'type',
                    name: 'type'
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'period',
                    name: 'period'
                },
                {
                    data: 'km',
                    name: 'km'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'garage',
                    name: 'garage'
                },
                {
                    data: 'description',
                    name: 'description'
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
                    'targets': 3,
                    'render': function(data) {
                        return moment(data).format('DD-MM-YYYY');
                    }
                },
                {
                    'targets': [2, 4, 5, 6, 7, 8],
                    "orderable": false,
                },
                {
                    "searchable": false,
                    "targets": [3, 4, 5, 6]
                }
            ],
            'select': {
                'style': 'multi'
            },
            "language": {
                "url": language
            },
            "responsive": true,
            search: {
                "regex": true
            },
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

        $('#btn-close').on('click', function(event) {
            if ($('#addTruck').length != 0) $('#addTruck')[0].reset(); //PER CONTROLLARE SE SONO IN EDIT O IN ADD E EVITARE ERRORI DEL JAVASCRIPT
            else $('#editTruck')[0].reset();
        });

        $('#addTruck').on('submit', function(event) {
            event.preventDefault();

            $('#form-result').text('');
            $('#form-result').show();

            var url;
            var form = $(this).closest('form');

            if (form.attr('id') == 'addTruck') {
                url = '{{ route("admin.maint.store") }}';
            } else {
                url = '{{ route("admin.maint.edit") }}';
            };

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
                        if ($('#addTruck').length != 0) {
                            $('#addTruck')[0].reset(); //PER CONTROLLARE SE SONO IN EDIT O IN ADD E EVITARE ERRORI DEL JAVASCRIPT
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

        $('#btn-delete').on('click', function(e) {

            var rows_selected = table.column(0).checkboxes.selected();
            var id = [];

            $.each(rows_selected, function(index, rowId) {
                id[index] = rowId;
            });

            $('#message-success').text('');
            $('#message-success').show();

            $.ajax({
                url: '{{ route("admin.maint.delete") }}',
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    maint: id
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

        $('#btn-edit').on('click', function(e) {

            $('#modal-label-add').text('Edit maintenance');
            $('#addTruck').prop('id', 'editTruck');

            var rows_selected = table.column(0).checkboxes.selected();

            if (rows_selected.length == 1) {
                $('#id_truck').val(rows_selected[0]);
                var row = table.row('#' + rows_selected[0]).data();

                $('#plate').val(row['plate']);
                $('#type').val(row['type']);
                $('#brand').val(row['brand']);
                $('#model').val(row['model']);
                $('#km').val(row['km']);
                $('#description').val(row['description']);
                $('#group').val(row['group']);

                row['expirations'].forEach(function(expiration, i) {
                    var id = i + 1;
                    var html = "<div id='expiration_" + id + "' class='form-group'>";
                    html += '<label>@lang("Name")</label>';
                    html += '<input type="text" id="expirationName_' + id + '" class="form-control" name="expiration_' + id + '" required>'
                    html += '<label>@lang("Description")</label>';
                    html += '<input type="text" id="description_' + id + '" class="form-control" name="description_' + id + '">'
                    html += ' <label>@lang("Deadline")</label>';
                    html += '<input type="date" id="deadline_' + id + '" class="form-control" name="deadline_' + id + '"' + ` min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>`
                    html += '</div>'

                    $('#expirations-row').before(html);

                    $('#expirationName_' + id).val(expiration['name']);
                    if (expiration['deadline'] != undefined) {
                        $('#deadline_' + id).val(formatData(expiration['deadline']));
                    }
                    if (expiration['description'] != undefined) {
                        $('#description_' + id).val(expiration['description']);
                    }
                });
            }
        });

        $('#btn-add').on('click', function(e) {

            $('#modal-label-add').text('Add maintenance');
            $('#editTruck').attr('id', 'addTruck');

        });

        $('.select-input-date-to').change(function() {
            var url = "{{ route('api.maint') }}";

            if ($(this).val() == '') {
                var date = 'nullValue';
            } else {
                var date = $(this).val();
            }
            url += "/" + date + "/" + $('.select-input-date-from').val();

            table.ajax.url(url).load();
        }); // FILTRO PER GRUPPO

        $('.select-input-date-from').change(function() {
            var url = "{{ route('api.maint') }}";

            if ($('.select-input-date-to').val() == '') {
                var date = 'nullValue';
            } else {
                var date = $('.select-input-date-to').val();
            }
            url += "/" + date + "/" + $(this).val();

            table.ajax.url(url).load();
        }); // FILTRO PER TIPO

        $('#btn-reset').click(function(e) {
            table.ajax.url("{{ route('api.maint') }}").load();
        }) //RESET DELLE DATE

    });
</script>
@endsection
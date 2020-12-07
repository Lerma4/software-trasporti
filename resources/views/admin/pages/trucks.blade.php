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
                    <button id="btn-show-expirations" type="button" class="btn btn-primary btn-show-exp">
                        @lang('Show upcoming deadlines')
                    </button>
                </div>
                <div class="col-sm-auto">
                    <div class="form-inline">
                        <label id="search_type">@lang('Search for type'):</label>
                        <select class="form-control select-input-type" data-column="3">
                            <option default value="">@lang('All')</option>
                            <option value="@lang('semirimorchio')">@lang('Semirimorchio')</option>
                            <option value="@lang('trattore')">@lang('Trattore')</option>
                            <option value="@lang('motrice')">@lang('Motrice')</option>
                        </select>
                        <label id="search_group">@lang('Search for group'):</label>
                        <select class="form-control select-input-group" data-column="8">
                            <option default value="">@lang('All')</option>
                            @foreach ($groups as $group)
                            <option value="{{$group->name}}">{{$group->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table cellspacing="0" class="table table-bordered nowrap" id="datatable" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>@lang('Plate')</th>
                            <th>@lang('Type')</th>
                            <th>@lang('Brand')</th>
                            <th>@lang('Model')</th>
                            <th>@lang('Km')</th>
                            <th>@lang('Description')</th>
                            <th>@lang('Group')</th>
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
                <h5 class="modal-title" id="modal-label-add">@lang('Add truck')</h5>
            </div>
            <div class="modal-body">
                <form id="addTruck">
                    @csrf
                    <input type="hidden" id="id_truck" name="id_truck" value="">
                    <div class="form-group">
                        <label for="plate">@lang('Plate')</label>
                        <input type="text" id="plate" class="form-control" name="plate" required>
                    </div>
                    <div class="form-group">
                        <label for="type">@lang('Type')</label>
                        <select name="type" id="type" class="custom-select" required>
                            <option value=""></option>
                            <option value="@lang('semirimorchio')">@lang('Semirimorchio')</option>
                            <option value="@lang('trattore')">@lang('Trattore')</option>
                            <option value="@lang('motrice')">@lang('Motrice')</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="brand">@lang('Brand') (@lang('Optional'))</label>
                        <input type="text" id="brand" class="form-control" name="brand">
                    </div>
                    <div class="form-group">
                        <label for="model">@lang('Model') (@lang('Optional'))</label>
                        <input type="text" class="form-control" id="model" name="model">
                    </div>
                    <div class="form-group">
                        <label for="km">@lang('Km')</label>
                        <input type="number" step="0.01" class="form-control" id="km" name="km" required>
                    </div>
                    <div class="form-group">
                        <label for="description">@lang('Description (optional)')</label>
                        <input type="text" class="form-control" id="description" maxlength="150" name="description">
                        <small>@lang('Max 50 characters.')</small>
                    </div>
                    <div class="form-group">
                        <label for="group">@lang('Gruppo')</label>
                        <select name="group" id="group" class="custom-select">
                            <option value="">@lang('No group')</option>
                            @foreach ($groups as $group)
                            <option value="{{ $group->name }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div id="expirations-title" class="form-group">
                        <h5>
                            @lang('Expirations') (@lang('Optional'))
                        </h5>
                        <small>@lang('For example insurance and property tax.')</small>
                    </div>
                    <div id="expirations-row" class="row justify-content-center">
                        <div class="col-auto">
                            <button id="buttonAdd" type="button" class="btn btn-success btn-expiration">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div class="col-auto">
                            <button id="buttonRemove" type="button" class="btn btn-danger btn-expiration">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div id="form-result"></div>
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

    function formatExp(d) {
        var today = new Date();
        var deadline;
        today.setDate(today.getDate() + 16);
        var html = '<table class="table table-sm table-borderless table-exp"><thead>' +
            '<tr> <th scope = "col" > @lang("Name")' +
            '</th> <th scope = "col" > @lang("Deadline")' +
            '</th> <th scope = "col" > @lang("Description") </th> </thead>' +
            '<tbody>';
        d.forEach(element => {
            html += '<tr><td>' + element.name + '</td>'
            if (element.deadline != null) {
                deadline = new Date(formatInternational(element.deadline));
                html += '<td>' + element.deadline;
                if (deadline < today) {
                    html += '<i class="fas fa-exclamation-triangle"></i>';
                }
                html += '</td>';
            } else {
                html += '<td></td>';
            }
            if (element.description != null) {
                html += '<td>' + element.description + '</td>'
            } else {
                html += '<td></td>';
            }
            html += '</tr>';
        });
        html += '</tbody></table>';
        return html;
    }


    $(document).ready(function() {

        var table = $('#datatable').DataTable({
            "dom": '<"row justify-content-between table-row"<"col-sm table-col"lB><"col-sm-auto"f>>rtip',
            "order": [
                [2, "asc"]
            ],
            buttons: {
                buttons: [{
                        extend: 'excelHtml5',
                        className: 'btn btn-success',
                        exportOptions: {
                            columns: [2, 3, 4, 5, 7, 8]
                        },
                        text: '@lang("Export EXCEL")'
                    }, // NON FUNZIONA (PER ORA) CON I NUMERI
                    {
                        extend: 'pdfHtml5',
                        className: 'btn btn-danger',
                        exportOptions: {
                            columns: [2, 3, 4, 5, 6, 7, 8]
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
            "ajax": "{{ route('api.trucks') }}",
            "columns": [{
                    data: 'id',
                    name: 'id'
                },
                {
                    "className": 'details-control',
                    "orderable": false,
                    "data": 'expirations',
                    "defaultContent": '',
                    "render": function(data, type, row) {
                        var html = "",
                            check = 0;
                        if (data.length > 0) {
                            html = '<button id="btn-details" type="button" class="btn btn-sm btn-success">' +
                                '<i class="fas fa-plus"></i>' +
                                '</button>';
                            var today = new Date();
                            var deadline;
                            today.setDate(today.getDate() + 16);

                            data.forEach(expiration => {
                                if (expiration.deadline != null) {
                                    deadline = new Date(formatInternational(expiration.deadline));
                                    if (deadline < today) {
                                        html += `<i title="@lang('There is at least one upcoming deadline')"` +
                                            'class="fas fa-exclamation-triangle fa-lg alert-expiration"></i>';
                                    }
                                }
                            });
                        }
                        return html;
                    },
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
                    data: 'brand',
                    name: 'brand'
                },
                {
                    data: 'model',
                    name: 'model'
                },
                {
                    data: 'km',
                    name: 'km'
                },
                {
                    data: 'description',
                    name: 'description'
                },
                {
                    data: 'group',
                    name: 'group'
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
                }, {
                    'targets': 7,
                    "orderable": false,
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
                row.child(formatExp(rowData.expirations)).show();
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

        $('#buttonAdd').on('click', function(event) {
            var lastInput = $('#expirations-row').prev();
            var id;
            var idNew = [];
            if (lastInput.attr('id') == 'expirations-title') {
                id = 1;
            } else {
                idNew = lastInput.attr('id').split("_");
                idNew[1] = parseInt(idNew[1]);
                id = idNew[1] + 1;
            }

            var html = "<div id='expiration_" + id + "' class='form-group'>";
            html += '<label>@lang("Name")</label>';
            html += '<input type="text" id="expirationName_' + id + '" class="form-control" name="expiration_' + id + '" required>'
            html += '<label>@lang("Description")</label>';
            html += '<input type="text" id="description_' + id + '" class="form-control" name="description_' + id + '">'
            html += ' <label>@lang("Deadline")</label>';
            html += '<input type="date" id="deadline_' + id + '" class="form-control" name="deadline_' + id + '"' + ` min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>`
            html += '</div>'

            $('#expirations-row').before(html);
        });

        $('#buttonRemove').on('click', function(event) {
            var lastInput = $('#expirations-row').prev();

            if (lastInput.attr('id') != 'expirations-title') {
                lastInput.remove();
            }
        });

        $('#btn-close').on('click', function(event) {
            if ($('#addTruck').length != 0) $('#addTruck')[0].reset(); //PER CONTROLLARE SE SONO IN EDIT O IN ADD E EVITARE ERRORI DEL JAVASCRIPT
            else $('#editTruck')[0].reset();

            var lastInput = $('#expirations-row').prev();
            while (lastInput.attr('id') != 'expirations-title') {
                lastInput.remove();
                lastInput = $('#expirations-row').prev();
            }
        });

        $('#addTruck').on('submit', function(event) {
            event.preventDefault();

            $('#form-result').text('');
            $('#form-result').show();

            var url;
            var form = $(this).closest('form');

            if (form.attr('id') == 'addTruck') {
                url = '{{ route("admin.trucks.store") }}';
            } else {
                url = '{{ route("admin.trucks.edit") }}';
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

                            var lastInput = $('#expirations-row').prev();
                            while (lastInput.attr('id') != 'expirations-title') {
                                lastInput.remove();
                                lastInput = $('#expirations-row').prev();
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

        $('#btn-delete').on('click', function(e) {

            var rows_selected = table.column(0).checkboxes.selected();
            var id = [];

            $.each(rows_selected, function(index, rowId) {
                id[index] = rowId;
            });

            $('#message-success').text('');
            $('#message-success').show();

            $.ajax({
                url: '{{ route("admin.trucks.delete") }}',
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    trucks: id
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

            $('#modal-label-add').text('Edit truck');
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

            $('#modal-label-add').text('Add truck');
            $('#editTruck').attr('id', 'addTruck');

        });

        $('.select-input-group').change(function() {
            if ($('.btn-show-exp').text() == "@lang('Show regular table')") {
                var url = "{{ route('api.trucks.expirations') }}";
            } else {
                var url = "{{ route('api.trucks') }}";
            }
            if ($(this).val() == '') {
                var group = 'nullValue';
            } else {
                var group = $(this).val();
            }
            url += "/" + group + "/" + $('.select-input-type').val();

            table.ajax.url(url).load();
        }); // FILTRO PER GRUPPO

        $('.select-input-type').change(function() {
            if ($('.btn-show-exp').text() == "@lang('Show regular table')") {
                var url = "{{ route('api.trucks.expirations') }}";
            } else {
                var url = "{{ route('api.trucks') }}";
            }
            if ($('.select-input-group').val() == '') {
                var group = 'nullValue';
            } else {
                var group = $('.select-input-group').val();
            }
            url += "/" + group + "/" + $(this).val();

            table.ajax.url(url).load();
        }); // FILTRO PER TIPO

        $('#btn-show-expirations').click(function(e) {
            if ($(this).attr('id') == 'btn-show-table') {

                var url = "{{ route('api.trucks') }}";
                if ($('.select-input-group').val() == '') {
                    var group = 'nullValue';
                } else {
                    var group = $('.select-input-group').val();
                }
                url += "/" + group + "/" + $('.select-input-type').val();

                table.ajax.url(url).load();

                $(this).text("@lang('Show upcoming deadlines')");
                $(this).prop('id', 'btn-show-expirations');

            } else {

                var url = "{{ route('api.trucks.expirations') }}";
                if ($('.select-input-group').val() == '') {
                    var group = 'nullValue';
                } else {
                    var group = $('.select-input-group').val();
                }
                url += "/" + group + "/" + $('.select-input-type').val();

                table.ajax.url(url).load();

                $(this).text("@lang('Show regular table')");
                $(this).prop('id', 'btn-show-table');

            }
        });

    });
</script>
@endsection
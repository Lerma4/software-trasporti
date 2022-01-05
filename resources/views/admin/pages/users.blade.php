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
        <div class="card-body">
            @include('multiauth::message')
            <!--<form method="post" enctype="multipart/form-data" action="{{ route('admin.users.importExcel') }}">
                @csrf
                <div class="form-group">
                    <table class="table">
                        <tr>
                            <td width="40%" align="right"><label>@lang('Select File for Upload (limit of 1000 records,
                                    formats XLS and XLSX)') DA AGGIUNGERE REGOLE PASSWORD</label></td>
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
            </form>-->
            <div class="row justify-content-between page-row">
                <div class="col-sm">
                    <button id="btn-add" type="button" class="btn btn-primary" data-toggle="modal"
                        data-target="#modal-add">
                        @lang('New')
                    </button>
                    <button id="btn-edit" type="button" class="btn btn-secondary" data-toggle="modal"
                        data-target="#modal-add" disabled>
                        @lang('Edit')
                    </button>
                    <button type="button" class="btn btn-danger" id="btn-delete" disabled>
                        @lang('Delete')
                    </button>
                    <button id="btn-show-licenses" type="button" class="btn btn-primary btn-show-licenses">
                        @lang('Show expiring licenses')
                    </button>
                </div>
                <div class="col-sm-auto">
                    <div class="form-inline">
                        <label id="search_group">@lang('Search for group'):</label>
                        <select class="form-control select-input" data-column="4">
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
                            <th>@lang('Name')</th>
                            <th>@lang('Email')</th>
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

<!-- AGGIUNGI UTENTI -->

<div class="modal fade" id="modal-add" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="modal-label-add" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-label-add">@lang('Add user')</h5>
            </div>
            <div class="modal-body">
                <form id="addUser">
                    @csrf
                    <input type="hidden" id="id_user" name="id_user" value="">
                    <div class="form-group">
                        <label for="email">@lang('Email')</label>
                        <input type="email" id="email" value="{{ old('email') }}" class="form-control" name="email"
                            autocomplete="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">@lang('Password')</label>
                        <input type="password" id="password" class="form-control password" name="password"
                            autocomplete="new-password" required>
                        <span class="invalid-feedback" role="alert">
                            <strong id="password-error"></strong>
                        </span>
                        <small>@lang('The password must have at least 8 characters, including an uppercase letter, a')
                            @lang('lowercase letter and a number.')</small>
                    </div>
                    <div class="form-group">
                        <label for="password_confirm">@lang('Conferma Password')</label>
                        <input id="password_confirm" type="password" class="form-control passwordConfirm"
                            name="password_confirmation" required autocomplete="new-password">
                    </div>

                    <button type="button" class="btn btn-success random_password">@lang('Random Password')</button>
                    <button type="button" class="btn btn-secondary show_password">@lang('Show Password')</button>

                    <div class="form-group">
                        <label for="name">@lang('Nome')</label>
                        <input type="text" class="form-control" id="name" value="{{ old('nome') }}" name="name"
                            required>
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
                    <div id="license-title" class="form-group">
                        <h5>
                            @lang('Licenses (Optional)')
                        </h5>
                    </div>
                    <div id="row-licenses" class="row justify-content-center">
                        <div class="col-auto">
                            <button id="buttonAdd" type="button" class="btn btn-success btn-trip">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <div class="col-auto">
                            <button id="buttonRemove" type="button" class="btn btn-danger btn-trip">
                                <i class="fas fa-minus"></i>
                            </button>
                        </div>
                    </div>
                    <div id="form-result"></div>
                    <div class="modal-footer">
                        <button id="btn-close" type="button" class="btn btn-secondary"
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

<script>
    function generatePassword() {
        var length = 8,
            charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789",
            retVal = "";
        for (var i = 0, n = charset.length; i < length; ++i) {
            retVal += charset.charAt(Math.floor(Math.random() * n));
        }
        return retVal;
    };

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

    function formatLicenses(d) {
        var today = new Date();
        var deadline;
        today.setDate(today.getDate() + 16);
        var html = '<ul class="list-group list-group-flush list-licenses">';
        d.forEach(element => {
            if (element.deadline != null) {
                deadline = new Date(formatInternational(element.deadline));
                html += '<li class="list-group-item">' + element.name;
                html += ' : ' + element.deadline;
                if (deadline < today) {
                    html += '<i class="fas fa-exclamation-triangle"></i>';
                }
            } else {
                html += '<li class="list-group-item">' + element.name;
            }
            html += '</li>';
        });
        html += '</ul>';
        return html;
    }


    $(document).ready(function() {

        $('.random_password').on('click', function(event) {
            var passwordField = $(event.target).closest('.modal-body').find('.password');
            var passwordFieldConfirm = $(event.target).closest('.modal-body').find('.passwordConfirm');
            var i = 0;
            var patt = /^(?=.*[0-9])(?=.*[a-z])(?=.*[A-Z])(?=.*)/;

            while (i == 0) {
                var newPassword = generatePassword();
                if (patt.test(newPassword)) {
                    i = 1;
                }
            }
            passwordField.val(newPassword);
            passwordFieldConfirm.val(newPassword);
        });

        $('.show_password').on('click', function() {
            var passwordField = $(event.target).closest('.modal-body').find('.password');
            var passwordFieldType = passwordField.attr('type');
            if (passwordFieldType == 'password') {
                passwordField.attr('type', 'text');
                $(this).text('@lang("Hide Password")');
            } else {
                passwordField.attr('type', 'password');
                $(this).text('@lang("Show Password")');
            }
        });

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
                            columns: [2, 3, 4]
                        },
                        text: '@lang("Export EXCEL")'
                    },
                    {
                        extend: 'pdfHtml5',
                        className: 'btn btn-danger',
                        exportOptions: {
                            columns: [2, 3, 4]
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
            "ajax": "{{ route('api.users') }}",
            "columns": [{
                    data: 'id',
                    name: 'id'
                },
                {
                    "className": 'details-control',
                    "orderable": false,
                    "data": 'licenses',
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

                            var check = 0;
                            data.forEach(license => {
                                if (license.deadline != null && check === 0) {
                                    deadline = new Date(formatInternational(license.deadline));
                                    if (deadline < today) {
                                        html += `<i title="@lang('There is at least one license expiring')"` +
                                            'class="fas fa-exclamation-triangle fa-lg alert-license"></i>';
                                        check = -1;
                                    }
                                }
                            });
                        }
                        return html;
                    },
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'group',
                    name: 'group'
                },
            ],
            "columnDefs": [
                {
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
                }
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
                row.child(formatLicenses(rowData.licenses)).show();
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
            var lastInput = $('#row-licenses').prev();
            var id;
            var idNew = [];
            if (lastInput.attr('id') == 'license-title') {
                id = 1;
            } else {
                idNew = lastInput.attr('id').split("_");
                idNew[1] = parseInt(idNew[1]);
                id = idNew[1] + 1;
            }

            var html = "<div id='license_" + id + "' class='form-group'>";
            html += '<label>@lang("Name")</label>';
            html += '<input type="text" id="licenseName_' + id + '" class="form-control" name="license_' + id + '" required>'
            html += ' <label>@lang("Deadline")</label>';
            html += '<input type="date" id="deadline_' + id + '" class="form-control" name="deadline_' + id + '"' + ` min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">`
            html += '</div>'

            $('#row-licenses').before(html);
        });

        $('#buttonRemove').on('click', function(event) {
            var lastInput = $('#row-licenses').prev();

            if (lastInput.attr('id') != 'license-title') {
                lastInput.remove();
            }
        });

        $('#btn-close').on('click', function(event) {
            if ($('#addUser').length != 0) $('#addUser')[0].reset(); //PER CONTROLLARE SE SONO IN EDIT O IN ADD E EVITARE ERRORI DEL JAVASCRIPT
            else $('#editUser')[0].reset();

            var lastInput = $('#row-licenses').prev();
            while (lastInput.attr('id') != 'license-title') {
                lastInput.remove();
                lastInput = $('#row-licenses').prev();
            }
        });

        $('#addUser').on('submit', function(event) {
            event.preventDefault();

            $('#form-result').text('');
            $('#form-result').show();

            var url;
            var form = $(this).closest('form');

            if (form.attr('id') == 'addUser') {
                url = '{{ route("admin.users.store") }}';
            } else {
                if(!confirm("@lang('Are you sure?')")) return;
                url = '{{ route("admin.users.edit") }}';
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

        $('#btn-delete').on('click', function(e) {
            if(!confirm("@lang('Are you sure?')")) return;

            var rows_selected = table.column(0).checkboxes.selected();
            var id = [];

            $.each(rows_selected, function(index, rowId) {
                id[index] = rowId;
            });

            $('#message-success').text('');
            $('#message-success').show();

            $.ajax({
                url: '{{ route("admin.users.delete") }}',
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    users: id
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

            $('#modal-label-add').text("@lang('Edit user')");
            $('#addUser').prop('id', 'editUser');

            $('#password').prop('required', false);
            $('#password_confirm').prop('required', false);

            var rows_selected = table.column(0).checkboxes.selected();

            if (rows_selected.length == 1) {
                $('#id_user').val(rows_selected[0]);
                var row = table.row('#' + rows_selected[0]).data();

                $('#email').val(row['email']);
                $('#name').val(row['name']);
                $('#group').val(row['group']);

                row['licenses'].forEach(function(license, i) {
                    var id = i + 1;
                    var html = "<div id='license_" + id + "' class='form-group'>";
                    html += '<label>@lang("Name")</label>';
                    html += '<input type="text" id="licenseName_' + id + '" class="form-control" name="license_' + id + '" required>'
                    html += ' <label>@lang("Deadline")</label>';
                    html += '<input type="date" id="deadline_' + id + '" class="form-control" name="deadline_' + id + '"' + ` min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}">`
                    html += '</>'

                    $('#row-licenses').before(html);

                    $('#licenseName_' + id).val(license['name']);
                    if (license['deadline'] != undefined) {
                        $('#deadline_' + id).val(formatData(license['deadline']));
                    }
                });
            }
        });

        $('#btn-add').on('click', function(e) {

            $('#modal-label-add').text('Add user');
            $('#editUser').attr('id', 'addUser');

            $('#password').prop('required', true);
            $('#password_confirm').prop('required', true);

        });

        $('.select-input').change(function() {
            if ($('.btn-show-licenses').text() == "@lang('Show regular table')") {
                var url = "{{ route('api.users.licenses') }}";
            } else {
                var url = "{{ route('api.users') }}";
            }
            url += "/" + $(this).val();

            table.ajax.url(url).load();
        }); // FILTRO PER GRUPPO

        $('#btn-show-licenses').click(function(e) {
            if ($(this).attr('id') == 'btn-show-table') {

                var url = "{{ route('api.users') }}";
                url += "/" + $('.select-input').val();
                table.ajax.url(url).load();

                $(this).text("@lang('Show expiring licenses')");
                $(this).prop('id', 'btn-show-licenses');
            } else {

                var url = "{{ route('api.users.licenses') }}";
                url += "/" + $('.select-input').val();
                table.ajax.url(url).load();

                $(this).text("@lang('Show regular table')");
                $(this).prop('id', 'btn-show-table');
            }
        });

    });
</script>
@endsection
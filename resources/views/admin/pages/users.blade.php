@extends('multiauth::layouts.admin')

@section('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
<link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css" rel="stylesheet" />
@endsection

@section('content')
<div class="col-12 pages-content">
    <div class="card">
        <div class="card-body">
            @include('multiauth::message')
            <form method="post" enctype="multipart/form-data" action="{{ route('admin.users.importExcel') }}">
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
            <div class="row">
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
            <div class="row">
                <div class="form-group">
                    <label>@lang('Search for group'):</label>
                    <select class="form-control select-input" data-column="3">
                        <option default value="">@lang('All')</option>
                        @foreach ($groups as $group)
                        <option value="{{$group->name}}">{{$group->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table cellspacing="0" class="table table-bordered nowrap" id="datatable" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>@lang('Name')</th>
                            <th>@lang('Email')</th>
                            <th>@lang('Group')</th>
                            <th>@lang('Licenses')</th>
                            <th>@lang('Deadlines')</th>
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

<div class="modal fade" id="modal-add" data-backdrop="static" tabindex="-1" aria-labelledby="modal-label-add" aria-hidden="true">
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
                        <input type="email" id="email" value="{{ old('email') }}" class="form-control" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">@lang('Password')</label>
                        <input type="password" id="password" class="form-control password" name="password" required>
                        <span class="invalid-feedback" role="alert">
                            <strong id="password-error"></strong>
                        </span>
                        <small>@lang('The password must have at least 8 characters, including an uppercase letter, a lowercase letter and a number.')</small>
                    </div>
                    <div class="form-group">
                        <label for="password_confirm">@lang('Conferma Password')</label>
                        <input id="password_confirm" type="password" class="form-control passwordConfirm" name="password_confirmation" required autocomplete="new-password">
                    </div>

                    <button type="button" class="btn btn-success random_password">@lang('Random Password')</button>
                    <button type="button" class="btn btn-secondary show_password">@lang('Show Password')</button>

                    <div class="form-group">
                        <label for="name">@lang('Nome')</label>
                        <input type="text" class="form-control" id="name" value="{{ old('nome') }}" name="name" required>
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
                    <button id="buttonAdd" type="button" class="btn btn-success btn-license">
                        <i class="fas fa-plus"></i>
                    </button>
                    <button id="buttonRemove" type="button" class="btn btn-danger btn-license">
                        <i class="fas fa-minus"></i>
                    </button>
                    <div id="form-result"></div>
                    <div class="modal-footer">
                        <button id="btn-close" type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-primary submit">@lang('Submit')</button>
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
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" defer></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" defer></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" defer></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.html5.min.js" defer></script>
<script type="text/javascript" src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js" defer></script>

@switch(App::getLocale())
@case('it')
<script>
    var language = "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Italian.json";
</script>
@break
@case('en')
<script>
    var language = "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/English.json";
</script>
@break
@default
<script>
    var language = "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/English.json";
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
            dom: 'lBFfrtip',
            "order": [
                [1, "asc"]
            ],
            buttons: {
                buttons: [{
                        extend: 'excelHtml5',
                        className: 'btn btn-success',
                        exportOptions: {
                            columns: [1, 2, 3]
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        className: 'btn btn-danger',
                        exportOptions: {
                            columns: [1, 2, 3]
                        },
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
                {
                    data: 'licenses',
                    "render": "[<br>].name"
                },
                {
                    data: 'licenses',
                    "render": "[<br>].deadline"
                }
            ],
            "columnDefs": [{
                    "targets": 4,
                    "orderable": false,
                    "searchable": false,
                    "width": "5%",
                },
                {
                    "targets": 5,
                    "orderable": false,
                    "searchable": false,
                    "width": "5%",
                },
                {
                    'targets': 0,
                    'checkboxes': {
                        'selectRow': true
                    },
                    'width': '1%'
                }
            ],
            'select': {
                'style': 'multi'
            },
            "language": {
                "url": language
            },
            "responsive": true,
            //"scrollX": true,
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
            var lastInput = $('#buttonAdd').prev();
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

            $(this).before(html);
        });

        $('#buttonRemove').on('click', function(event) {
            var lastInput = $('#buttonAdd').prev();

            if (lastInput.attr('id') != 'license-title') {
                lastInput.remove();
            }
        });

        $('#btn-close').on('click', function(event) {
            if ($('#addUser').length != 0) $('#addUser')[0].reset(); //PER CONTROLLARE SE SONO IN EDIT O IN ADD E EVITARE ERRORI DEL JAVASCRIPT
            else $('#editUser')[0].reset();

            var lastInput = $('#buttonAdd').prev();
            while (lastInput.attr('id') != 'license-title') {
                lastInput.remove();
                lastInput = $('#buttonAdd').prev();
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
                        if ($('#addUser').length != 0) $('#addUser')[0].reset(); //PER CONTROLLARE SE SONO IN EDIT O IN ADD E EVITARE ERRORI DEL JAVASCRIPT
                        else $('#editUser')[0].reset();

                        var lastInput = $('#buttonAdd').prev();
                        while (lastInput.attr('id') != 'license-title') {
                            lastInput.remove();
                            lastInput = $('#buttonAdd').prev();
                        }

                        $('#datatable').DataTable().ajax.reload();
                    }
                    $('#form-result').html(html);
                    $('#form-result').delay(5000).fadeOut();
                }
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
                    html = '<div class="alert alert-success">' + data.success + '</div>';
                    $('#datatable').DataTable().ajax.reload();
                    $('html, body').animate({
                        scrollTop: 0
                    }, 'fast');
                    $('#message-success').html(html);
                    $('#message-success').delay(5000).fadeOut();
                }
            });
        });

        $('#btn-edit').on('click', function(e) {

            $('#modal-label-add').text('Edit user');
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
                    html += '</div>'

                    $('#buttonAdd').before(html);

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
            table.column($(this).data('column'))
                .search($(this).val())
                .draw();
        });

    });
</script>
@endsection
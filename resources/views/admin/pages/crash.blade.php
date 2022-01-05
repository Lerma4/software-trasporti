@extends('multiauth::layouts.admin')

@section('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.bootstrap4.min.css">
<link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css"
    rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/file-uploader/5.16.2/fine-uploader.min.css"
    integrity="sha512-RIjvm40hf5zylq2bAzo6gq7zle9d02ivUUrIB9FjBZYd2N87P9VcKoSufenZp5NSMR47IjvG1R2g3EnQ0qEjYA=="
    crossorigin="anonymous" />

<!-- NECESSARI PER MEDIALIBRARY PRO -->
@livewireStyles
<link rel="stylesheet" type="text/css" href="{{ asset('medialibrary_css/styles.css') }}" />
@endsection

@section('content')
<div class="col-12 pages-content">
    <div class="card">
        <div class="card-body">
            @include('multiauth::message')
            <div class="row justify-content-between page-row">
                <div class="col-sm">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addDocuments">
                        @lang('Add report')
                    </button>
                    <button id="btn-edit" type="button" class="btn btn-secondary" data-toggle="modal"
                        data-target="#editDocument" disabled>
                        @lang('Edit')
                    </button>
                    <button type="button" class="btn btn-danger" id="btn-delete" disabled>
                        @lang('Delete')
                    </button>
                </div>
            </div>

            <br>
            <div class="table-responsive">
                <table cellspacing="0" class="table table-bordered nowrap" id="datatable" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>@lang('Date')</th>
                            <th>@lang('Email')</th>
                            <th>@lang("Driver's name")</th>
                            <th>@lang('Plate')</th>
                            <th>@lang("Plate's semitrailer")</th>
                            <th>@lang("Download")</th>
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

<!-- ADD DOCUMENT -->

<div class="modal fade" id="addDocuments" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog"
    aria-labelledby="documentLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentLabel">@lang("Add crash report")</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <label class="small-alert">@lang("Prima di premere su Conferma attendere che tutti gli upload
                            siano
                            completati.")</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label>@lang("Compilare il form sottostante (se nel nome saranno inseriti degli spazi verranno
                            eliminati) per effettuare l'upload dei documenti condivisi con l'autista"):</label>
                    </div>
                </div>
                <form action="{{ route('api.crash.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <select class="form-control" name="user" required>
                            <option value="" disabled selected>@lang("Select driver")</option>
                            @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <input type="date" class="form-control" name="date"
                            max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" placeholder='@lang("Date")' required>
                    </div>

                    <div class="form-group">
                        <select class="form-control" name="plate" required>
                            <option value="" disabled selected>@lang("Plate")</option>
                            @foreach ($plates as $plate)
                            <option value="{{ $plate->plate }}">{{ $plate->plate }} </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <select class="form-control" name="plate_s">
                            <option value="" disabled selected>@lang("Plate semitrailer")</option>
                            @foreach ($plates_semi as $plate)
                            <option value="{{ $plate->plate }}">{{ $plate->plate }} </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="description">@lang('Incident description'):</label>
                        <textarea class="form-control" name="description" rows="3" maxlength="20000" minlength="10"
                            required></textarea>
                    </div>

                    <x-media-library-attachment multiple max-items="5" rules="mimes:png,jpg,jpeg,heif|max:2000"
                        name="photos" />

                    <div class="modal-footer">
                        <button type="button" id="btn-close-document" class="btn btn-secondary"
                            data-dismiss="modal">@lang(' Close')
                        </button>
                        <button type="submit" class="submit-document btn btn-primary">
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

<!-- EDIT DOCUMENT -->

<div class="modal fade" id="editDocument" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog"
    aria-labelledby="documentLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="documentLabel">@lang("Edit document")</h5>
            </div>
            <div class="modal-body">

                <div id="form-result-edit"></div>
                <form id="edit-document" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id_crash">

                    <div class="form-group">
                        <select id="email" class="form-control" name="email" required>
                            <option value="" disabled selected>@lang("Select driver")</option>
                            @foreach ($users as $user)
                            <option value="{{ $user->email }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <input id="date_edit" type="date" class="form-control" name="date"
                            max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" placeholder='@lang("Date")' required>
                    </div>

                    <div class="form-group">
                        <select id="plate" class="form-control" name="plate" required>
                            <option value="" disabled selected>@lang("Plate")</option>
                            @foreach ($plates as $plate)
                            <option value="{{ $plate->plate }}">{{ $plate->plate }} </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <select id="plate_s" class="form-control" name="plate_s">
                            <option value="" disabled selected>@lang("Plate semitrailer")</option>
                            <option value=""> @lang('No semitrailer')</option>
                            @foreach ($plates_semi as $plate)
                            <option value="{{ $plate->plate }}">{{ $plate->plate }} </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="button" id="btn-close-editDocument" class="btn btn-secondary"
                            data-dismiss="modal">@lang(' Close')
                        </button>
                        <button type="submit" class="submit-document btn btn-primary">
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
    src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.html5.min.js" defer></script>
<script type="text/javascript"
    src="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/js/dataTables.checkboxes.min.js" defer></script>

<!-- LIBRERIE NECESSARIE PER MEDIALIBRARY PRO -->
@livewireScripts
<script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.6.0/dist/alpine.min.js" defer></script>

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
        // FADE OUT DEI MESSAGGI DAI CONTROLLER

        $('.message').delay(4000).fadeOut();

        // DATATABLE

        var table = $('#datatable').DataTable({
            "order": [
                [1, "desc"]
            ],
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "@lang('All')"]
            ],
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('api.crash') }}",
            "columns": [
                {
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'date',
                    name: 'date'
                },
                {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'plate',
                    name: 'plate'
                },
                {
                    data: 'plate_s',
                    name: 'plate_s'
                },
                {
                    "className": 'document-download',
                    "orderable": false,
                    "data": 'id',
                    "width": '1%',
                    "defaultContent": '',
                    "render": function(data, type, row) {
                        var html = "";

                        if (row.read == true) {
                            html += `<a href="{{ route('api.crash.download') }}/` +
                                data + `"` +
                                ` class="btn btn-secondary btn-sm btn-download">` +
                                `<i class="fas fa-file-download"></i></a>`;
                        } else {
                            html += `<a href="{{ route('api.crash.download') }}/` +
                                data + `"` +
                                ` class="btn btn-secondary btn-sm btn-download">` +
                                `<i class="fas fa-file-download"></i></a>`+
                                '<i title="@lang('Still to be downloaded')" class="fas fa-exclamation-triangle fa-lg alert-expiration"></i>';
                        }

                        return html;
                    },
                },
                {
                    data: 'read',
                    name: 'read',
                    visible: false
                }
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
                'targets': 3,
                'width': '1%'
            }],
            "language": {
                "url": language,
            },
            "responsive": true,
        });

        // RIAGGIORNO LA TABELLA CON 1 SECONDO DI RITARDO PER VEDERE GLI ALERT AGGIORNATI
        $(document.body).on('click', '.btn-download' ,function(){
            setTimeout(
                function() 
                {
                    $('#datatable').DataTable().ajax.reload();
                }, 1000);
        });

        // GESTIONE BUTTON EDIT E DELETE

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

        // DELETE DOCUMENT

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
                url: '{{ route("api.crash.delete") }}',
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    ids: id
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

        // EDIT DOCUMENT

        $('#btn-edit').on('click', function(e) {
        
            var rows_selected = table.column(0).checkboxes.selected();

            if (rows_selected.length == 1) {
                $('#id_crash').val(rows_selected[0]);
                var row = table.row('#' + rows_selected[0]).data();

                var date = formatData(row['date']);

                $('#email').val(row['email']);
                $('#plate').val(row['plate']);
                $('#plate_s').val(row['plate_s']);
                $('#date_edit').val(date);
            }

        });

        $('#edit-document').on('submit', function(event) {
            if(!confirm("@lang('Are you sure?')")) return;

            event.preventDefault();
            var form = $(this).closest('form');

            $('#form-result-edit').text('');
            $('#form-result-edit').fadeIn();

            $.ajax({
                url: "{{route('api.crash.edit')}}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: $(form).serialize(),
                dataType: "json",
                beforeSend: function() {
                    $('.loader-submit').removeClass('hidden');
                    $('.submit-document').contents().last().replaceWith('@lang("Loading...")');
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
                        $('#datatable').DataTable().ajax.reload();
                    }
                    $('#form-result-edit').html(html);
                    $('#form-result-edit').delay(4000).fadeOut();
                },
                complete: function() {
                    $('.loader-submit').addClass('hidden');
                    $('.submit-document').contents().last().replaceWith('@lang("Submit")');
                },
            });
        });

        $('#btn-close-document').on('click', function(event) {
            $(this).closest("form")[0].reset();
        });

    });
</script>
@endsection
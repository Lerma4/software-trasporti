@extends('multiauth::layouts.admin')

@section('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.bootstrap4.min.css">
<link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css"
    rel="stylesheet" />

<!-- NECESSARI PER MEDIALIBRARY PRO -->
@livewireStyles
<link rel="stylesheet" type="text/css" href="{{ asset('medialibrary_css/styles.css') }}" />
@endsection

@section('content')
<div class=" col-12 pages-content">
    <div class="card">
        <div class="card-body">
            @include('multiauth::message')
            <div class="row justify-content-between page-row">
                <div class="col-sm">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addDocuments">
                        @lang('Add document')
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
                            <th>@lang('Name')</th>
                            <th>@lang('Email')</th>
                            <th>@lang("Driver's name")</th>
                            <th>@lang('Date')</th>
                            <th>@lang('Download')</th>
                            <th>@lang('Read')</th>
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
                <h5 class="modal-title" id="documentLabel">@lang("Add document")</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <label class="small-alert">@lang("È possibile effettuare l'upload di 1 pdf OPPURE di molteplici
                            foto.")</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label class="small-alert">@lang("Nel caso si scelga di usare delle foto si consiglia di
                            abbassare la qualità delle foto dalle
                            impostazioni della
                            fotocamera, perché questo velocizzerà il processo di upload delle foto.")</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label class="small-alert">@lang("Prima di premere su Conferma attendere che tutti gli upload
                            siano
                            completati.")</label>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label>@lang("Compilare il form sottostante per effettuare l'upload dei documenti condivisi con
                            l'autista"):</label>
                    </div>
                </div>
                <div id="form-result"></div>
                <div class="form-group">
                    <select id="format" class="form-control" name="format" required>
                        <option value="" disabled selected>@lang("Select file format")</option>
                        <option value="pdf">@lang("PDF")</option>
                        <option value="photos">@lang("Photos")</option>
                    </select>
                </div>
                <form id="document-pdf" class="hidden" action="{{ route('api.document.store.pdf') }}" method="POST">
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
                        <input type="text" class="form-control" name="name" placeholder='@lang("Document name")'
                            required>
                    </div>

                    <x-media-library-attachment rules="mimes:pdf|max:5000" name="pdf" />

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
                <form id="document-photos" class="hidden" action="{{ route('api.document.store.photos') }}"
                    method="POST">
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
                        <input type="text" class="form-control" name="name" placeholder='@lang("Document name")'
                            required>
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
                    <input type="hidden" name="id" id="id_document">

                    <div class="form-group">
                        <select class="form-control" id="email" name="user" required>
                            <option value="" disabled selected>@lang("Select driver")</option>
                            @foreach ($users as $user)
                            <option value="{{ $user->email }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" class="form-control" id="name" name="name"
                            placeholder='@lang("Document name")' required>
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
    $(document).ready(function() {
        // FADE OUT DEI MESSAGGI DAI CONTROLLER

        $('.message').delay(4000).fadeOut();

        // DATATABLE

        var table = $('#datatable').DataTable({
            "order": [
                [4, "desc"]
            ],
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "@lang('All')"]
            ],
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('api.documents') }}",
            "columns": [
                {
                    data: 'id',
                    name: 'id'
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
                    data: 'user_name',
                    name: 'user_name'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    "className": 'document-download',
                    "orderable": false,
                    "data": 'id',
                    "width": '1%',
                    "defaultContent": '',
                    "render": function(data, type, row) {
                        var html = "";

                        html += `<a href="{{ route('api.document.download') }}/` +
                            data + `"` +
                            ` class="btn btn-secondary btn-sm btn-download">` +
                            `<i class="fas fa-file-download"></i></a>`;

                        return html;
                    },
                },
                {
                    className: 'icon-read',
                    searchable: false,
                    orderable: false,
                    data: 'read',
                    "width": '1%',
                    "render": function(data, type, row) {
                        var html = "";

                        if (data == true) {
                            html += '<i class="fas fa-check"></i>';
                        } else {
                            html += '<i class="fas fa-times"></i>';
                        }

                        return html;
                    },
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
                'targets': 3,
                'width': '1%'
            }],
            "language": {
                "url": language,
            },
            "responsive": true,
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

        // ADD DOCUMENT

        $('#format').on('change', function(event) {
            switch ($(this).val()) {
                case "pdf":
                    $("#document-pdf").removeClass("hidden");
                    $("#document-photos").addClass("hidden");
                    break;

                case "photos":
                    $("#document-photos").removeClass("hidden");
                    $("#document-pdf").addClass("hidden");
                    break;

                default:
                    $("#upload").prop("disabled", true);
                    break;
            }
        });

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
                url: '{{ route("api.documents.delete") }}',
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
                $('#id_user').val(rows_selected[0]);
                var row = table.row('#' + rows_selected[0]).data();

                $('#email').val(row['user_email']);
                $('#name').val(row['name']);
                $('#id_document').val(row['id']);
            }

        });

        $('#edit-document').on('submit', function(event) {
            if(!confirm("@lang('Are you sure?')")) return;

            event.preventDefault();
            var form = $(this).closest('form');

            $('#form-result-edit').text('');
            $('#form-result-edit').fadeIn();

            $.ajax({
                url: "{{route('api.documents.edit')}}",
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
                        $('#edit-document')[0].reset();
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
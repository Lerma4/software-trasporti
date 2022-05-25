@extends('layouts.app')

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
            <div class="row">
                <div class="col">
                    <button type="button" class="btn btn-primary btn-block" data-toggle="modal"
                        data-target="#addDocuments">
                        @lang('Add document')
                    </button>
                </div>
            </div>

            <br>
            <div class="table-responsive ">
                <table cellspacing="0" class="table table-bordered nowrap" id="datatable" width="100%">
                    <thead>
                        <tr>
                            <th>@lang('Download')</th>
                            <th>@lang('Name')</th>
                            <th>@lang('Date')</th>
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
                        <label class="small-alert">@lang("Si consiglia di abbassare la qualità delle foto dalle
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
                        <label>@lang('Selezionare le foto necessari e scegliere un nome (gli spazi nel
                            nome verranno eliminati)'):</label>
                    </div>
                </div>
                <div class="form-group">
                    <select id="format" class="form-control" name="format" required>
                        <option value="" disabled selected>@lang("Select file format")</option>
                        <option value="pdf">@lang("PDF")</option>
                        <option value="photos">@lang("Photos")</option>
                    </select>
                </div>
                <div id="form-result"></div>
                <form id="document-pdf" class="hidden" action="{{ route('document.store.pdf') }}" method="POST">
                    @csrf
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
                <form id="document-photos" class="hidden" action="{{ route('document.store.photos') }}" method="POST">
                    @csrf
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

        // DATATABLE

        var table = $('#datatable').DataTable({
            "order": [
                [2, "desc"]
            ],
            "bInfo": false, // hide showing entries
            "paging": false,
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('getDocumentsSent') }}",
            "columns": [{
                    "className": 'document-download',
                    "orderable": false,
                    "data": 'id',
                    "width": '1%',
                    "defaultContent": '',
                    "render": function(data, type, row) {
                        var html = "";

                        html += `<a href="{{ route('document.download') }}/` +
                            data + `"` +
                            ` class="btn btn-secondary btn-sm btn-download">` +
                            `<i class="fas fa-file-download"></i>` +
                            '</a>';

                        if (row.read == false) {
                            html += '<i class="fas fa-exclamation-triangle fa-lg alert-expiration"></i>';
                        }

                        return html;
                    },
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'read',
                    name: 'read',
                    visible: false
                }
            ],
            "columnDefs": [{
                'targets': 0,
                'width': '1%'
            }],
            "language": {
                "url": language,
            },
            "responsive": true,
        });

        $(document.body).on('click', '.btn-download', function() {
            setTimeout(
                function() {
                    $('#datatable').DataTable().ajax.reload();
                }, 1000);
        });
    });
</script>
@endsection
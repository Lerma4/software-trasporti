@extends('layouts.app')

@section('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.bootstrap4.min.css">
<link type="text/css" href="//gyrocode.github.io/jquery-datatables-checkboxes/1.2.12/css/dataTables.checkboxes.css"
    rel="stylesheet" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/file-uploader/5.16.2/fine-uploader.min.css"
    integrity="sha512-RIjvm40hf5zylq2bAzo6gq7zle9d02ivUUrIB9FjBZYd2N87P9VcKoSufenZp5NSMR47IjvG1R2g3EnQ0qEjYA=="
    crossorigin="anonymous" />
@endsection

@section('content')
<div class="col-12 pages-content">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addDocuments">
                        @lang('Add documents')
                    </button>
                </div>
            </div>

            <br>
            <div class="table-responsive">
                <table cellspacing="0" class="table table-bordered nowrap" id="datatable" width="100%">
                    <thead>
                        <tr>
                            <th>@lang('Name')</th>
                            <th>@lang('Date')</th>
                            <th>@lang('Download')</th>
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
                <h5 class="modal-title" id="documentLabel">Modal title</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col">
                        <label>@lang('Selezionare le foto necessari e scegliere un nome (gli spazi nel
                            nome verranno eliminati):')</label>
                    </div>
                </div>
                <div id="form-result"></div>
                <form id="document" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <input type="text" class="form-control" name="name" placeholder="@lang('Name')" required>
                    </div>
                    <div class="form-group">
                        <input type="file" id="fileupload" name="photos[]" class="form-control-file">
                    </div>
                    <div id="files_list"></div>
                    <p id="loading"></p>
                    <input type="hidden" name="file_ids" id="file_ids" value="">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">@lang('Close')</button>
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

<!-- LIBRERIA PER COMPRIMERE FILE PRIMA DELL'UPLOAD -->

<script src="{{ asset('js/fileupload/vendor/jquery.ui.widget.js') }}" defer></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="https://blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js" defer></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="https://blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js" defer></script>
<script src="{{ asset('js/fileupload/jquery.iframe-transport.js') }}" defer></script>
<script src="{{ asset('js/fileupload/jquery.fileupload.js') }}" defer></script>
<script src="{{ asset('js/fileupload/jquery.fileupload-process.js') }}" defer></script>
<script src="{{ asset('js/fileupload/jquery.fileupload-image.js') }}" defer></script>

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
    $(function () {
        $('#fileupload').fileupload({
            url: "{{ route('document.upload') }}",
            dataType: 'json',
            // Enable image resizing, except for Android and Opera,
            // which actually support image resizing, but fail to
            // send Blob objects via XHR requests:
            disableImageResize: /Android(?!.*Chrome)|Opera/
                .test(window.navigator && navigator.userAgent),
            imageForceResize: true,
            imageCrop: true, // Force cropped images
            imageQuality: 0.3,
            add: function (e, data) {
                $('#loading').text('Uploading...');
                data.submit();
            },
            done: function (e, data) {
                $.each(data.result.files, function (index, file) {
                    $('<p/>').html(file.name + ' (' + file.size + ' KB)').appendTo($('#files_list'));
                    if ($('#file_ids').val() != '') {
                        $('#file_ids').val($('#file_ids').val() + ',');
                    }
                    $('#file_ids').val($('#file_ids').val() + file.fileID);
                });
                $('#loading').text('');
            }
        });
    });

    $(document).ready(function() {
        // FILES INPUT

        /*$(".btn-add").click(function() {
            var html = $(".clone").html();
            if ($(".decrease").length === 1) {
                $(".increment").after(html);
            } else {
                $(".decrease").last().after(html);
            }
        });

        $("body").on("click", ".btn-delete", function() {
            $(this).parents(".row").remove();
        });*/

        // DATATABLE

        var table = $('#datatable').DataTable({
            "order": [
                [1, "asc"]
            ],
            "lengthMenu": [
                [10, 25, 50, -1],
                [10, 25, 50, "@lang('All')"]
            ],
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('getDocuments') }}",
            "columns": [{
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'id',
                    name: 'id'
                }
            ],
            "columnDefs": [{
                'targets': 2,
                'width': '1%'
            }],
            "language": {
                "url": language,
            },
            "responsive": true,
        });

        // ADD DOCUMENT

        $('#document').on('submit', function(event) {
            event.preventDefault();
            var form = $(this).closest('form');
            var formData = new FormData(this);

            $('#form-result').text('');
            $('#form-result').fadeIn();

            $.ajax({
                url: "{{route('document.store')}}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
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
                        $('#document')[0].reset();
                        table.ajax.reload();
                    }
                    $(window).scrollTop(0);
                    $('#form-result').html(html);
                    $('#form-result').delay(4000).fadeOut();
                },
                complete: function() {
                    $('.loader-submit').addClass('hidden');
                    $('.submit-document').contents().last().replaceWith('@lang("Add document")');
                },
            });
        });
    });
</script>
@endsection

@extends('layouts.app')

@section('styles')
<link rel="stylesheet" type="text/css" href="{{asset('jqueryui/jquery-ui.min.css')}}">
@endsection

@section('content')
<div class="col-12 pages-content">
    <div class="card">
        <div class="card-body">

            <div id="form-result"></div>

            <form id="document" enctype="multipart/form-data">
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
                        <option value="{{ $plate->plate }}">{{ $plate->plate }}</option>
                        @endforeach
                    </select>
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
                    <label for="description">@lang('Incident description'):</label>
                    <textarea class="form-control" name="description" rows="3" maxlength="200" minlength="10"
                        required></textarea>
                </div>

                <label>@lang("Incident photos"):</label>
                <div id="drop">
                    @lang("Drop here")
                    <a>@lang("Browse")</a>
                    <input id="upload" type="file" name="upl[]" accept="image/*" multiple />
                </div>

                <ul class="uploads-list">
                    <!-- The file uploads will be shown here -->
                </ul>

                <input type="hidden" name="file_ids" id="file_ids" value="">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary submit-document">
                        <span class="spinner-border spinner-border-sm loader-submit hidden" role="status"
                            aria-hidden="true"></span>
                        @lang('Submit')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script src="{{ asset('js/fileupload/vendor/jquery.ui.widget.js') }}" defer></script>
<script src="{{ asset('js/fileupload/jquery.iframe-transport.js') }}" defer></script>
<script src="{{ asset('js/fileupload/jquery.fileupload.js') }}" defer></script>
<script src="{{ asset('js/fileupload/jquery.fileupload-process.js') }}" defer></script>
<script src="{{ asset('js/fileupload/jquery.fileupload-image.js') }}" defer></script>

<script src="{{ asset('js/knob/jquery.knob.js') }}" defer></script>

<script>
    $(function(){

        var ul = $('#document ul');

        $('#drop a').click(function(){
            // Simulate a click on the file input button
            // to show the file browser dialog
            $(this).parent().find('input').click();
        });

        // Initialize the jQuery File Upload plugin
        $('#upload').fileupload({
            headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
            url: "{{ route('crash.upload') }}",
            // This element will accept file drag/drop uploading
            dropZone: $('#drop'),

            // This function is called when a file is added to the queue;
            // either via the browse button, or via drag/drop:
            add: function (e, data) {

                var tpl = $('<li class="working"><input type="text" value="0" data-width="48" data-height="48"'+
                    ' data-fgColor="#0788a5" data-readOnly="1" data-bgColor="#3e4043" /><p></p><span></span></li>');

                // Append the file name and file size
                tpl.find('p').text(data.files[0].name)
                            .append('<i>' + formatFileSize(data.files[0].size) + '</i>');

                // Add the HTML to the UL element
                data.context = tpl.appendTo(ul);

                // Initialize the knob plugin
                tpl.find('input').knob();

                // Listen for clicks on the cancel icon
                tpl.find('span').click(function(){

                    if(tpl.hasClass('working')){
                        jqXHR.abort();
                    }

                    tpl.fadeOut(function(){
                        tpl.remove();
                    });

                });

                // Automatically upload the file once it is added to the queue
                var jqXHR = data.submit();
            },
            progress: function(e, data){

                // Calculate the completion percentage of the upload
                var progress = parseInt(data.loaded / data.total * 100, 10);

                // Update the hidden input field and trigger a change
                // so that the jQuery knob plugin knows to update the dial
                data.context.find('input').val(progress).change();

                if(progress == 100){
                    data.context.removeClass('working');
                }
            },

            progressServerRate: 0.5,
            progressServerDecayExp:3.5,

            done: function (e, data) {
                $.each(data.result.files, function (index, file) {
                    $('<p/>').html(file.name + ' (' + file.size + ' KB)').appendTo($('#files_list'));
                    if ($('#file_ids').val() != '') {
                        $('#file_ids').val($('#file_ids').val() + ',');
                    }
                    $('#file_ids').val($('#file_ids').val() + file.fileID);
                });
                $('#loading').text('');
            },

            fail:function(e, data){
                // Something has gone wrong!
                data.context.addClass('error');
            }

        });

        // Prevent the default action when a file is dropped on the window
        $(document).on('drop dragover', function (e) {
            e.preventDefault();
        });

        // Helper function that formats the file sizes
        function formatFileSize(bytes) {
            if (typeof bytes !== 'number') {
                return '';
            }

            if (bytes >= 1000000000) {
                return (bytes / 1000000000).toFixed(2) + ' GB';
            }

            if (bytes >= 1000000) {
                return (bytes / 1000000).toFixed(2) + ' MB';
            }

            return (bytes / 1000).toFixed(2) + ' KB';
        }

    });

    $(document).ready(function() {
        $('#document').on('submit', function(event) {
            event.preventDefault();
            var form = $(this).closest('form');
            var formData = new FormData(this);

            $('#form-result').text('');
            $('#form-result').fadeIn();

            $.ajax({
                url: "{{route('crash.store')}}",
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
                        $(".uploads-list").children().remove();
                        $('#file_ids').val("");
                    }
                    $('html, body').animate({
                        scrollTop: 0
                    }, 'fast');
                    $('#form-result').html(html);
                    $('#form-result').delay(4000).fadeOut();
                },
                complete: function() {
                    $('.loader-submit').addClass('hidden');
                    $('.submit-document').contents().last().replaceWith('@lang("Submit")');
                },
            });
        });
    });
</script>

@endsection

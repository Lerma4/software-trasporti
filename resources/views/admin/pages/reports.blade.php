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

            <div class="row">
                <div class="col-sm">
                    <button type="button" class="btn btn-block mb-col btn-danger" id="btn-delete" disabled>
                        @lang('Delete')
                    </button>
                </div>
            </div>

            <br>

            <div class="table-responsive">
                <table cellspacing="0" class="table table-bordered" id="datatable" width="100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>@lang('Date')</th>
                            <th>@lang('Vehicle\'s plate')</th>
                            <th>@lang('Driver')</th>
                            <th>@lang('Message')</th>
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

<div class="modal fade" id="modal-show" data-backdrop="static" data-keyboard="false" tabindex="-1"
    aria-labelledby="modal-label-add" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Report')</h5>
            </div>
            <div class="modal-body">
                @csrf
                <div class="form-group">
                    <label for="name">@lang('Date')</label>
                    <input disabled type="date" class="form-control" id="date" value="{{ old('nome') }}" name="name"
                        required>
                </div>
                <div class="form-group">
                    <label for="name">@lang('Truck')</label>
                    <input disabled type="text" class="form-control" id="truck" value="{{ old('nome') }}" name="name"
                        required>
                </div>
                <div class="form-group">
                    <label for="name">@lang('Driver')</label>
                    <input disabled type="text" class="form-control" id="driver" value="{{ old('nome') }}" name="name"
                        required>
                </div>
                <div class="form-group">
                    <label for="name">@lang('Message')</label>
                    <textarea disabled class="form-control" id="text" name="text" rows="5"></textarea>
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
    function formatInternational(date) {
        var dateAr = date.split('-');
        var newDate = dateAr[2] + '-' + dateAr[1] + '-' + dateAr[0];
        return newDate;
    }

    $(document).ready(function() {

        var table = $('#datatable').DataTable({
            "dom": '<"row justify-content-between table-row"<"col-sm table-col"l><"col-sm-auto"f>>rtip',
            "order": [
                [1, "asc"]
            ],
            buttons: {
                buttons: [{
                        extend: 'excelHtml5',
                        className: 'btn btn-success',
                        exportOptions: {
                            columns: [1, 2]
                        },
                        text: '@lang("Export EXCEL")'
                    },
                    {
                        extend: 'pdfHtml5',
                        className: 'btn btn-danger',
                        exportOptions: {
                            columns: [1, 2]
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
            "ajax": "{{ route('api.reports') }}",
            "columns": [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'created_at',
                    name: 'created_at'
                },
                {
                    data: 'truck.plate'
                },
                {
                    "data": 'user',
                    "name": 'user.name',
                    "render": function(data, type, row) {
                        var html = "";
                        
                        html = data.name + ' (' + data.email + ')';
                        
                        return html;
                    },
                },
                {
                    "className": "btn-open",
                    'width': '1%',
                    "data": 'text',
                    "serachable": false,
                    "render": function(data, type, row) {
                        var html = "";

                        html = '<button type="button"'+
                                'class="btn btn-sm btn-success btn-open" data-toggle="modal" data-target="#modal-show">' +
                                '<i class="fas fa-plus"></i>';

                        if (row.read == 0) {
                            html += '<span class="ml-2 badge badge-warning"><i class="fas fa-exclamation-triangle"></i></span>';
                        }

                        html += '</button>';

                        return html;
                    },
                },
                {
                    "data": 'user',
                    "name": 'user.email',
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
                    'targets': [2, 3, 4] ,
                    "orderable": false,
                },
                {
                    'targets': 5,
                    "visible": false
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

        $('#datatable tbody').on('click', '.btn-open', function() {
                
                var tr = $(this).closest('tr');
                var row = table.row(tr);
                var row = row.data();

                var truck = row['truck'];
                var user = row['user'];
                
                $('#modal-show').find('#date').val(formatInternational(row['created_at']));
                $('#modal-show').find('#truck').val(truck.plate);
                $('#modal-show').find('#driver').val(user.name);
                $('#modal-show').find('#text').val(row['text']);
        });

        $('#datatable').on('draw.dt', function() {
            table.column(0).checkboxes.deselectAll();
            $('#btn-delete').prop('disabled', true);
        }); // DESELEZIONA LE CHECKBOX E I BUTTONS EDIT E DELETE

        $('#datatable').change(function() {
            switch ((table.column(0).checkboxes.selected().count())) {
                case 0:
                    $('#btn-delete').prop('disabled', true);
                    break;
                case 1:
                    $('#btn-delete').prop('disabled', false);
                    break;
                default:
                    $('#btn-delete').prop('disabled', false);
                    break;
            }
        }); // DESELEZIONA I BUTTONS EDIT E DELETE

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
                url: '{{ route("api.reports.delete") }}',
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    reports: id
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

        $('#datatable tbody').on( 'click', '.btn-open', function () {
        var d = table.row( this.closest('tr') ).data();
        
        if (d.read == 0) {
            $.ajax({
                url: '{{ route("api.reports.read") }}',
                type: "POST",
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: d.id
                },
                success: function(data) {
                    table.draw();
                }
            });
        }
        
        } );

    });
</script>
@endsection
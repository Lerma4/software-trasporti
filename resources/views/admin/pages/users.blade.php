@extends('multiauth::layouts.admin')

@section('styles')
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.22/css/dataTables.bootstrap4.min.css">
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
                <div class="form-group">
                    <label>@lang('Search for group'):</label>
                    <select class="form-control select-input" data-column="2">
                        <option default value="">@lang('All')</option>
                        @foreach ($groups as $group)
                        <option value="{{$group->name}}">{{$group->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped table-bordered display responsive nowrap" id="datatable" width="100%">
                    <thead>
                        <tr>
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
@endsection

@section('scripts')
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js" defer></script>

<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js" defer></script>

<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.4/js/dataTables.buttons.min.js" defer></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" defer></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" defer></script>
<script type="text/javascript" language="javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" defer></script>
<script type="text/javascript" language="javascript" src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.html5.min.js" defer></script>

<script>
    $(document).ready(function() {

        var language;

        switch (language) {
            case "it":
                language = "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Italian.json";
                break;

            case "en":
                language = "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/English.json";
                break;

            default:
                language = "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/English.json";
                break;
        }

        var table = $('#datatable').DataTable({
            dom: 'lBfrtip',
            "order": [
                [0, "asc"]
            ],
            buttons: {
                buttons: [{
                        extend: 'excelHtml5',
                        className: 'btn btn-success',
                        exportOptions: {
                            columns: [0, 1, 2]
                        }
                    },
                    {
                        extend: 'pdfHtml5',
                        className: 'btn btn-danger',
                        exportOptions: {
                            columns: [0, 1, 2]
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
                    "targets": 3,
                    "orderable": false,
                    "searchable": false,
                    "width": "5%",
                },
                {
                    "targets": 4,
                    "orderable": false,
                    "searchable": false,
                    "width": "5%",
                }
            ],
            "language": {
                "url": language
            },
            "responsive": true,
            //"scrollX": true,
        });

        $('.select-input').change(function() {
            table.column($(this).data('column'))
                .search($(this).val())
                .draw();
        });

    });
</script>
@endsection
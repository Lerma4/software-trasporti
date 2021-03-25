<script>
    $(document).ready(function() {

        // DATATABLES

        var table = $('#maint_already').DataTable({
            "dom": '<"row justify-content-between table-row"<"col-sm table-col"lB><"col-sm-auto"f>>rtip',
            "order": [
                [1, "desc"]
            ],
            buttons: {
                buttons: [{
                        extend: 'excelHtml5',
                        className: 'btn btn-success',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 7, 8]
                        },
                        text: '@lang("Export EXCEL")'
                    }, // NON FUNZIONA (PER ORA) CON I NUMERI
                    {
                        extend: 'pdfHtml5',
                        className: 'btn btn-danger',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5, 6, 7, 8]
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
            "ajax": "{{ route('api.maint') }}",
            "columns": [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'date',
                    name: 'date'
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
                    data: 'km',
                    name: 'km'
                },
                {
                    data: 'garage',
                    name: 'garage'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'notes',
                    name: 'notes'
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
                    'targets': [4, 5, 6, 7],
                    "orderable": false,
                },
                {
                    "searchable": false,
                    "targets": [1, 4, 6, 7]
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

        $('#maint_already').on('draw.dt', function() {
            table.column(0).checkboxes.deselectAll();
            $('#btn-delete').prop('disabled', true);
            $('#btn-edit').prop('disabled', true);
        }); // DESELEZIONA LE CHECKBOX E I BUTTONS EDIT E DELETE

        $('#maint_already').change(function() {
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

        $('.btn-close').on('click', function(event) {
            $('#editMaint')[0].reset();
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
                url: '{{ route("admin.maint.delete") }}',
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    maint: id
                },
                success: function(data) {
                    var html = '';
                    $('#maint_already').DataTable().ajax.reload();
                    $('html, body').animate({
                        scrollTop: 0
                    }, 'fast');
                    html = '<div class="alert alert-success">' + data.success + '</div>';
                    $('#message-success').html(html);
                    $('#message-success').delay(4000).fadeOut();
                }
            });
        });

        $('#addMaint').on('submit', function(event) {
            event.preventDefault();

            var result = $(this).find(".form-result");

            result.text('');
            result.show();

            var form = $(this).closest('form');
            var url = '{{ route("admin.maint.store") }}';

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
                        $('#addMaint')[0].reset();
                        $('#maint_already').DataTable().ajax.reload();
                    }
                    result.html(html);
                    result.delay(4000).fadeOut();
                },
                complete: function() {
                    $('.loader-submit').addClass('hidden');
                    $('.submit').contents().last().replaceWith('@lang("Submit")');
                },
            });
        });

        $('#btn-edit').on('click', function(e) {

            var rows_selected = table.column(0).checkboxes.selected();

            var form = $("#editMaint");

            if (rows_selected.length == 1) {
                var row = table.row('#' + rows_selected[0]).data();

                form.find('.maint-id').val(row['id']);
                form.find('.maint-plate').val(row['plate']);
                form.find('.maint-type').val(row['type']);
                form.find('.maint-date').val(formatInternational(row['date']));
                form.find('.maint-garage').val(row['garage']);
                form.find('.maint-price').val(row['price']);
                form.find('.maint-km').val(row['km']);
                form.find('.maint-notes').val(row['notes']);
            }
        });

        $('#editMaint').on('submit', function(event) {
            event.preventDefault();

            var result = $(this).find(".form-result");

            result.text('');
            result.show();

            var form = $(this).closest('form');
            var url = '{{ route("admin.maint.edit") }}';

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
                        $('#maint_already').DataTable().ajax.reload();
                    }
                    result.html(html);
                    result.delay(4000).fadeOut();
                },
                complete: function() {
                    $('.loader-submit').addClass('hidden');
                    $('.submit').contents().last().replaceWith('@lang("Submit")');
                },
            });
        });

        $('.select-input-date-to').change(function() {
            var url = "{{ route('api.maint') }}";

            if ($(this).val() == '') {
                var date = 'nullValue';
            } else {
                var date = $(this).val();
            }
            url += "/" + date + "/" + $('.select-input-date-from').val();

            table.ajax.url(url).load();
        }); // FILTRO PER DATA (TO)

        $('.select-input-date-from').change(function() {
            var url = "{{ route('api.maint') }}";

            if ($('.select-input-date-to').val() == '') {
                var date = 'nullValue';
            } else {
                var date = $('.select-input-date-to').val();
            }
            url += "/" + date + "/" + $(this).val();

            table.ajax.url(url).load();
        }); // FILTRO PER DATA (FROM)

        $('#btn-reset').click(function(e) {
            table.ajax.url("{{ route('api.maint') }}").load();
        }) //RESET DELLE DATE

    });
</script>

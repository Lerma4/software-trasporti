<script>
    $(document).ready(function() {

        // DATATABLES

        var tableStill = $('#maint_still').DataTable({
            "dom": '<"row justify-content-between table-row"<"col-sm table-col"lB><"col-sm-auto"f>>rtip',
            "order": [
                [1, "asc"]
            ],
            buttons: {
                buttons: [{
                        extend: 'excelHtml5',
                        className: 'btn btn-success',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5]
                        },
                        text: '@lang("Export EXCEL")'
                    }, // NON FUNZIONA (PER ORA) CON I NUMERI
                    {
                        extend: 'pdfHtml5',
                        className: 'btn btn-danger',
                        exportOptions: {
                            columns: [1, 2, 3, 4, 5]
                        },
                        text: '@lang("Export PDF")',
                        customize: function(doc) {
                            doc.content[1].tableStill.widths =
                                Array(doc.content[1].tableStill.body[0].length + 1).join('*').split('');
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
            "ajax": "{{ route('api.maintStill') }}",
            "columns": [{
                    data: 'id',
                    name: 'id'
                },
                {
                    data: 'km',
                    name: 'km'
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
                    data: 'renew',
                    name: 'renew'
                },
                {
                    data: 'notes',
                    name: 'notes'
                },
                {
                    "className": 'maintDone',
                    "orderable": false,
                    "data": 'id',
                    "width": '1%',
                    "defaultContent": '',
                    "render": function(data, type, row) {
                        var html = "";

                        html += `<button type="button" class="btn btn-success btn-sm btn-maintDone"`+
                            `data-toggle="modal" data-target="#modal-maintDone">` +
                            `<i class="fas fa-check"></i></button>`;

                        return html;
                    },
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
                    'targets': [4, 5],
                    "orderable": false,
                },
                {
                    "searchable": false,
                    "targets": [4, 5]
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

        $('#maint_still').on('draw.dt', function() {
            tableStill.column(0).checkboxes.deselectAll();
            $('#btn-delete-stillToDo').prop('disabled', true);
            $('#btn-edit-stillToDo').prop('disabled', true);
        }); // DESELEZIONA LE CHECKBOX E I BUTTONS EDIT E DELETE

        $('#maint_still').change(function() {
            switch ((tableStill.column(0).checkboxes.selected().count())) {
                case 0:
                    $('#btn-delete-stillToDo').prop('disabled', true);
                    $('#btn-edit-stillToDo').prop('disabled', true);
                    break;
                case 1:
                    $('#btn-delete-stillToDo').prop('disabled', false);
                    $('#btn-edit-stillToDo').prop('disabled', false);
                    break;
                default:
                    $('#btn-delete-stillToDo').prop('disabled', false);
                    $('#btn-edit-stillToDo').prop('disabled', true);
                    break;
            }
        }); // DESELEZIONA I BUTTONS EDIT E DELETE

        $('.btn-close').on('click', function(event) {
            $('#editMaint-stillToDo')[0].reset();
        });

        $('#btn-delete-stillToDo').on('click', function(e) {

            var rows_selected = tableStill.column(0).checkboxes.selected();
            var id = [];

            $.each(rows_selected, function(index, rowId) {
                id[index] = rowId;
            });

            $('#message-success-stillToDo').text('');
            $('#message-success-stillToDo').show();

            $.ajax({
                url: '{{ route("admin.maintStill.delete") }}',
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    maint: id
                },
                success: function(data) {
                    var html = '';
                    $('#maint_still').DataTable().ajax.reload();
                    $('html, body').animate({
                        scrollTop: $("#message-success-stillToDo").offset().top
                    }, 'fast');
                    html = '<div class="alert alert-success">' + data.success + '</div>';
                    $('#message-success-stillToDo').html(html);
                    $('#message-success-stillToDo').delay(4000).fadeOut();
                }
            });
        });

        $('#addMaint-stillToDo').on('submit', function(event) {
            event.preventDefault();

            var result = $(this).find(".form-result");

            result.text('');
            result.show();

            var form = $(this).closest('form');
            var url = '{{ route("admin.maintStill.store") }}';

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
                        $('#addMaint-stillToDo')[0].reset();
                        $('#maint_still').DataTable().ajax.reload();
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

        $('#btn-edit-stillToDo').on('click', function(e) {

            var rows_selected = tableStill.column(0).checkboxes.selected();

            var form = $("#editMaint-stillToDo");

            if (rows_selected.length == 1) {
                var row = tableStill.row('#' + rows_selected[0]).data();

                form.find('.maint-id').val(row['id']);
                form.find('.maint-plate').val(row['plate']);
                form.find('.maint-type').val(row['type']);
                form.find('.maint-km').val(row['km']);
                form.find('.maint-renew').val(row['renew']);
                form.find('.maint-notes').val(row['notes']);
            }
        });

        $('#editMaint-stillToDo').on('submit', function(event) {
            event.preventDefault();

            var result = $(this).find(".form-result");

            result.text('');
            result.show();

            var form = $(this).closest('form');
            var url = '{{ route("admin.maintStill.edit") }}';

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
                        $('#maint_still').DataTable().ajax.reload();
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

        // CONFERMA DELL'ESECUZIONE DELLA MANUTENZIONE

        $('body').on('click', '.btn-maintDone', function(e) {

            var id = $(this).closest("tr").attr("id"),
                rowData = tableStill.row($(this).closest("tr")).data(),
                check = 0,
                i = 0;

            while (check == 0) {
                if (plates[i].plate == rowData['plate']) {
                    $("#confirm-km").val(plates[i].km);
                    check = 1;
                }
                i++;
            }

            $(".maintDone-id").val(id);

        });

        $('#maintDone').on('submit', function(event) {
            event.preventDefault();

            var result = $("#message-success");
            var err = $("#confirm-error");

            result.text('');
            result.show();

            err.text('');
            err.show();

            var form = $(this).closest('form');
            var url = '{{ route("admin.maintStill.confirm") }}';

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
                        err.html(html);
                        err.delay(4000).fadeOut();
                    }
                    if (data.success) {
                        html = '<div class="alert alert-success">' + data.success + '</div>';
                        $('#maint_still').DataTable().ajax.reload();
                        $('#maint_already').DataTable().ajax.reload();
                        // SOLUZIONE TEMPORANEA : USANDO I METODI TRADIZIONALI LA MODALE SCOPARE MA RIMANE LO SFONDO GRIGIO NON CLICCABILE
                        setTimeout(function(){
                            $('.btn-close-confirm').click();
                            $('.btn-close-confirm').click();
                        },500);
                        result.html(html);
                        result.delay(4000).fadeOut();
                        $('html, body').scrollTop(0);
                        }
                },
                complete: function() {
                    $('.loader-submit').addClass('hidden');
                    $('.submit').contents().last().replaceWith('@lang("Submit")');
                },
            });
        });

        $('.btn-close-confirm').on('click', function(event) {
            $('#maintDone')[0].reset();
        });

    });
</script>

@extends('multiauth::layouts.admin')

@section('styles')
<link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.css">
@endsection

@section('content')
<div class="col-12 pages-content">
    <div class="card">
        <div class="card-body">
            @include('multiauth::message')
            <table class="table table-bordered" id="datatable">
                <thead>
                    <tr>
                        <th>@lang('Name')</th>
                        <th>@lang('Email')</th>
                        <th>@lang('Group')</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.js" defer></script>
<script>
    $(document).ready(function() {
        $('#datatable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('api.users') }}",
            "columns": [{
                    "data": "name"
                },
                {
                    "data": "email"
                },
                {
                    "data": "group"
                }
            ]
        });
    });
</script>
@endsection
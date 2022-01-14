@extends('layouts.app')

@section('content')
<div class="col-12 pages-content">

    <div class="card">
        <div class="card-header">@lang('Change Password')</div>

        <div class="card-body">
            <div id="form-result"></div>
            <form id="change-psw">
                <div class="form-group row">
                    <label for="oldPassword" class="col-md-4 col-form-label text-md-right">{{ __('Old Password')
                        }}</label>

                    <div class="col-md-3">
                        <input id="oldPassword" type="password" class="form-control" name="oldPassword"
                            autocomplete="current_password" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('New Password') }}</label>

                    <div class="col-md-3">
                        <input id="password" type="password" class="form-control" name="password"
                            autocomplete="new_password" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-4 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                    <div class="col-md-3">
                        <input id="password-confirm" type="password" class="form-control" name="password_confirmation"
                            autocomplete="new_password" required>
                    </div>
                </div>

                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Change Password') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">@lang('Change Language')</div>

        <div class="card-body">
            <form method="POST" action="{{ route('lang') }}">
                @csrf

                <div class="form-group row">
                    <label class="col-md-4 col-form-label text-md-right">@lang("Select new language")</label>
                    <div class="col-md-3">
                        <select class="form-control" name="lang" required>
                            @if (auth()->user()->language == 'it')
                            <option value="it">@lang("Italian")</option>
                            <option value="en">@lang("English")</option>
                            @else
                            <option value="en">@lang("English")</option>
                            <option value="it">@lang("Italian")</option>
                            @endif
                        </select>
                    </div>
                </div>

                <div class="form-group row mb-0">
                    <div class="col-md-6 offset-md-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Change Language') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')

<script>
    $(document).ready(function() {
        $('#change-psw').on('submit', function(event) {

            event.preventDefault();
            var form = $(this).closest('form');

            $.ajax({
                url: "{{ route('psw.change') }}",
                type: "POST",
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: $(form).serialize(),
                dataType: "json",
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
                        $('#change-psw')[0].reset();
                    }
                    $('#form-result').html(html);
                    $('#form-result').css("display", "block");
                    $('#form-result').delay(4000).fadeOut();
                }
            });
        });
    });
</script>

@endsection
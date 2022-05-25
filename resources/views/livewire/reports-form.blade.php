<div>
    @if (session()->has('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
    @endif

    @if ($uploated == null)

    <form wire:submit.prevent="submit">

        <div class="form-group">
            <label for="truck">@lang('Vehicle\'s plate'):</label>

            <select wire:model="truck" class="form-control" required>
                <option value="" selected></option>
                @foreach($trucks as $truck)
                <option value="{{ $truck->id }}">{{ $truck->plate }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="text">@lang('Report\'s text'):</label>
            <textarea wire:model="text" class="form-control" cols="30" rows="10" minlength="5" maxlength="10000"
                required></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-block">
            @lang('Submit')
        </button>
    </form>

    @endif
</div>
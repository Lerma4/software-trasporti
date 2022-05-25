<div>
    @if (session()->has('message'))
    <div class="alert alert-success">
        {{ session('message') }}
    </div>
    @endif

    @if ($uploated == null)

    <div class="form-group">
        <label for="truck">@lang('Vehicle\'s plate'):</label>

        <select wire:model="selectedTruck" class="form-control" required>
            <option value="" selected></option>
            @foreach($trucks as $truck)
            <option value="{{ $truck->id }}">{{ $truck->plate }}</option>
            @endforeach
        </select>
    </div>

    <form wire:submit.prevent="submit">
        <div class="form-group">
            <label for="plate">@lang('Intervention performed'):</label>

            <select class="form-control" wire:model="maint_id" required>
                <option value="" selected></option>
                @if (isset($selectedTruck))
                @foreach($maint as $element)
                <option value="{{ $element->id }}">{{ $element->type }}</option>
                @endforeach
                @endif
            </select>
        </div>

        <div class="form-group">
            <label for="km">@lang("Truck's km"):</label>
            <input wire:model="km" type="number" step="1" min="0" class="form-control" required>
            <small>@lang('Km del mezzo al momento della manutenzione.')</small>
            <br>
            <small class="small-alert">@lang('Di default vengono inseriti i km del mezzo in questo
                momento.')</small>
        </div>

        <div class="form-group">
            <label for="date">@lang('Date'):</label>
            <input wire:model="date" type="date" class="form-control maint-date"
                max="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" required>
        </div>

        <div class="form-group">
            <label for="garage">@lang('Garage') (@lang('Optional')):</label>
            <input type="text" class="form-control maint-garage" wire:model="garage">
        </div>

        <div class="form-group">
            <label for="price">@lang('Price') (@lang('Optional')):</label>
            <input type="number" class="form-control maint-price" min="1" step="0.01" wire:model="price">
        </div>

        <div class="form-group">
            <label for="notes">@lang('Notes') (@lang('Optional')):</label>
            <input type="text" class="form-control maint-notes" maxlength="50" wire:model="notes">
            <small>@lang('Max 50 characters').</small>
        </div>

        <button type="submit" class="btn btn-primary btn-block">
            @lang('Submit')
        </button>
    </form>

    @endif
</div>
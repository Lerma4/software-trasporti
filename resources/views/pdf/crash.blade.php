<!DOCTYPE html>
<html>

<style>
    *,
    html {}


    img {
        position: relative;
        left: 0;
        top: 0;
        max-height: 100%;
        max-width: 100%;
    }
</style>

<body>
    <h3>@lang('Incident report')</h3>
    <br>
    <p>@lang('Date') : {{ \Carbon\Carbon::parse($crash->date)->format('d-m-Y') }}</p>
    <p>@lang('Name') : {{ $crash->name }}</p>
    <p>@lang('Email') : {{ $crash->email }}</p>
    <p>@lang('Plate') : {{ $crash->plate }}</p>
    @if ($crash->plate_s != '')
    <p>@lang('Plate semitrailer') : {{ $crash->plate_s }}</p>
    @endif
    <p>@lang('Description') : {{ $crash->description }}</p>

    <?php
    $dim = sizeof($data);   
    ?>
    @foreach ($data as $n => $img)
    <img src="{{ $img->getPath() }}">
    @if ($n != $dim-1)
    <div class="page-break"></div>
    @endif
    @endforeach
</body>

</html>
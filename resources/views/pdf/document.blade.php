<!DOCTYPE html>
<html>

<style>
    *,
    html {
        margin: 0;
        padding: 0;
    }


    img {
        position: relative;
        top: 0;
        left: 0;
        margin: 0;
        padding: 0;
        max-height: 100%;
        max-width: 100%;
    }
</style>

<body>
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
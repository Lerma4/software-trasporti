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
    @foreach ($data as $photo)
    <img src="{{ storage_path('app/photos') }}/{{ $photo->filename }}" alt="">
    @endforeach
</body>

</html>

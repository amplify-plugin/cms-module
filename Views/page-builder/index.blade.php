<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="{{ asset('page-builder/css/lib/grapes.min.css') }}">
        <link rel="stylesheet" href="{{ asset('page-builder/css/lib/grapesjs-preset-webpage.min.css') }}">
        <title>Page Builder</title>
    </head>
    <body>
        <div id="app">
            <page-builder></page-builder>
        </div>

        <script src="{{  asset("assets/js/app.js") }}"></script>
        <script src="{{ asset('page-builder/js/new.js') }}"></script>
        <script src="{{ asset('page-builder/js/lib/grapes.min.js') }}"></script>
        <script src="{{ asset('page-builder/js/lib/grapesjs-preset-webpage.min.js') }}"></script>
    </body>
</html>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width,initial-scale=1.0" />
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" type="image/png" sizes="16x16" href="/favicon.ico">
        <meta name="msapplication-TileColor" content="#161616">
        <meta name="theme-color" content="#ffffff">
        <link href="{{ mix('/css/app.css') }}" type="text/css" rel="stylesheet"/>
    </head>
    <body>
        <div id="app"></div>
        <script src="{{ mix("js/main.js") }}"></script>
    </body>
</html>

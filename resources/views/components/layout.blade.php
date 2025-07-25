<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    {{-- <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0"> --}}
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="ie=edge">
    <title>{{ $heading }}</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <script src="{{ asset('js/global.js') }}"></script>
</head>
<body>
    <x-navbar />
    <x-alert-success />
    <x-alert-error />
    {{ $slot }}    
</body>
</html>
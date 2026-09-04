<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Blog Integration - {{ $title ?? 'Test' }}</title>
    @if(($theme ?? 'bootstrap-5') === 'bootstrap-5')
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
            rel="stylesheet"
            crossorigin="anonymous"
        >
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
        <script>window.bootstrap = bootstrap;</script>
    @endif
    <style>
        html { scroll-behavior: auto !important; }
        body { font-family: sans-serif; padding: 2rem; }
        section { margin-bottom: 2rem; border: 1px solid #ccc; padding: 1rem; }
        .alert-success { color: green; border: 1px solid green; padding: 10px; margin-bottom: 20px; }
    </style>
</head>
<body>
<div class="container">
    @if(session('success'))
        <div class="alert alert-success" id="flash-success">
            {{ session('success') }}
        </div>
    @endif

    {{ $slot }}
</div>
</body>
</html>

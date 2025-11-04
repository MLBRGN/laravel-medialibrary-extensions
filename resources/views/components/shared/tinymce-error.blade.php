<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Media Manager Error</title>
    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            margin: 2rem;
            color: #333;
            background: #fafafa;
        }
        .error-box {
            background: #fff3f3;
            border: 1px solid #ffcccc;
            border-radius: 8px;
            padding: 1.5rem;
            max-width: 500px;
        }
        h1 {
            color: #c00;
            font-size: 1.25rem;
            margin-top: 0;
        }
        ul { margin: .5rem 0 0; padding-left: 1.25rem; }
    </style>
</head>
<body>
<div class="error-box">
    <h1>{{ $message ?? __('media-library-extensions::messages.validation_error') }}</h1>
    @if(!empty($errors))
        <ul>
            @foreach($errors as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif
</div>
</body>
</html>

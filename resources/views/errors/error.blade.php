<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? __('medialibrary-extensions::messages.media_manager_error') }}</title>

    <style>
        body {
            font-family: system-ui, -apple-system, sans-serif;
            margin: 2rem;
            color: #333;
            background: #fafafa;
        }

        .error-box {
            max-width: 700px;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1.5rem;
        }

        h1 {
            margin-top: 0;
            color: #c00;
            font-size: 1.5rem;
        }

        p {
            margin-bottom: 1rem;
        }

        ul {
            margin: .5rem 0 0;
            padding-left: 1.25rem;
        }

        table {
            margin-top: 1rem;
            border-collapse: collapse;
        }

        td {
            padding: .25rem .75rem .25rem 0;
            vertical-align: top;
        }

        td:first-child {
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="error-box">

    <h1>{{ $title ?? __('medialibrary-extensions::messages.media_manager_error') }}</h1>

    @isset($message)
        <p>{{ $message }}</p>
    @endisset

    @if(!empty($errors))
        <ul>
            @foreach($errors as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    @if(!empty($details))
        <table>
            @foreach($details as $label => $value)
                <tr>
                    <td>{{ $label }}</td>
                    <td>{{ $value }}</td>
                </tr>
            @endforeach
        </table>
    @endif

</div>

</body>
</html>
{{-- resources/views/errors/media-manager.blade.php --}}

    <!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>

    <style>
        body {
            font-family: system-ui, sans-serif;
            margin: 3rem;
            line-height: 1.5;
            color: #333;
        }

        .box {
            max-width: 700px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 1.5rem;
            background: #fafafa;
        }

        h1 {
            margin-top: 0;
        }

        table {
            margin-top: 1rem;
        }

        td:first-child {
            font-weight: 600;
            padding-right: 1rem;
        }
    </style>
</head>
<body>

<div class="box">
    <h1>{{ $title }}</h1>

    <p>{{ $message }}</p>

    @if(! empty($details))
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
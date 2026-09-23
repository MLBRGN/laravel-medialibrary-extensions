<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MLE Isolation Test</title>
    @if($theme === 'bootstrap-5')
        @if(app()->environment('testing'))
            <link href="/vendor/mlbrgn/bootstrap/css/bootstrap.min.css" rel="stylesheet">
        @else
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
        @endif
    @endif
</head>
<body>
<div class="container mt-5">
    <h1>MLE Isolation Test</h1>

    <form action="{{ route('mle-demo-isolation-submit') }}" method="post" id="isolation-form">
        @csrf
        <div class="mb-3">
            <label for="app_field" class="form-label">App Field</label>
            <input type="text" name="app_field" id="app_field" class="form-control" value="app-value">
        </div>

        <div class="mb-5">
            <h2>Manager A (Min 1)</h2>
            <x-mle-media-manager-multiple
                id="manager-a"
                model-reference="Mlbrgn\MediaLibraryExtensions\Models\demo\Alien"
                :collections="['image' => 'alien-multiple-images']"
                name="manager_a_count"
                :options="[
                    'theme' => $theme,
                    'dataSource' => $dataSource,
                    'useXhr' => $useXhr,
                    'minMediaCount' => 1
                ]"
                :data-source="$dataSource"
            />
        </div>

        <div class="mb-5">
            <h2>Manager B (Max 2)</h2>
            <x-mle-media-manager-multiple
                id="manager-b"
                model-reference="Mlbrgn\MediaLibraryExtensions\Models\demo\Alien"
                :collections="['image' => 'alien-multiple-images']"
                name="manager_b_count"
                :options="[
                    'theme' => $theme,
                    'dataSource' => $dataSource,
                    'useXhr' => $useXhr,
                    'maxMediaCount' => 2
                ]"
                :data-source="$dataSource"
            />
        </div>

        @if($media)
            <div class="mb-5">
                <h2>Media Lab</h2>
                <x-mle-media-lab
                    id="media-lab-a"
                    :media="$media"
                    name="media_lab_a"
                    :options="[
                        'theme' => $theme,
                        'dataSource' => $dataSource,
                        'useXhr' => $useXhr
                    ]"
                    :data-source="$dataSource"
                />
            </div>
        @endif

        <button type="submit" class="btn btn-primary" data-test="btn-submit-isolation">Submit Form</button>
    </form>

    @if(session('submitted_data'))
        <div id="submitted-results" class="mt-5">
            <h2>Submitted Data</h2>
            <pre data-test="submitted-data">@json(session('submitted_data'), JSON_PRETTY_PRINT)</pre>
        </div>
    @endif
    
    @if ($errors->any())
        <div class="alert alert-danger mt-3">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>

@if($theme === 'bootstrap-5')
    @if(app()->environment('testing'))
        <script src="/vendor/mlbrgn/bootstrap/js/bootstrap.bundle.min.js"></script>
    @else
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    @endif
@endif
@stack('scripts')
</body>
</html>

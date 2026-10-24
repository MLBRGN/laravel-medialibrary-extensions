<?php

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Config;
use Mlbrgn\MediaLibraryExtensions\View\Components\Partials\Status;
use Illuminate\View\ComponentAttributeBag;

beforeEach(function () {
    Config::set('medialibrary-extensions.frontend_theme', 'bootstrap-6');
});

//it('renders bootstrap-6 status with alert-success class', function () {
//    $baseId = 'media-manager-123';
//
//    $statusKey = status_session_prefix();
//    session()->put($statusKey, [
//        'base_id' => $baseId,
//        'type' => 'success',
//        'message' => 'Success Message',
//    ]);
//
//    $component = new Status(
//        id: $baseId,
//        options: ['theme' => 'bootstrap-6'],
//    );
//
//    $html = $component->render()->with([
//        'status' => $component->status,
//        'attributes' => new ComponentAttributeBag,
//    ])->render();
//
//    expect($html)->toContain('alert-success');
//    expect($html)->not()->toContain('theme-success');
//});
//
//it('renders bootstrap-6 media-modal with correct dialog structure', function () {
//    $model = $this->getModelWithMedia(['image' => 1]);
//
//    $html = Blade::render('<x-mle-media-modal
//                    id="test-modal"
//                    :model-reference="$model"
//                    :collections="[\'image_collection\']"
//                    theme="bootstrap-6"
//                    title="Test Modal Title"
//                />', [
//        'model' => $model,
//    ]);
//
//    expect($html)->toContain('<dialog')
//        ->and($html)->toContain('dialog-header')
//        ->and($html)->toContain('dialog-title')
//        ->and($html)->toContain('dialog-body')
//        ->and($html)->toContain('btn-close')
//        ->and($html)->toContain('data-bs-dismiss="dialog"')
//        ->and($html)->toContain('dialog-xl')
//        ->and($html)->toContain('dialog-centered')
//        ->and($html)->toContain('dialog-fullscreen-lg-down');
//});
//
//it('renders bootstrap-6 image-editor-modal with correct dialog structure', function () {
//    $model = $this->getModelWithMedia(['image' => 1]);
//    $medium = $model->getFirstMedia('image_collection');
//
//    $html = Blade::render('<x-mle-image-editor-modal
//                    id="test-editor"
//                    :model-reference="$model"
//                    :medium="$medium"
//                    :collections="[\'image_collection\']"
//                    :options="[]"
//                    theme="bootstrap-6"
//                    title="Editor Title"
//                />', [
//        'model' => $model,
//        'medium' => $medium,
//    ]);
//
//    expect($html)->toContain('<dialog')
//        ->and($html)->toContain('dialog-header')
//        ->and($html)->toContain('dialog-title')
//        ->and($html)->toContain('dialog-body')
//        ->and($html)->toContain('btn-close')
//        ->and($html)->toContain('data-bs-dismiss="dialog"')
//        ->and($html)->toContain('dialog-xl')
//        ->and($html)->toContain('dialog-centered');
//});
//
//it('renders bootstrap-6 image-editor-modal-temporary-upload with correct dialog structure', function () {
//    $model = $this->getModelWithMedia(['image' => 1]);
//    $medium = $model->getFirstMedia('image_collection');
//
//    $html = Blade::render('<x-mle-image-editor-modal
//                    id="test-editor-temp"
//                    :model-reference="$model"
//                    :medium="$medium"
//                    :collections="[\'image_collection\']"
//                    :options="[]"
//                    :temporary-upload-mode="true"
//                    theme="bootstrap-6"
//                    title="Temp Editor Title"
//                />', [
//        'model' => $model,
//        'medium' => $medium,
//    ]);
//
//    expect($html)->toContain('<dialog')
//        ->and($html)->toContain('dialog-header')
//        ->and($html)->toContain('dialog-title')
//        ->and($html)->toContain('dialog-body')
//        ->and($html)->toContain('btn-close')
//        ->and($html)->toContain('data-bs-dismiss="dialog"')
//        ->and($html)->toContain('dialog-xl')
//        ->and($html)->toContain('dialog-centered');
//});

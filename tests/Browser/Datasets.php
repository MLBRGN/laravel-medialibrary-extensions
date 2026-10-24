<?php

/** @noinspection InvalidDatasetNameCaseInspection */
dataset('mms_test_matrix', function () {
    $full = [
        'bootstrap 5 + demo default + xhr + permanent' => ['bootstrap-5', 'demo_default', true, 'permanent'],
        'bootstrap 5 + demo default + xhr + temporary' => ['bootstrap-5', 'demo_default', true, 'temporary'],
        'bootstrap 5 + demo default + no xhr + permanent' => ['bootstrap-5', 'demo_default', false, 'permanent'],
        'bootstrap 5 + demo default + no xhr + temporary' => ['bootstrap-5', 'demo_default', false, 'temporary'],

        'bootstrap 5 + demo alt + xhr + permanent' => ['bootstrap-5', 'demo_alt', true, 'permanent'],
        'bootstrap 5 + demo alt + xhr + temporary' => ['bootstrap-5', 'demo_alt', true, 'temporary'],
        'bootstrap 5 + demo alt + no xhr + permanent' => ['bootstrap-5', 'demo_alt', false, 'permanent'],
        'bootstrap 5 + demo alt + no xhr + temporary' => ['bootstrap-5', 'demo_alt', false, 'temporary'],

        'bootstrap 6 + demo default + xhr + permanent' => ['bootstrap-6', 'demo_default', true, 'permanent'],
        'bootstrap 6 + demo default + xhr + temporary' => ['bootstrap-6', 'demo_default', true, 'temporary'],
        'bootstrap 6 + demo default + no xhr + permanent' => ['bootstrap-6', 'demo_default', false, 'permanent'],
        'bootstrap 6 + demo default + no xhr + temporary' => ['bootstrap-6', 'demo_default', false, 'temporary'],

        'bootstrap 6 + demo alt + xhr + permanent' => ['bootstrap-6', 'demo_alt', true, 'permanent'],
        'bootstrap 6 + demo alt + xhr + temporary' => ['bootstrap-6', 'demo_alt', true, 'temporary'],
        'bootstrap 6 + demo alt + no xhr + permanent' => ['bootstrap-6', 'demo_alt', false, 'permanent'],
        'bootstrap 6 + demo alt + no xhr + temporary' => ['bootstrap-6', 'demo_alt', false, 'temporary'],

        'plain + demo default + xhr + permanent' => ['plain', 'demo_default', true, 'permanent'],
        'plain + demo default + xhr + temporary' => ['plain', 'demo_default', true, 'temporary'],
        'plain + demo default + no xhr + permanent' => ['plain', 'demo_default', false, 'permanent'],
        'plain + demo default + no xhr + temporary' => ['plain', 'demo_default', false, 'temporary'],

        'plain + demo alt + xhr + permanent' => ['plain', 'demo_alt', true, 'permanent'],
        'plain + demo alt + xhr + temporary' => ['plain', 'demo_alt', true, 'temporary'],
        'plain + demo alt + no xhr + permanent' => ['plain', 'demo_alt', false, 'permanent'],
        'plain + demo alt + no xhr + temporary' => ['plain', 'demo_alt', false, 'temporary'],
    ];

    if (getenv('PEST_BROWSER_FULL')) {
        return $full;
    }

    return [
        'bootstrap 5 + demo default + xhr + permanent' => $full['bootstrap 5 + demo default + xhr + permanent'],
        'bootstrap 6 + demo default + xhr + permanent' => $full['bootstrap 6 + demo default + xhr + permanent'],
        'bootstrap 5 + demo alt + no xhr + temporary' => $full['bootstrap 5 + demo alt + no xhr + temporary'],
        'plain + demo default + xhr + temporary' => $full['plain + demo default + xhr + temporary'],
        'plain + demo alt + no xhr + permanent' => $full['plain + demo alt + no xhr + permanent'],
    ];
});

dataset('blog_crud_matrix', [
    'bootstrap 5 + xhr' => ['bootstrap-5', true],
    'bootstrap 5 + no xhr' => ['bootstrap-5', false],
    'bootstrap 6 + xhr' => ['bootstrap-6', true],
    'bootstrap 6 + no xhr' => ['bootstrap-6', false],
    'plain + xhr' => ['plain', true],
    'plain + no xhr' => ['plain', false],
]);

dataset('mmm_test_matrix', function () {
    $full = [
        'bootstrap 5 + demo default + xhr + permanent' => ['bootstrap-5', 'demo_default', true, 'permanent'],
        'bootstrap 5 + demo default + xhr + temporary' => ['bootstrap-5', 'demo_default', true, 'temporary'],
        'bootstrap 5 + demo default + no xhr + permanent' => ['bootstrap-5', 'demo_default', false, 'permanent'],
        'bootstrap 5 + demo default + no xhr + temporary' => ['bootstrap-5', 'demo_default', false, 'temporary'],

        'bootstrap 5 + demo alt + xhr + permanent' => ['bootstrap-5', 'demo_alt', true, 'permanent'],
        'bootstrap 5 + demo alt + xhr + temporary' => ['bootstrap-5', 'demo_alt', true, 'temporary'],
        'bootstrap 5 + demo alt + no xhr + permanent' => ['bootstrap-5', 'demo_alt', false, 'permanent'],
        'bootstrap 5 + demo alt + no xhr + temporary' => ['bootstrap-5', 'demo_alt', false, 'temporary'],

        'bootstrap 6 + demo default + xhr + permanent' => ['bootstrap-6', 'demo_default', true, 'permanent'],
        'bootstrap 6 + demo default + xhr + temporary' => ['bootstrap-6', 'demo_default', true, 'temporary'],
        'bootstrap 6 + demo default + no xhr + permanent' => ['bootstrap-6', 'demo_default', false, 'permanent'],
        'bootstrap 6 + demo default + no xhr + temporary' => ['bootstrap-6', 'demo_default', false, 'temporary'],

        'bootstrap 6 + demo alt + xhr + permanent' => ['bootstrap-6', 'demo_alt', true, 'permanent'],
        'bootstrap 6 + demo alt + xhr + temporary' => ['bootstrap-6', 'demo_alt', true, 'temporary'],
        'bootstrap 6 + demo alt + no xhr + permanent' => ['bootstrap-6', 'demo_alt', false, 'permanent'],
        'bootstrap 6 + demo alt + no xhr + temporary' => ['bootstrap-6', 'demo_alt', false, 'temporary'],

        'plain + demo default + xhr + permanent' => ['plain', 'demo_default', true, 'permanent'],
        'plain + demo default + xhr + temporary' => ['plain', 'demo_default', true, 'temporary'],
        'plain + demo default + no xhr + permanent' => ['plain', 'demo_default', false, 'permanent'],
        'plain + demo default + no xhr + temporary' => ['plain', 'demo_default', false, 'temporary'],

        'plain + demo alt + xhr + permanent' => ['plain', 'demo_alt', true, 'permanent'],
        'plain + demo alt + xhr + temporary' => ['plain', 'demo_alt', true, 'temporary'],
        'plain + demo alt + no xhr + permanent' => ['plain', 'demo_alt', false, 'permanent'],
        'plain + demo alt + no xhr + temporary' => ['plain', 'demo_alt', false, 'temporary'],
    ];

    if (getenv('PEST_BROWSER_FULL')) {
        return $full;
    }

    return [
        'bootstrap 5 + demo default + xhr + permanent' => $full['bootstrap 5 + demo default + xhr + permanent'],
        'bootstrap 6 + demo default + xhr + permanent' => $full['bootstrap 6 + demo default + xhr + permanent'],
        'bootstrap 5 + demo alt + no xhr + temporary' => $full['bootstrap 5 + demo alt + no xhr + temporary'],
        'plain + demo default + xhr + temporary' => $full['plain + demo default + xhr + temporary'],
        'plain + demo alt + no xhr + permanent' => $full['plain + demo alt + no xhr + permanent'],
    ];
});

dataset('mms_youtube_test_matrix', function () {
    $full = [
        'bootstrap 5 + demo default + xhr + permanent' => ['bootstrap-5', 'demo_default', true, 'permanent'],
        'bootstrap 5 + demo default + xhr + temporary' => ['bootstrap-5', 'demo_default', true, 'temporary'],
        'bootstrap 5 + demo default + no xhr + permanent' => ['bootstrap-5', 'demo_default', false, 'permanent'],
        'bootstrap 5 + demo default + no xhr + temporary' => ['bootstrap-5', 'demo_default', false, 'temporary'],

        'bootstrap 5 + demo alt + xhr + permanent' => ['bootstrap-5', 'demo_alt', true, 'permanent'],
        'bootstrap 5 + demo alt + xhr + temporary' => ['bootstrap-5', 'demo_alt', true, 'temporary'],
        'bootstrap 5 + demo alt + no xhr + permanent' => ['bootstrap-5', 'demo_alt', false, 'permanent'],
        'bootstrap 5 + demo alt + no xhr + temporary' => ['bootstrap-5', 'demo_alt', false, 'temporary'],

        'bootstrap 6 + demo default + xhr + permanent' => ['bootstrap-6', 'demo_default', true, 'permanent'],
        'bootstrap 6 + demo default + xhr + temporary' => ['bootstrap-6', 'demo_default', true, 'temporary'],
        'bootstrap 6 + demo default + no xhr + permanent' => ['bootstrap-6', 'demo_default', false, 'permanent'],
        'bootstrap 6 + demo default + no xhr + temporary' => ['bootstrap-6', 'demo_default', false, 'temporary'],

        'bootstrap 6 + demo alt + xhr + permanent' => ['bootstrap-6', 'demo_alt', true, 'permanent'],
        'bootstrap 6 + demo alt + xhr + temporary' => ['bootstrap-6', 'demo_alt', true, 'temporary'],
        'bootstrap 6 + demo alt + no xhr + permanent' => ['bootstrap-6', 'demo_alt', false, 'permanent'],
        'bootstrap 6 + demo alt + no xhr + temporary' => ['bootstrap-6', 'demo_alt', false, 'temporary'],

        'plain + demo default + xhr + permanent' => ['plain', 'demo_default', true, 'permanent'],
        'plain + demo default + xhr + temporary' => ['plain', 'demo_default', true, 'temporary'],
        'plain + demo default + no xhr + permanent' => ['plain', 'demo_default', false, 'permanent'],
        'plain + demo default + no xhr + temporary' => ['plain', 'demo_default', false, 'temporary'],

        'plain + demo alt + xhr + permanent' => ['plain', 'demo_alt', true, 'permanent'],
        'plain + demo alt + xhr + temporary' => ['plain', 'demo_alt', true, 'temporary'],
        'plain + demo alt + no xhr + permanent' => ['plain', 'demo_alt', false, 'permanent'],
        'plain + demo alt + no xhr + temporary' => ['plain', 'demo_alt', false, 'temporary'],
    ];

    if (getenv('PEST_BROWSER_FULL')) {
        return $full;
    }

    return [
        'bootstrap 5 + demo default + xhr + permanent' => $full['bootstrap 5 + demo default + xhr + permanent'],
        'bootstrap 6 + demo default + xhr + permanent' => $full['bootstrap 6 + demo default + xhr + permanent'],
        'bootstrap 5 + demo alt + no xhr + temporary' => $full['bootstrap 5 + demo alt + no xhr + temporary'],
        'plain + demo default + xhr + temporary' => $full['plain + demo default + xhr + temporary'],
        'plain + demo alt + no xhr + permanent' => $full['plain + demo alt + no xhr + permanent'],
    ];
});

dataset('media_lab_test_matrix', function () {
    $full = [
        'bootstrap 5 + demo default + xhr' => ['bootstrap-5', 'demo_default', true],
        'bootstrap 5 + demo default + no xhr' => ['bootstrap-5', 'demo_default', false],

        'bootstrap 5 + demo alt + xhr' => ['bootstrap-5', 'demo_alt', true],
        'bootstrap 5 + demo alt + no xhr' => ['bootstrap-5', 'demo_alt', false],

        'bootstrap 6 + demo default + xhr' => ['bootstrap-6', 'demo_default', true],
        'bootstrap 6 + demo default + no xhr' => ['bootstrap-6', 'demo_default', false],

        'bootstrap 6 + demo alt + xhr' => ['bootstrap-6', 'demo_alt', true],
        'bootstrap 6 + demo alt + no xhr' => ['bootstrap-6', 'demo_alt', false],

        'plain + demo default + xhr' => ['plain', 'demo_default', true],
        'plain + demo default + no xhr' => ['plain', 'demo_default', false],

        'plain + demo alt + xhr' => ['plain', 'demo_alt', true],
        'plain + demo alt + no xhr' => ['plain', 'demo_alt', false],
    ];

    if (getenv('PEST_BROWSER_FULL')) {
        return $full;
    }

    return [
        'bootstrap 5 + demo default + xhr' => $full['bootstrap 5 + demo default + xhr'],
        'bootstrap 6 + demo default + xhr' => $full['bootstrap 6 + demo default + xhr'],
        'bootstrap 5 + demo alt + no xhr' => $full['bootstrap 5 + demo alt + no xhr'],
        'plain + demo default + no xhr' => $full['plain + demo default + no xhr'],
        'plain + demo alt + xhr' => $full['plain + demo alt + xhr'],
    ];
});

dataset('media_html_editor_matrix', function () {
    $full = [
        'bootstrap 5 + demo default + xhr' => ['bootstrap-5', 'demo_default', true],
        'bootstrap 5 + demo default + no xhr' => ['bootstrap-5', 'demo_default', false],
        'bootstrap 6 + demo default + xhr' => ['bootstrap-6', 'demo_default', true],
        'bootstrap 6 + demo default + no xhr' => ['bootstrap-6', 'demo_default', false],
        'plain + demo default + xhr' => ['plain', 'demo_default', true],
        'plain + demo default + no xhr' => ['plain', 'demo_default', false],
        'plain + demo alt + xhr' => ['plain', 'demo_alt', true],
        'plain + demo alt + no xhr' => ['plain', 'demo_alt', false],
    ];

    if (getenv('PEST_BROWSER_FULL')) {
        return $full;
    }

    return [
        'bootstrap 6 + demo default + xhr' => $full['bootstrap 6 + demo default + xhr'],
        'plain + demo default + xhr' => $full['plain + demo default + xhr'],
        'plain + demo alt + no xhr' => $full['plain + demo alt + no xhr'],
    ];
});

dataset('validation_matrix', function () {
    return [
        'bootstrap 5 + demo default + xhr + permanent' => ['bootstrap-5', 'demo_default', true, 'permanent'],
        'bootstrap 6 + demo default + xhr + permanent' => ['bootstrap-6', 'demo_default', true, 'permanent'],
        'plain + demo default + no xhr + permanent' => ['plain', 'demo_default', false, 'permanent'],
    ];
});

dataset('media_carousel_test_matrix',
    [
        'bootstrap 5 + demo default + xhr + permanent' => ['bootstrap-5', 'demo_default', true, false],
        'bootstrap 5 + demo default + xhr + temporary' => ['bootstrap-5', 'demo_default', true, true],
        'bootstrap 5 + demo alt + no xhr + permanent' => ['bootstrap-5', 'demo_alt', false, false],
        'bootstrap 6 + demo default + xhr + permanent' => ['bootstrap-6', 'demo_default', true, false],
        'bootstrap 6 + demo default + xhr + temporary' => ['bootstrap-6', 'demo_default', true, true],
        'bootstrap 6 + demo alt + no xhr + permanent' => ['bootstrap-6', 'demo_alt', false, false],
        'plain + demo default + xhr + permanent' => ['plain', 'demo_default', true, false],
        'plain + demo default + xhr + temporary' => ['plain', 'demo_default', true, true],
        'plain + demo alt + no xhr + permanent' => ['plain', 'demo_alt', false, false],
    ]);

dataset('mmm_cap_matrix', [
    'plain + demo default + xhr + permanent' => ['plain', 'demo_default', 'permanent'],
    'plain + demo default + xhr + temporary' => ['plain', 'demo_default', 'temporary'],
]);

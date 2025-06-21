# Testing with Pest in Laravel Media Library Extensions

This guide explains how to write and run tests using [Pest PHP](https://pestphp.com/) for the Laravel Media Library Extensions package.

## Setup

The package is already configured to use Pest for testing. The setup includes:

1. Pest and its plugins are installed as dev dependencies
2. Test suites are configured in `phpunit.xml.dist`
3. The base `TestCase` is extended for all test types in `tests/Pest.php`

## Running Tests

To run all tests:

```bash
composer test
```

To run a specific test suite:

```bash
./vendor/bin/pest --testsuite=Unit
./vendor/bin/pest --testsuite=Feature
./vendor/bin/pest --testsuite=Architecture
```

To run a specific test file:

```bash
./vendor/bin/pest tests/Unit/ExampleTest.php
```

## Writing Tests

This package includes example tests in the `tests/Unit/Components` and `tests/Feature/Components` directories. These tests demonstrate how to use Pest to test components in isolation and in a more integrated way. You may need to adapt these examples to your specific environment.

### Unit Tests

Unit tests should be placed in the `tests/Unit` directory. These tests focus on testing individual components in isolation.

Example:

```php
<?php
// tests/Unit/YourTest.php

it('can do something', function () {
    // Arrange
    $object = new YourClass();

    // Act
    $result = $object->doSomething();

    // Assert
    expect($result)->toBe('expected value');
});
```

### Feature Tests

Feature tests should be placed in the `tests/Feature` directory. These tests focus on testing how components work together.

Example:

```php
<?php
// tests/Feature/YourFeatureTest.php

it('can render a component', function () {
    $this->registerTestRoute('your_test_route')
        ->visit('/your_test_route')
        ->seeElement('.your-component');
});
```

### Architecture Tests

Architecture tests should be placed in the `tests/Arch` directory. These tests enforce coding standards and architectural decisions.

Example:

```php
<?php
// tests/Arch/YourArchTest.php

arch('Your classes follow naming convention')
    ->expect('Mlbrgn\MediaLibraryExtensions\YourNamespace')
    ->toHaveSuffix('YourSuffix');
```

## Testing with Models

If you need to test with models, you can use the provided test models in `tests/Models` or create your own.

Example:

```php
<?php
// tests/Feature/YourModelTest.php

use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

it('can associate media with a model', function () {
    $blog = Blog::factory()->create();

    $blog->addMedia(storage_path('test-image.jpg'))
        ->toMediaCollection('images');

    expect($blog->getMedia('images'))->toHaveCount(1);
});
```

## Useful Pest Features

### Higher Order Testing

Pest allows for higher order testing, which can make your tests more readable:

```php
test('it can do something')->expect(true)->toBeTrue();
```

### Datasets

You can use datasets to run the same test with different inputs:

```php
it('can validate different inputs', function ($input, $expected) {
    expect(validate($input))->toBe($expected);
})->with([
    ['valid input', true],
    ['invalid input', false],
]);
```

### Custom Expectations

You can create custom expectations in `tests/Pest.php`:

```php
expect()->extend('toBeValidMedia', function () {
    return $this->toBeInstanceOf(\Spatie\MediaLibrary\MediaCollections\Models\Media::class)
        ->and($this->value->exists())->toBeTrue();
});
```

## Resources

- [Pest Documentation](https://pestphp.com/docs/installation)
- [Laravel Testing Documentation](https://laravel.com/docs/testing)
- [Spatie Media Library Documentation](https://spatie.be/docs/laravel-medialibrary)

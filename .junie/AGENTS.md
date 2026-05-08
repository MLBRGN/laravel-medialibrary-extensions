# Agent Development Guide - Media Library Extensions

This document provides project-specific information for developers working on the `mlbrgn/laravel-medialibrary-extensions` package.

## Build/Configuration Instructions

### Prerequisites
- PHP 8.1+
- SQLite (for testing)

### Local Setup
1. Clone the repository.
2. Install dependencies:
   ```bash
   composer install
   ```
3. The package uses a demo environment. If you need to run the demo, ensure you have the necessary icons and assets configured as suggested in `composer.json`.

## Testing Information

### Configuration
The project uses `phpunit` and `orchestra/testbench` for testing. The configuration is located in `phpunit.xml.dist`.

### Running Tests
To run the full suite:
```bash
./vendor/bin/phpunit
```
Or via the composer script:
```bash
composer test
```

### Adding New Tests
- Unit tests go in `tests/Unit`.
- Feature tests go in `tests/Feature`.
- Test models are located in `tests/Models`.
- Always extend `Mlbrgn\MediaLibraryExtensions\Tests\TestCase`.

### Test Example
The following test demonstrates how to verify that a model implements the `HasMediaExtended` interface:

```php
namespace Mlbrgn\MediaLibraryExtensions\Tests\Unit;

use Mlbrgn\MediaLibraryExtensions\Tests\TestCase;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog;

class HasMediaExtendedTest extends TestCase
{
    public function test_model_implements_interface()
    {
        $blog = new Blog();
        $this->assertInstanceOf(HasMediaExtended::class, $blog);
        $this->assertTrue(is_subclass_of(Blog::class, HasMediaExtended::class));
    }
}
```

## Additional Development Information

### Code Style
- Follow PSR-12 coding standards.
- Use `laravel/pint` for formatting: `./vendor/bin/pint`.
- Use `phpstan` for static analysis: `composer analyse`.

### Key Components
- **HasMediaExtended Interface**: Models should implement this interface to gain access to extended media library features.
- **InteractsWithMediaExtended Trait**: Provides the default implementation for the `HasMediaExtended` interface. Ensure models using this trait also explicitly `implements HasMediaExtended`.
- **Authenticatable Contract**: Always use `Illuminate\Contracts\Auth\Authenticatable` for type-hinting users in media upload methods.

### Migration Conflicts
When adding new columns to `mle_temporary_uploads`, double-check `2025_07_30_000000_create_temporary_uploads_table.php` as it may already contain columns (like `instance_id`) that were added in later iterations to the base migration.

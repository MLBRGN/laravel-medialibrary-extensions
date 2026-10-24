<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Feature;

use Illuminate\Database\Eloquent\Relations\Relation;
use Mlbrgn\MediaLibraryExtensions\Support\PackageInfrastructure;
use Mlbrgn\MediaLibraryExtensions\Tests\TestCase;

class MorphMapCollisionTest extends TestCase
{
    /** @test */
    public function test_it_does_not_override_existing_morph_map_entries()
    {
        // 1. Simulate host application defining a 'blog' morph map entry
        Relation::morphMap([
            'blog' => 'App\Models\Blog',
        ], false); // false to not merge but replace for the test

        // Verify initial state
        $this->assertEquals('App\Models\Blog', Relation::getMorphedModel('blog'));

        // 2. Package infrastructure is registered
        PackageInfrastructure::register('demo');

        // 3. Verify that 'blog' is STILL 'App\Models\Blog' and NOT overridden
        // Note: The package should now use 'mle_blog' instead of 'blog'
        $this->assertEquals('App\Models\Blog', Relation::getMorphedModel('blog'), "The 'blog' morph map entry was overridden by the package!");

        // 4. Verify that 'mle_blog' is registered
        $this->assertEquals('Mlbrgn\MediaLibraryExtensions\Tests\Models\Blog', Relation::getMorphedModel('mle_blog'));
        $this->assertEquals('Mlbrgn\MediaLibraryExtensions\Models\demo\Alien', Relation::getMorphedModel('mle_alien'));
    }
}

<?php

// tests/Support/Models/TestPost.php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Support\Models;

use Illuminate\Database\Eloquent\Model;
use Mlbrgn\MediaLibraryExtensions\Interfaces\HasMediaExtended;
use Mlbrgn\MediaLibraryExtensions\Traits\InteractsWithMediaExtended;

class TestPost extends Model implements HasMediaExtended
{
    use InteractsWithMediaExtended;

    protected $guarded = [];

    public function getHtmlEditorFields(): array
    {
        return ['content'];
    }
}

<?php

// tests/Support/Models/TestPost.php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Support\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TestPost extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = [];

    public function getHtmlEditorFields(): array
    {
        return ['content'];
    }
}

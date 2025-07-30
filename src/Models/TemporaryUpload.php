<?php /** @noinspection PhpMultipleClassDeclarationsInspection */

namespace Mlbrgn\MediaLibraryExtensions\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class TemporaryUpload extends Model
{

    protected $table = 'mle_temporary_uploads';
    protected $fillable = [
        'disk',
        'path',
        'original_filename',
        'mime_type',
        'session_id',
        'user_id',
        'extra_properties'
    ];

    protected $casts = [
        'extra_properties' => 'array',
    ];

    protected $appends = ['url'];

    public static function forCurrentSession(): Collection
    {
        return self::where('session_id', session()->getId())->get();
    }

    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk)->url($this->path);
    }

    // TODO use config values?
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    // TODO use config values?
    public function isDocument(): bool
    {
        return in_array($this->mime_type, [
            'application/pdf', 'text/plain', 'application/msword',
            'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function hasExtraProperty(string $key): bool
    {
        return false;
    }
}

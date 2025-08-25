<?php

namespace Mlbrgn\MediaLibraryExtensions\Tests\Models;

use Altek\Accountant\Contracts\Recordable as RecordableContract;
use Altek\Accountant\Recordable;
use App\Models\Organization;
use App\Models\Role;
use App\Models\Workplace;
use App\Notifications\CustomEmailVerificationNotification;
use App\Notifications\CustomPasswordResetNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Kyslik\ColumnSortable\Sortable;
use Lab404\Impersonate\Models\Impersonate;
use Laravel\Passport\HasApiTokens;
use Laravel\Scout\Searchable;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    protected $guarded = [];
}

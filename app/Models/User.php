<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Enums\RoleUser;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, InteractsWithMedia;
    protected $keyType = "string";
    protected $connection = 'sso';

    protected $table = 'pdamone.users';


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'role' => RoleUser::class,
        'password' => 'hashed'
    ];

    public function menu(): BelongsToMany
    {
        return $this->belongsToMany(Menu::class, 'hak_akses', 'user_id', 'menu_id')
            ->where('aplikasi', 'website')
            ->orderByRaw("pdamone.menu.created_at ASC")
            ->withTimestamps();
    }

    public function is_admin(): bool
    {
        return $this->role->value == 'admin';
    }

    public function is_super(): bool
    {
        return $this->role->value == 'super';
    }

    public function is_user(): bool
    {
        return $this->role->value == 'user';
    }
}

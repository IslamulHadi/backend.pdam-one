<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Kategori extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'kategori';

    protected $fillable = [
        'nama',
    ];

    /**
     * @return BelongsToMany<Berita, $this>
     */
    public function berita(): BelongsToMany
    {
        return $this->belongsToMany(Berita::class, 'berita_kategori', 'kategori_id', 'berita_id')
            ->withTimestamps();
    }
}

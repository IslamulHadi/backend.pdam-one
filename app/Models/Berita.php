<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Berita extends Model implements HasMedia
{
    use HasFactory, HasUuids, InteractsWithMedia;

    protected $table = 'berita';

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'published_at' => 'datetime:d F Y H:i:s',
            'view_count' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Berita $news) {
            if (empty($news->slug)) {
                $news->slug = Str::slug($news->title);
            }
            if (empty($news->excerpt) && ! empty($news->content)) {
                $news->excerpt = Str::limit(strip_tags($news->content), 200);
            }
        });

        $clearCaches = function (): void {
            Cache::forget('page.home');
            Cache::forget('berita.featured');
        };

        static::saved($clearCaches);
        static::deleted($clearCaches);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * @return BelongsToMany<Kategori, $this>
     */
    public function kategori(): BelongsToMany
    {
        return $this->belongsToMany(Kategori::class, 'berita_kategori', 'berita_id', 'kategori_id')
            ->withTimestamps();
    }

    /**
     * @param  Builder<Berita>  $query
     * @return Builder<Berita>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /**
     * @param  Builder<Berita>  $query
     * @return Builder<Berita>
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * @param  Builder<Berita>  $query
     * @return Builder<Berita>
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->whereHas('kategori', fn (Builder $q) => $q->where('nama', $category));
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }

    public function getReadingTimeAttribute(): int
    {
        $wordCount = str_word_count(strip_tags($this->content));

        return max(1, (int) ceil($wordCount / 200));
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnails')
            ->singleFile();
    }
}

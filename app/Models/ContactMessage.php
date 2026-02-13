<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContactMessage extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pesan_kontak';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'category',
        'message',
        'is_read',
        'responded_at',
        'response',
        'responded_by',
    ];

    protected function casts(): array
    {
        return [
            'is_read' => 'boolean',
            'responded_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function respondedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responded_by');
    }

    /**
     * @param  Builder<ContactMessage>  $query
     * @return Builder<ContactMessage>
     */
    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('is_read', false);
    }

    /**
     * @param  Builder<ContactMessage>  $query
     * @return Builder<ContactMessage>
     */
    public function scopeUnanswered(Builder $query): Builder
    {
        return $query->whereNull('responded_at');
    }

    public function markAsRead(): void
    {
        if (! $this->is_read) {
            $this->update(['is_read' => true]);
        }
    }

    public function respond(string $response, string $userId): void
    {
        $this->update([
            'is_read' => true,
            'response' => $response,
            'responded_at' => now(),
            'responded_by' => $userId,
        ]);
    }
}


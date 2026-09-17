<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'user_id',
        'category_id',
        'status',
        'archived_at',
        'archived_by',
    ];

    protected function casts(): array
    {
        return [
            'archived_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(PostVersion::class)->latest('version_number');
    }

    public function archivedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'archived_by');
    }

    public function suggestions(): HasMany
    {
        return $this->hasMany(PostSuggestion::class);
    }

    public function pinnedSuggestions(): HasMany
    {
        return $this->suggestions()->where('is_pinned', true)->latest();
    }

    public function isArchived(): bool
    {
        return $this->status === 'archived';
    }
}

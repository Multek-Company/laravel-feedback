<?php

namespace Multek\LaravelFeedback\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Multek\LaravelFeedback\Database\Factories\FeedbackFactory;

class Feedback extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'content',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'collection',
        ];
    }

    public function getTable(): string
    {
        return config('feedback.table_name', 'feedbacks');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('feedback.user_model'));
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeByUser(Builder $query, Model $user): Builder
    {
        return $query->where('user_id', $user->getKey());
    }

    public function scopeRecent(Builder $query, int $days = 7): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    protected static function newFactory(): FeedbackFactory
    {
        return FeedbackFactory::new();
    }
}

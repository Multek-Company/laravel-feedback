<?php

namespace Multek\LaravelFeedback\Facades;

use Illuminate\Support\Facades\Facade;
use Multek\LaravelFeedback\FeedbackManager;

/**
 * @method static \Multek\LaravelFeedback\Models\Feedback create(array $data)
 * @method static \Illuminate\Database\Eloquent\Builder query()
 * @method static \Illuminate\Database\Eloquent\Builder forUser(\Illuminate\Database\Eloquent\Model $user)
 * @method static \Illuminate\Database\Eloquent\Builder ofType(string $type)
 *
 * @see FeedbackManager
 */
class Feedback extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return FeedbackManager::class;
    }
}

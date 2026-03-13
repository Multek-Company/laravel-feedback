<?php

namespace Multek\LaravelFeedback\Traits;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Multek\LaravelFeedback\FeedbackManager;
use Multek\LaravelFeedback\Models\Feedback;

trait HasFeedback
{
    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class, 'user_id');
    }

    public function submitFeedback(array $data): Feedback
    {
        $data['user_id'] = $this->getKey();

        return app(FeedbackManager::class)->create($data);
    }
}

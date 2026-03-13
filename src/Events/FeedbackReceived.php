<?php

namespace Multek\LaravelFeedback\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Multek\LaravelFeedback\Models\Feedback;

class FeedbackReceived
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Feedback $feedback,
    ) {}
}

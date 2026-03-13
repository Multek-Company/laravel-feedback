<?php

namespace Multek\LaravelFeedback;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Multek\LaravelFeedback\Events\FeedbackReceived;
use Multek\LaravelFeedback\Models\Feedback;

class FeedbackManager
{
    /**
     * @throws ValidationException
     */
    public function create(array $data): Feedback
    {
        $this->validateMetadata($data['metadata'] ?? []);

        $feedback = Feedback::create($data);

        FeedbackReceived::dispatch($feedback);

        return $feedback;
    }

    public function query(): Builder
    {
        return Feedback::query();
    }

    public function forUser(Model $user): Builder
    {
        return Feedback::byUser($user);
    }

    public function ofType(string $type): Builder
    {
        return Feedback::byType($type);
    }

    /**
     * @throws ValidationException
     */
    protected function validateMetadata(mixed $metadata): void
    {
        $rules = config('feedback.metadata.validation', []);

        if (empty($rules)) {
            return;
        }

        $prefixedRules = collect($rules)->mapWithKeys(
            fn ($rule, $key) => ["metadata.{$key}" => $rule]
        )->all();

        Validator::make(['metadata' => $metadata ?: []], $prefixedRules)->validate();
    }
}

<?php

use Illuminate\Support\Facades\Event;
use Multek\LaravelFeedback\Events\FeedbackReceived;
use Multek\LaravelFeedback\Models\Feedback;
use Multek\LaravelFeedback\Tests\TestUser;

beforeEach(function () {
    config()->set('feedback.user_model', TestUser::class);
});

it('has feedbacks relationship', function () {
    $user = TestUser::create(['name' => 'John', 'email' => 'john@test.com']);

    Feedback::create(['type' => 'bug', 'content' => 'Bug 1', 'user_id' => $user->id]);
    Feedback::create(['type' => 'feature', 'content' => 'Feature 1', 'user_id' => $user->id]);

    expect($user->feedbacks)->toHaveCount(2);
});

it('submits feedback via trait method', function () {
    Event::fake();

    $user = TestUser::create(['name' => 'Jane', 'email' => 'jane@test.com']);

    $feedback = $user->submitFeedback([
        'type' => 'bug',
        'content' => 'Something is broken',
    ]);

    expect($feedback)->toBeInstanceOf(Feedback::class)
        ->and($feedback->user_id)->toBe($user->id)
        ->and($feedback->type)->toBe('bug');

    Event::assertDispatched(FeedbackReceived::class);
});

it('submits feedback with metadata via trait', function () {
    Event::fake();

    $user = TestUser::create(['name' => 'Alice', 'email' => 'alice@test.com']);

    $feedback = $user->submitFeedback([
        'type' => 'ux-issue',
        'content' => 'Confusing navigation',
        'metadata' => ['page_url' => 'https://app.com/nav', 'browser' => 'Safari'],
    ]);

    expect($feedback->metadata->get('page_url'))->toBe('https://app.com/nav')
        ->and($feedback->user_id)->toBe($user->id);
});

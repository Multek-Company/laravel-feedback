<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Validation\ValidationException;
use Multek\LaravelFeedback\Events\FeedbackReceived;
use Multek\LaravelFeedback\Facades\Feedback;
use Multek\LaravelFeedback\Models\Feedback as FeedbackModel;
use Multek\LaravelFeedback\Tests\TestUser;

it('creates feedback via facade', function () {
    Event::fake();

    $feedback = Feedback::create([
        'type' => 'bug',
        'content' => 'Something broke',
    ]);

    expect($feedback)->toBeInstanceOf(FeedbackModel::class)
        ->and($feedback->type)->toBe('bug');

    Event::assertDispatched(FeedbackReceived::class, function ($event) use ($feedback) {
        return $event->feedback->id === $feedback->id;
    });
});

it('creates feedback with metadata via facade', function () {
    Event::fake();

    $feedback = Feedback::create([
        'type' => 'bug',
        'content' => 'Page is slow',
        'metadata' => ['browser' => 'Firefox', 'page_url' => 'https://example.com/slow'],
    ]);

    expect($feedback->metadata->get('browser'))->toBe('Firefox')
        ->and($feedback->metadata->get('page_url'))->toBe('https://example.com/slow');
});

it('validates metadata when rules are configured', function () {
    config()->set('feedback.metadata.validation', [
        'browser' => 'required|string',
    ]);

    Feedback::create([
        'type' => 'bug',
        'content' => 'Missing browser info',
        'metadata' => [],
    ]);
})->throws(ValidationException::class);

it('skips metadata validation when no rules configured', function () {
    Event::fake();

    config()->set('feedback.metadata.validation', []);

    $feedback = Feedback::create([
        'type' => 'bug',
        'content' => 'No rules, no problem',
        'metadata' => ['anything' => 'goes'],
    ]);

    expect($feedback)->toBeInstanceOf(FeedbackModel::class);
});

it('returns query builder via facade', function () {
    FeedbackModel::create(['type' => 'bug', 'content' => 'Bug 1']);
    FeedbackModel::create(['type' => 'feature', 'content' => 'Feature 1']);

    expect(Feedback::query()->count())->toBe(2);
});

it('queries by user via facade', function () {
    config()->set('feedback.user_model', TestUser::class);

    $user = TestUser::create(['name' => 'Test', 'email' => 'test@test.com']);
    FeedbackModel::create(['type' => 'bug', 'content' => 'My bug', 'user_id' => $user->id]);
    FeedbackModel::create(['type' => 'bug', 'content' => 'Other bug', 'user_id' => 999]);

    expect(Feedback::forUser($user)->count())->toBe(1);
});

it('queries by type via facade', function () {
    FeedbackModel::create(['type' => 'bug', 'content' => 'Bug']);
    FeedbackModel::create(['type' => 'praise', 'content' => 'Great!']);

    expect(Feedback::ofType('bug')->count())->toBe(1)
        ->and(Feedback::ofType('praise')->count())->toBe(1);
});

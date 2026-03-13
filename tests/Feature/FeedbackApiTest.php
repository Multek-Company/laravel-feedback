<?php

use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Multek\LaravelFeedback\Events\FeedbackReceived;
use Multek\LaravelFeedback\Http\Controllers\FeedbackController;
use Multek\LaravelFeedback\Models\Feedback;
use Multek\LaravelFeedback\Tests\TestUser;

beforeEach(function () {
    config()->set('feedback.user_model', TestUser::class);
    config()->set('feedback.route.enabled', true);
    config()->set('feedback.route.middleware', ['api']);

    // Re-register routes since we enabled them after boot
    Route::prefix(config('feedback.route.prefix'))
        ->middleware(config('feedback.route.middleware'))
        ->group(function () {
            Route::get('/', [FeedbackController::class, 'index']);
            Route::post('/', [FeedbackController::class, 'store']);
        });
});

it('stores feedback via api', function () {
    Event::fake();

    $user = TestUser::create(['name' => 'Test', 'email' => 'test@test.com']);

    $response = $this->actingAs($user)->postJson('/api/feedback', [
        'type' => 'bug',
        'content' => 'Something is broken',
    ]);

    $response->assertStatus(201)
        ->assertJsonFragment([
            'type' => 'bug',
            'content' => 'Something is broken',
        ]);

    Event::assertDispatched(FeedbackReceived::class);
});

it('stores feedback with metadata via api', function () {
    Event::fake();

    $user = TestUser::create(['name' => 'Test', 'email' => 'test@test.com']);

    $response = $this->actingAs($user)->postJson('/api/feedback', [
        'type' => 'bug',
        'content' => 'Broken button',
        'metadata' => ['browser' => 'Chrome', 'page_url' => 'https://app.com'],
    ]);

    $response->assertStatus(201);

    $feedback = Feedback::first();
    expect($feedback->metadata->get('browser'))->toBe('Chrome');
});

it('validates required fields on store', function () {
    $user = TestUser::create(['name' => 'Test', 'email' => 'test@test.com']);

    $response = $this->actingAs($user)->postJson('/api/feedback', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['type', 'content']);
});

it('lists feedback with pagination', function () {
    Feedback::create(['type' => 'bug', 'content' => 'Bug 1']);
    Feedback::create(['type' => 'feature', 'content' => 'Feature 1']);

    $response = $this->getJson('/api/feedback');

    $response->assertOk()
        ->assertJsonCount(2, 'data');
});

it('filters feedback by type', function () {
    Feedback::create(['type' => 'bug', 'content' => 'Bug 1']);
    Feedback::create(['type' => 'bug', 'content' => 'Bug 2']);
    Feedback::create(['type' => 'feature', 'content' => 'Feature 1']);

    $response = $this->getJson('/api/feedback?type=bug');

    $response->assertOk()
        ->assertJsonCount(2, 'data');
});

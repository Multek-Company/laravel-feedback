<?php

use Illuminate\Support\Collection;
use Multek\LaravelFeedback\Models\Feedback;
use Multek\LaravelFeedback\Tests\TestUser;

it('can create feedback', function () {
    $feedback = Feedback::create([
        'type' => 'bug',
        'content' => 'Something is broken',
    ]);

    expect($feedback)->toBeInstanceOf(Feedback::class)
        ->and($feedback->type)->toBe('bug')
        ->and($feedback->content)->toBe('Something is broken');
});

it('casts metadata as collection', function () {
    $feedback = Feedback::create([
        'type' => 'bug',
        'content' => 'Broken button',
        'metadata' => ['browser' => 'Chrome', 'page_url' => 'https://example.com'],
    ]);

    expect($feedback->metadata)->toBeInstanceOf(Collection::class)
        ->and($feedback->metadata->get('browser'))->toBe('Chrome');
});

it('allows null metadata', function () {
    $feedback = Feedback::create([
        'type' => 'feature',
        'content' => 'Add dark mode',
    ]);

    expect($feedback->metadata)->toBeNull();
});

it('scopes by type', function () {
    Feedback::create(['type' => 'bug', 'content' => 'Bug report']);
    Feedback::create(['type' => 'feature', 'content' => 'Feature request']);
    Feedback::create(['type' => 'bug', 'content' => 'Another bug']);

    expect(Feedback::byType('bug')->count())->toBe(2)
        ->and(Feedback::byType('feature')->count())->toBe(1);
});

it('scopes by user', function () {
    $user = TestUser::create(['name' => 'John', 'email' => 'john@test.com']);
    Feedback::create(['type' => 'bug', 'content' => 'My bug', 'user_id' => $user->id]);
    Feedback::create(['type' => 'bug', 'content' => 'Other bug', 'user_id' => 999]);

    expect(Feedback::byUser($user)->count())->toBe(1);
});

it('scopes recent feedback', function () {
    $recent = Feedback::create(['type' => 'bug', 'content' => 'Recent']);
    $old = Feedback::create(['type' => 'bug', 'content' => 'Old']);

    // Manually update created_at to bypass Eloquent timestamps
    Feedback::where('id', $old->id)->update(['created_at' => now()->subDays(30)]);

    expect(Feedback::recent(7)->count())->toBe(1)
        ->and(Feedback::recent(60)->count())->toBe(2);
});

it('belongs to a user', function () {
    config()->set('feedback.user_model', TestUser::class);

    $user = TestUser::create(['name' => 'Jane', 'email' => 'jane@test.com']);
    $feedback = Feedback::create([
        'type' => 'praise',
        'content' => 'Great app!',
        'user_id' => $user->id,
    ]);

    expect($feedback->user)->toBeInstanceOf(TestUser::class)
        ->and($feedback->user->id)->toBe($user->id);
});

it('uses configurable table name', function () {
    $feedback = new Feedback;

    expect($feedback->getTable())->toBe('feedbacks');

    config()->set('feedback.table_name', 'custom_feedbacks');

    expect($feedback->getTable())->toBe('custom_feedbacks');
});

it('can use factory', function () {
    $feedback = Feedback::factory()->create();

    expect($feedback)->toBeInstanceOf(Feedback::class)
        ->and($feedback->type)->toBeString()
        ->and($feedback->content)->toBeString();
});

it('can use factory with metadata', function () {
    $feedback = Feedback::factory()->withMetadata(['key' => 'value'])->create();

    expect($feedback->metadata->get('key'))->toBe('value');
});

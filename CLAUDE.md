# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Package Overview

`multek/laravel-feedback` — A headless, customizable user feedback collection package for Laravel. Provides a Facade, HasFeedback trait, and optional API routes. Features a flexible `metadata` JSON column for attaching arbitrary structured data. Requires PHP 8.2+, Laravel 11/12.

## Commands

```bash
# Run all tests (Pest v3)
vendor/bin/pest

# Run a single test file
vendor/bin/pest tests/Feature/FeedbackApiTest.php

# Run a specific test by name
vendor/bin/pest --filter="creates feedback"

# Run only unit or feature tests
vendor/bin/pest --testsuite=Unit
vendor/bin/pest --testsuite=Feature

# Code style
vendor/bin/pint --test
vendor/bin/pint
```

## Architecture

### Core Flow

1. **FeedbackManager** (`src/FeedbackManager.php`) — Service class bound as singleton. Entry point for `create()`, `query()`, `forUser()`, `ofType()`. Validates metadata against configured rules, dispatches `FeedbackReceived` event.
2. **Feedback model** (`src/Models/Feedback.php`) — Eloquent model with `metadata` cast as `collection`. Scopes: `byType()`, `byUser()`, `recent()`. Configurable table name via `config('feedback.table_name')`.
3. **Facade** (`src/Facades/Feedback.php`) — Resolves to `FeedbackManager`.
4. **HasFeedback trait** (`src/Traits/HasFeedback.php`) — Add to User model for `feedbacks()` relationship and `submitFeedback()` convenience method.
5. **FeedbackReceived event** (`src/Events/FeedbackReceived.php`) — Dispatched on every feedback creation.
6. **Optional API** (`src/Http/Controllers/FeedbackController.php`) — Disabled by default. Enable via `config('feedback.route.enabled')`.

### Key Design Decisions

- **No foreign key constraint** on `user_id` — user table name may vary across apps.
- **Free-form `type` column** — no restricted list, parent app defines types.
- **Routes disabled by default** — most users interact via Facade/trait.
- **Metadata validation is optional** — configure rules in `config('feedback.metadata.validation')`.

### Testing Setup

Tests use Orchestra Testbench with SQLite. `tests/TestCase.php` sets up the service provider, runs migration, and creates a `users` table. `tests/TestUser.php` is a minimal model using HasFeedback trait.

### Configuration

All settings in `config/feedback.php`: `user_model`, `table_name`, `route.*`, `metadata.validation`.

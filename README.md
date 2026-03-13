# Laravel Feedback

A headless, customizable user feedback collection package for Laravel with a flexible metadata JSON column.

## Installation

```bash
composer require multek/laravel-feedback
```

Publish the config and migration:

```bash
php artisan vendor:publish --tag=feedback-config
php artisan vendor:publish --tag=feedback-migrations
php artisan migrate
```

## Usage

### Via Facade

```php
use Multek\LaravelFeedback\Facades\Feedback;

// Create feedback
$feedback = Feedback::create([
    'type' => 'bug',
    'content' => 'The button does not work',
    'user_id' => auth()->id(),
    'metadata' => [
        'browser' => 'Chrome 120',
        'page_url' => 'https://app.com/dashboard',
    ],
]);

// Query feedback
$bugs = Feedback::ofType('bug')->get();
$userFeedback = Feedback::forUser($user)->get();
$recent = Feedback::query()->recent(7)->get();
```

### Via HasFeedback Trait

Add the trait to your User model:

```php
use Multek\LaravelFeedback\Traits\HasFeedback;

class User extends Authenticatable
{
    use HasFeedback;
}
```

Then use it:

```php
$user->submitFeedback([
    'type' => 'feature',
    'content' => 'Please add dark mode',
    'metadata' => ['priority' => 'high'],
]);

$user->feedbacks; // Collection of user's feedback
```

### Via API (Optional)

Enable routes in `config/feedback.php`:

```php
'route' => [
    'enabled' => true,
    'prefix' => 'api/feedback',
    'middleware' => ['api', 'auth:sanctum'],
],
```

Endpoints:

- `POST /api/feedback` — Submit feedback
- `GET /api/feedback` — List feedback (supports `?type=` filter, paginated)

### Events

`FeedbackReceived` is dispatched on every feedback creation:

```php
use Multek\LaravelFeedback\Events\FeedbackReceived;

class SendSlackNotification
{
    public function handle(FeedbackReceived $event): void
    {
        // $event->feedback
    }
}
```

### Metadata Validation

Optionally enforce metadata structure in `config/feedback.php`:

```php
'metadata' => [
    'validation' => [
        'browser' => 'required|string',
        'page_url' => 'sometimes|url',
    ],
],
```

## Configuration

| Key | Default | Description |
|-----|---------|-------------|
| `user_model` | `App\Models\User::class` | User model class |
| `table_name` | `feedbacks` | Database table name |
| `route.enabled` | `false` | Enable API routes |
| `route.prefix` | `api/feedback` | Route prefix |
| `route.middleware` | `['api', 'auth:sanctum']` | Route middleware |
| `metadata.validation` | `[]` | Metadata validation rules |

## License

MIT

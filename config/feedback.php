<?php

use App\Models\User;

return [

    /*
    |--------------------------------------------------------------------------
    | User Model
    |--------------------------------------------------------------------------
    |
    | The fully qualified class name of the user model that feedback belongs to.
    |
    */
    'user_model' => User::class,

    /*
    |--------------------------------------------------------------------------
    | Table Name
    |--------------------------------------------------------------------------
    |
    | The database table name for storing feedback entries.
    |
    */
    'table_name' => 'feedbacks',

    /*
    |--------------------------------------------------------------------------
    | API Routes
    |--------------------------------------------------------------------------
    |
    | Control whether the package registers API routes. Disabled by default —
    | most users will interact via the Facade or HasFeedback trait directly.
    |
    */
    'route' => [
        'enabled' => false,
        'prefix' => 'api/feedback',
        'middleware' => ['api', 'auth:sanctum'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Metadata Validation
    |--------------------------------------------------------------------------
    |
    | Optional validation rules for the metadata JSON column. When set, these
    | rules are enforced as metadata.* rules during creation.
    |
    | Example:
    |   'validation' => [
    |       'browser' => 'required|string',
    |       'page_url' => 'sometimes|url',
    |   ],
    |
    */
    'metadata' => [
        'validation' => [],
    ],

];

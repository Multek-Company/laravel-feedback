<?php

namespace Multek\LaravelFeedback\Tests;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Multek\LaravelFeedback\Traits\HasFeedback;

class TestUser extends Authenticatable
{
    use HasFeedback;

    protected $table = 'users';

    protected $fillable = ['name', 'email'];
}

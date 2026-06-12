<?php

use Illuminate\Support\Facades\Event;
use App\Listeners\LogSecurityEvent;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\PasswordReset;

Event::listen([
    Login::class,
    Logout::class,
    Failed::class,
    PasswordReset::class,
], LogSecurityEvent::class);

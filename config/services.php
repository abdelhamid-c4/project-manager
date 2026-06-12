<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file stores credentials for third-party services. Add your
    | GROQ_API_KEY to .env. Never hardcode secrets in this file.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Groq Llama AI-ready configuration
    |--------------------------------------------------------------------------
    | Used by App\Services\AiProjectService
    */
    'groq' => [
        'key' => env('GROQ_API_KEY'),
        'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
        'base_url' => env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google reCAPTCHA v2
    |--------------------------------------------------------------------------
    | Used on the login page to protect authentication from automated attempts.
    */
    'recaptcha' => [
        'enabled' => env('RECAPTCHA_ENABLED', true),
        'site_key' => env('RECAPTCHA_SITE_KEY'),
        'secret_key' => env('RECAPTCHA_SECRET_KEY'),
        'verify_url' => env('RECAPTCHA_VERIFY_URL', 'https://www.google.com/recaptcha/api/siteverify'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Trusted Login Devices
    |--------------------------------------------------------------------------
    | After a successful email-code verification, the current browser is trusted
    | for the configured period. A new browser/device must verify by email again.
    */
    'login_trusted_devices' => [
        'cookie_name' => env('LOGIN_TRUSTED_DEVICE_COOKIE', 'pm_trusted_device'),
        'lifetime_days' => env('LOGIN_TRUSTED_DEVICE_DAYS', 90),
    ],

];

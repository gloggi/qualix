<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'hitobito' => [
        'base_url' => env('HITOBITO_BASE_URL', 'http://demo.hitobito.ch'),
        'client_id' => env('HITOBITO_CLIENT_UID'),
        'client_secret' => env('HITOBITO_CLIENT_SECRET'),
        'redirect' => env('HITOBITO_CALLBACK_URI', 'https://qualix.flamberg.ch/login/hitobito/callback'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

];

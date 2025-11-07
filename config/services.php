<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'whatsapp' => [
        'token' => env('WHATSAPP_API_TOKEN', 'hhjSsIvJIaVRniUlHgn1UjFe26tOt0bS'),
        'send_text_url' => env('WHATSAPP_SEND_TEXT_URL', 'https://api-whatsapp.api-alisson.com.br/api/v1/message/send-text'),
        'webhook_base' => env('WHATSAPP_WEBHOOK_BASE', 'https://app.saudegaurdia.com.br'),
    ],

    'subscriptions' => [
        'base_url' => env('SUBSCRIPTIONS_BASE_URL', 'https://assinaturas.saudegaurdia.com.br/api'),
        'default_plan_id' => env('SUBSCRIPTIONS_DEFAULT_PLAN_ID'),
        'default_status' => env('SUBSCRIPTIONS_DEFAULT_STATUS', 'active'),
        'default_price' => env('SUBSCRIPTIONS_DEFAULT_PRICE'),
    ],

];

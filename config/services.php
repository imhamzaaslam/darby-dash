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

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'bol_com' => [
        'access_token_endpoint' => env('BOL_COM_ACCESS_TOKEN_ENDPOINT', 'https://login.bol.com/token'),
        'endpoint' => env('BOL_COM_API_ENDPOINT', 'https://api.bol.com/retailer-demo/'),
        'content_type_json' => env('BOL_COM_CONTENT_TYPE_JSON', 'application/vnd.retailer.v9+json'),
    ],

    'kvk' => [
        'access_token' => env('KVK_ACCESS_TOKEN'),
        'endpoint' => env('KVK_API_ENDPOINT'),
    ],

    'amazon' => [
        'url' => env('AMAZON_API_URL'),
    ],

    'yuki' => [
        'url' => env('YUKI_SOAP_URL'),
    ],
];

<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'useConfigOnDatabase' => env('CLICKSIGN_USE_CONFIG_ON_DATABASE', false),
    'devMode' => env('CLICKSIGN_DEV_MODE', true),
    'developmentUrl' => env('CLICKSIGN_DEV_URL', 'https://sandbox.clicksign.com'),
    'productionUrl' => env('CLICKSIGN_PROD_URL', 'https://app.clicksign.com'),
    'documentUrlVersion' => env('CLICKSIGN_DOCUMENT_VERSION', '/api/v1/documents'),
    'listUrlVersion' => env('CLICKSIGN_LIST_VERSION', '/api/v1/lists'),
    'notificationUrlVersion' => env('CLICKSIGN_NOTIFICATION_VERSION', '/api/v1/notifications'),
    'signersUrlVersion' => env('CLICKSIGN_SIGNERS_VERSION', '/api/v1/signers'),
    'accessToken' => env('CLICKSIGN_ACCESS_TOKEN', null),
];
<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'useConfigOnDatabase' => env('CLICKSIGN_USE_CONFIG_ON_DATABASE', false),
    'devMode' => env('CLICKSIGN_DEV_MODE', true),
    'developmentUrl' => env('CLICKSIGN_DEV_URL', 'https://sandbox.clicksign.com'),
    'productionUrl' => env('CLICKSIGN_PROD_URL', 'https://app.clicksign.com'),
    'documentEndPoint' => env('CLICKSIGN_DOCUMENT_VERSION', '/api/v1/documents'),
    'listEndPoint' => env('CLICKSIGN_LIST_VERSION', '/api/v1/lists'),
    'notificationEndPoint' => env('CLICKSIGN_NOTIFICATION_VERSION', '/api/v1/notifications'),
    'signersEndPoint' => env('CLICKSIGN_SIGNERS_VERSION', '/api/v1/signers'),
    'devAccessToken' => env('CLICKSIGN_DEV_ACCESS_TOKEN', null),
    'prodAccessToken' => env('CLICKSIGN_PROD_ACCESS_TOKEN', null),
    'documentSignDuration' => env('CLICKSIGN_DOCUMENT_DURATION', 0),
    'useIntegration' => env('CLICKSIGN_USE_INTEGRATION', false),
];
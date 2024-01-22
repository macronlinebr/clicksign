<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'devMode' => env('CLICKSIGN_DEV_MODE', true),
    'documentUrlVersion' => env('CLICKSIGN_URL_VERSION', '/api/v1/documents'),
    'listUrlVersion' => env('CLICKSIGN_URL_VERSION', '/api/v1/lists'),
    'notificationUrlVersion' => env('CLICKSIGN_URL_VERSION', '/api/v1/notifications'),
    'signersUrlVersion' => env('CLICKSIGN_URL_VERSION', '/api/v1/signers'),
    'urlBase' => env('CLICKSIGN_DEV_MODE', true)
        ? env('CLICKSIGN_DEV_URL', 'https://sandbox.clicksign.com')
        : env('CLICKSIGN_PROD_URL', 'https://app.clicksign.com'),
];
<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    'useConfigOnDatabase' => env('CLICKSIGN_USE_CONFIG_ON_DATABASE', false),
    'environment' => env('CLICKSIGN_ENVIRONMENT', 'dev'),
    'developmentUrl' => env('CLICKSIGN_DEV_URL', 'https://sandbox.clicksign.com'),
    'productionUrl' => env('CLICKSIGN_PROD_URL', 'https://app.clicksign.com'),
    'documentEndPoint' => env('CLICKSIGN_DOCUMENT_VERSION', '/api/v1/documents'),
    'listEndPoint' => env('CLICKSIGN_LIST_VERSION', '/api/v1/lists'),
    'notificationEndPoint' => env('CLICKSIGN_NOTIFICATION_VERSION', '/api/v1/notifications'),
    'signersEndPoint' => env('CLICKSIGN_SIGNERS_VERSION', '/api/v1/signers'),
    'devAccessToken' => env('CLICKSIGN_DEV_ACCESS_TOKEN', null),
    'prodAccessToken' => env('CLICKSIGN_PROD_ACCESS_TOKEN', null),
    'documentSignDuration' => env('CLICKSIGN_DOCUMENT_SIGN_DURATION', 0),
    'useIntegration' => env('CLICKSIGN_USE_INTEGRATION', false),
    'webhookDocumentAutoCloseDevSecret'=>  env('CLICKSIGN_WEBHOOK_DOCUMENT_AUTOCLOSE_DEV_SECRET',''),
    'webhookDocumentAutoCloseProdSecret'=> env('CLICKSIGN_WEBHOOK_DOCUMENT_AUTOCLOSE_PROD_SECRET', ''),
    'webhookDocumentCancelDevSecret'=>  env('CLICKSIGN_WEBHOOK_DOCUMENT_CANCEL_DEV_SECRET',''),
    'webhookDocumentCancelProdSecret'=> env('CLICKSIGN_WEBHOOK_DOCUMENT_CANCEL_PROD_SECRET', ''),
    'webhookDocumentCloseDevSecret'=>  env('CLICKSIGN_WEBHOOK_DOCUMENT_CLOSE_DEV_SECRET',''),
    'webhookDocumentCloseProdSecret'=> env('CLICKSIGN_WEBHOOK_DOCUMENT_CLOSE_PROD_SECRET', ''),
    'webhookDocumentDeadlineDevSecret'=>  env('CLICKSIGN_WEBHOOK_DOCUMENT_DEADLINE_DEV_SECRET',''),
    'webhookDocumentDeadlineProdSecret'=> env('CLICKSIGN_WEBHOOK_DOCUMENT_DEADLINE_PROD_SECRET', ''),
    'webhookDocumentDocumentClosedDevSecret'=>  env('CLICKSIGN_WEBHOOK_DOCUMENT_DOCUMENTCLOSED_DEV_SECRET',''),
    'webhookDocumentDocumentClosedProdSecret'=> env('CLICKSIGN_WEBHOOK_DOCUMENT_DOCUMENTCLOSED_PROD_SECRET', ''),
    'webhookDocumentRefusalDevSecret'=>  env('CLICKSIGN_WEBHOOK_DOCUMENT_REFUSAL_DEV_SECRET',''),
    'webhookDocumentRefusalProdSecret'=> env('CLICKSIGN_WEBHOOK_DOCUMENT_REFUSAL_PROD_SECRET', ''),
];
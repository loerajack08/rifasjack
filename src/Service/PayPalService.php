<?php

namespace App\Service;

use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class PayPalService
{
    private ApiContext $apiContext;

    public function __construct(string $clientId, string $clientSecret, string $mode)
    {
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential($clientId, $clientSecret)
        );

        $this->apiContext->setConfig([
            'mode' => $mode,
            'http.ConnectionTimeOut' => 30,
            'log.LogEnabled' => true,
            'log.FileName' => '../PayPal.log',
            'log.LogLevel' => 'DEBUG',
        ]);
    }

    public function getApiContext(): ApiContext
    {
        return $this->apiContext;
    }
}

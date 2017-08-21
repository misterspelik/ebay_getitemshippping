<?php
$autoload= 'vendor/autoload.php';
if (!file_exists($autoload)){
    throw new Exception('Please run composer install!');
}
require $autoload;
$config = require __DIR__.'/configuration.php';

use \DTS\eBaySDK\Constants;
use \DTS\eBaySDK\Trading\Services;
use \DTS\eBaySDK\Trading\Types;
use \DTS\eBaySDK\Trading\Enums;

$service = new Services\TradingService([
    'credentials' => $config['sandbox']['credentials'],
    'sandbox'     => true,
    'siteId'      => Constants\SiteIds::DE
]);
$request = new Types\GetItemShippingRequestType([
    'ItemID' => '152598060639',
    'DestinationCountryCode' => 'IL'
]);

$request->RequesterCredentials = new Types\CustomSecurityHeaderType();
$request->RequesterCredentials->eBayAuthToken = $config['sandbox']['oauthUserToken'];

$response= $service->getItemShipping($request);

if (isset($response->Errors)) {
    foreach ($response->Errors as $error) {
        printf(
            "%s: %s\n%s\n\n",
            $error->SeverityCode === Enums\SeverityCodeType::C_ERROR ? 'Error' : 'Warning',
            $error->ShortMessage,
            $error->LongMessage
        );
    }
}

echo "Domestic: ".$response->ShippingDetails->CalculatedShippingRate->PackagingHandlingCosts;
echo "International: ".$response->ShippingDetails->CalculatedShippingRate->InternationalPackagingHandlingCosts;
echo "Sales tax: ".isset($response->ShippingDetails->SalesTax->SalesTaxAmount) ? $response->ShippingDetails->SalesTax->SalesTaxAmount : '0';

<?php
require __DIR__ . '/PayPalSDK/paypal/rest-api-sdk-php/sample/bootstrap.php';
$config = array (
 	'mode' => 'sandbox' , 
 	'acct1.UserName' => 'jb-us-seller_api1.paypal.com',
	'acct1.Password' => 'WX4WTU3S8MY44S7F', 
	'acct1.Signature' => 'AFcWxV21C7fd0v3bYYYRCpSSRl31A7yDhhsPUU2XhtMoZXsWHFxu-RWy'
);
$paypalService = new PayPalAPIInterfaceServiceService($config);
$paymentDetails= new PaymentDetailsType();

$itemDetails = new PaymentDetailsItemType();
$itemDetails->Name = 'item';
$itemAmount = '1.00';
$itemDetails->Amount = $itemAmount;
$itemQuantity = '1';
$itemDetails->Quantity = $itemQuantity;

$paymentDetails->PaymentDetailsItem[0] = $itemDetails;

$orderTotal = new BasicAmountType();
$orderTotal->currencyID = 'USD';
$orderTotal->value = $itemAmount * $itemQuantity; 

$paymentDetails->OrderTotal = $orderTotal;
$paymentDetails->PaymentAction = 'Sale';

$setECReqDetails = new SetExpressCheckoutRequestDetailsType();
$setECReqDetails->PaymentDetails[0] = $paymentDetails;
$setECReqDetails->CancelURL = 'https://devtools-paypal.com/guide/expresscheckout/php?cancel=true';
$setECReqDetails->ReturnURL = 'https://devtools-paypal.com/guide/expresscheckout/php?success=true';

$setECReqType = new SetExpressCheckoutRequestType();
$setECReqType->Version = '104.0';
$setECReqType->SetExpressCheckoutRequestDetails = $setECReqDetails;

$setECReq = new SetExpressCheckoutReq();
$setECReq->SetExpressCheckoutRequest = $setECReqType;

$setECResponse = $paypalService->SetExpressCheckout($setECReq);
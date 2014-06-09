<?php

	require_once("rest-api-sdk-php-master/lib/PayPal/Api/Payment.php");
	require_once("rest-api-sdk-php-master/lib/PayPal/Api/PaymentExecution.php");

	$client_id = 'AVJnchB-IzspWNkcsi9q5BCnl-T2YmOkusUTDA0aA5C6pLFlvWYKT8ap3eFY';
	$client_secret = 'EAYclxDc6dQy04DZ3tFoCwCC6KUvAuXEokGiGjxPoZk8JKf3qCBro-dFwqKN';

	$token = $_GET['token'];
	$payerId = $_GET['PayerID'];

	$apiContext = new ApiContext(new OAuthTokenCredential($client_id, $client_secret));
	$payment = Payment::get($token);

?>
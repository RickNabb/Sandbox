<?php

/*
 * Sample bootstrap file.
 */

// Include the composer autoloader
if(!file_exists(__DIR__ .'/vendor/autoload.php')) {
	echo "The 'vendor' folder is missing. You must run 'composer update --no-dev' to resolve application dependencies.\nPlease see the README for more information.\n";
	exit(1);
}


require_once __DIR__ . '/vendor/autoload.php';
//require_once __DIR__ . '/common/user.php';
require_once __DIR__ . '/pay_app/common/order.php';
require_once __DIR__ . '/pay_app/common/paypal.php';
require_once __DIR__ . '/pay_app/common/util.php';
//require __DIR__ . '/common.php';

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

define('MYSQL_HOST', 'localhost:3306');
define('MYSQL_USERNAME', 'root');
define('MYSQL_PASSWORD', '');
define('MYSQL_DB', 'sandbox_payments');

/**
 * Helper method for getting an APIContext for all calls
 *
 * @return PayPal\Rest\ApiContext
 */
function getApiContext() {

	$client_id = 'AVJnchB-IzspWNkcsi9q5BCnl-T2YmOkusUTDA0aA5C6pLFlvWYKT8ap3eFY';
	$client_secret = 'EAYclxDc6dQy04DZ3tFoCwCC6KUvAuXEokGiGjxPoZk8JKf3qCBro-dFwqKN';

	// ### Api context
	// Use an ApiContext object to authenticate 
	// API calls. The clientId and clientSecret for the 
	// OAuthTokenCredential class can be retrieved from 
	// developer.paypal.com

	$apiContext = new ApiContext(
		new OAuthTokenCredential(
			$client_id,
			$client_secret
		)
	);



	// #### SDK configuration

	// Comment this line out and uncomment the PP_CONFIG_PATH
	// 'define' block if you want to use static file 
	// based configuration

	$apiContext->setConfig(
		array(
			'mode' => 'sandbox',
			'http.ConnectionTimeOut' => 30,
			'log.LogEnabled' => true,
			'log.FileName' => '../PayPal.log',
			'log.LogLevel' => 'FINE'
		)
	);

	/*
	// Register the sdk_config.ini file in current directory
	// as the configuration source.
	if(!defined("PP_CONFIG_PATH")) {
		define("PP_CONFIG_PATH", __DIR__);
	}
	*/

	return $apiContext;
}
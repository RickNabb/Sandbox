<?php


define('USERS_TABLE', 'users');
define('ORDERS_TABLE', 'orders');
define('APTS_TABLE', 'appointments');
define('ATTENDANTS_TABLE', 'apt_attendants');
define('EVENTS_TABLE', 'events');
define('CREDIT_CARD_TABLE', 'creditcards');
define('GOOGLE_AUTH_TABLE', 'google_auth');

function getConnection(){

	$usersTableCreateQuery = "CREATE TABLE IF NOT EXISTS `users` (`user_id` int(11) NOT NULL AUTO_INCREMENT,  `email` varchar(20) DEFAULT NULL,  `password` varchar(50) DEFAULT NULL,  `creditcard_id` varchar(40) DEFAULT NULL,  PRIMARY KEY (`user_id`),  UNIQUE KEY `email` (`email`)) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
	$ordersTableCreateQuery = "CREATE TABLE IF NOT EXISTS `orders` (`order_id` int(11) NOT NULL AUTO_INCREMENT,  `user_id` varchar(20) DEFAULT NULL,  `payment_id` varchar(50) DEFAULT NULL,  `state` varchar(20) DEFAULT NULL,  `amount` varchar(20) DEFAULT NULL,  `description` varchar(40) DEFAULT NULL,  `created_time` datetime DEFAULT NULL,   PRIMARY KEY (`order_id`)) ENGINE=InnoDB;";

	$link = @mysql_connect(MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD);
	if(!$link) {
		throw new Exception('Could not connect to mysql ' . mysql_error() . PHP_EOL . 
				'. Please check connection parameters in app/bootstrap.php');
	}
	if(!mysql_select_db(MYSQL_DB, $link)) {
		throw new Exception('Could not select database ' . mysql_error() . PHP_EOL . 
				'. Please check connection parameters in app/bootstrap.php');
	}
	
	//mysql_query($usersTableCreateQuery, $link);
	//mysql_query($ordersTableCreateQuery, $link);
	return $link;
}
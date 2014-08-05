<?php

// Page used when credit card is payment method

require_once __DIR__ . '/../../bootstrap.php';

if($_SERVER['REQUEST_METHOD'] == 'POST'){
	session_start();
	//$order = $_REQUEST['order'];

	require_once __DIR__ . '/../common/order.php';
	require_once __DIR__ . '/../common/util.php';

	if($_SESSION['logged_in'] == true || $_SESSION['guest_account'] == true){
		try{
			//if($order['payment_method'] == 'paypal'){

			$orderId = addOrder(($_SESSION['logged_in'] == true ? $_SESSION['user_id'] : '0000'), $_SESSION['appt_id'], NULL, NULL, $_SESSION['order_price'], 
				$_SESSION['order_desc']);
			$baseUrl = getBaseUrl() . "/order_completion.php?orderId=$orderId";
			$payment = makePaymentUsingPayPal($_SESSION['order_price'], 'USD', $_SESSION['order_desc'],
				"$baseUrl&success=true", "$baseUrl&success=false");
			updateOrder($orderId, $payment->getState(), $payment->getId());
			echo(getLink($payment->getLinks(), "approval_url"));
			//}
		} catch(PPConnectionException $ex){
			$message = parseApiError($ex->getData());
			$messageType = "error";
			echo $message;
		} catch(Exception $ex){
			$message = $ex->getMessage();
			$messageType = "error";
			echo $message;
		}
	}
}

//require_once 'orders.php';

?>
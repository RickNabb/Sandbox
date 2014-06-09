<!DOCTYPE html>
<html>

<head>
	<title>Schedule an Appointment | Payment</title>

	<link href="./css/metro-bootstrap.css" rel="stylesheet" type="text/css" />

	<script>

		window.onload = function(){
			//Read in event info

		};

		function eventSelect(sender){
			var partyName = "", partyInfo = "", price = 0;
			if(sender.selectedIndex > 0){
				clearInfo();
				switch(sender.value){
					case("Party 1"):
						partyName = "Party 1";
						partyInfo = "Fun party! The first party!";
						price = 25.99;
						break;
					case("Party 2"):
						partyName = "Party 2";
						partyInfo = "Another fun party! But the second one!";
						price = 35.99;
						break;
					case("Party 3"):
						partyName = "Party 3";
						partyInfo = "The third one! This one is okay.";
						price = 15.99;
						break;
					default:
						break;
				}

				var info = document.getElementById("info");
				var partyNameP = document.createElement("p");
				partyNameP.appendChild(document.createTextNode(partyName));
				info.appendChild(partyNameP);

				var partyInfoP = document.createElement("p");
				partyInfoP.appendChild(document.createTextNode(partyInfo));
				info.appendChild(partyInfoP);

				var priceP = document.createElement("p");
				priceP.appendChild(document.createTextNode("$" + price));
				info.appendChild(priceP);
			}
		}

		function clearInfo(){
			var info = document.getElementById("info");
			while(info.lastChild != null){
				info.removeChild(info.lastChild);
			}
		}

	</script>

	<style>

		body{
			padding: 20px;
		}

		#eventSelect,#info{
			display: inline-block;
			width: 250px;
			vertical-align: top;
		}

		#info{
			margin-left: 50px;
		}

		.oneCol{
			width: 250px;
			display: inline-block;
			margin-right: 50px;
			vertical-align: top;
		}

	</style>
</head>

<?php
	
	if($_SERVER['REQUEST_METHOD'] == 'POST'){

		$name = $address = $city = $state = $zip = $cardNumber = $cardType = $cardExpMonth = "";
		$cardExpYear = $cardCVV = $cardFirst = $cardLast = $billingAddr = $subTotal = $tax = $total = "";

		$name = $_POST['name'];
		$address = $_POST['address'];
		$city = $_POST['city'];
		$state = $_POST['state'];
		$zip = $_POST['zip'];
		$cardNumber = $_POST['cardNumber'];
		$cardType = $_POST['cardType'];
		$cardExpMonth = $_POST['cardExpMonth'];
		$cardExpYear = $_POST['cardExpYear'];
		$cardCVV = $_POST['cardCVV'];
		$cardFirst = $_POST['cardFirst'];
		$cardLast = $_POST['cardLast'];
		$billingAddr = $_POST['billingAddr'];
		$subTotal = $_POST['subTotal'];

		$client_id = 'AVJnchB-IzspWNkcsi9q5BCnl-T2YmOkusUTDA0aA5C6pLFlvWYKT8ap3eFY';
		$client_secret = 'EAYclxDc6dQy04DZ3tFoCwCC6KUvAuXEokGiGjxPoZk8JKf3qCBro-dFwqKN';

		$common_base_url = 'sdk-core-php-master/sdk-core-php-master/lib';

		echo $common_base_url;
		
		include($common_base_url . '/common/PPApiContext.php');
		require_once("rest-api-sdk-php-master/lib/PayPal/rest/ApiContext.php");
		require_once("rest-api-sdk-php-master/lib/PayPal/Api/Address.php");
		require_once("rest-api-sdk-php-master/lib/PayPal/Api/Payment.php");
		require_once("rest-api-sdk-php-master/lib/PayPal/Api/CreditCard.php");
		require_once("rest-api-sdk-php-master/lib/PayPal/Api/FundingInstrument.php");
		require_once("rest-api-sdk-php-master/lib/PayPal/Api/Payer.php");
		require_once("rest-api-sdk-php-master/lib/PayPal/Api/AmountDetails.php");
		require_once("rest-api-sdk-php-master/lib/PayPal/Api/Amount.php");
		require_once("rest-api-sdk-php-master/lib/PayPal/Api/Transaction.php");
		require_once("rest-api-sdk-php-master/lib/PayPal/Api/PaymentExecution.php");
		require_once("rest-api-sdk-php-master/lib/PayPal/Api/Links.php");

		$oauthCredential = new OAuthTokenCredential($client_id, $client_secret);
		$accessToken = $oauthCredential->getAccessToken(array('mode' => 'sandbox'));

		$apiContext = new ApiContext($oauthCredential);

		$addr = new Address();
		$addr->setLine1($address);
		$addr->setCity($city);
		$addr->setCountry_code('US');
		$addr->setPostal_code($zip);
		$addr->setState($state);

		$card = new CreditCard();
		$card->setNumber($cardNumber);
		$card->setType($cardType);
		$card->setExpire_month($cardExpMonth);
		$card->setExpire_year($cardExpYear);
		$card->setCvv2($cardCVV);
		$card->setFirst_name($cardFirst);
		$card->setLast_name($cardLast);
		$card->setBilling_address($billingAddr);

		$fi = new FundingInstrument();
		$fi->setCredit_card($card);

		$payer = new Payer();
		$payer->setPayment_method('credit_card');
		$payer->setFunding_instruments(array($fi));

		$amountDetails = new AmountDetails();
		$amountDetails->setSubtotal($subTotal);
		$amountDetails->setTax('0.00');
		$amountDetails->setShipping('0.00');

		$amount = new Amount();
		$amount->setCurrency('USD');
		$amount->setTotal($subTotal);
		$amount->setDetails($amountDetails);

		$transaction = new Transaction();
		$transaction->setAmount($amount);
		$transaction->setDescription('This is the payment transaction description.');

		$payment = new Payment();
		$payment->setIntent('sale');
		$payment->setPayer($payer);
		$payment->setTransactions(array($transaction));
		$payment->setRedirectUrls(array('http://localhost/paymentExecution.php'));

		$response = $payment->create($apiContext);

		foreach($response->links as $link){
			if($link->getRel() == 'approval_url'){
				header("Location: $link->getHref()");
			}
		}
	}
	/*else{
		require_once("rest-api-sdk-php-master/lib/PayPal/Auth/OAuthTokenCredential.php");
		$oauthCredential = new OAuthTokenCredential($client_id, $client_secret);
		$accessToken = $oauthCredential->getAccessToken(array('mode' => 'sandbox'));
	}*/
?>

<body>

	<h1>Payment</h1>

	<select id="eventSelect" onchange="eventSelect(this);">
		<option>Please select an option...</option>
		<option>Party 1</option>
		<option>Party 2</option>
		<option>Party 3</option>
		<option>Party 4</option>
	</select>

	<div id="info">
		<p>Please select an event to see information!</p>
	</div>

	<hr />

	<h3>Payment Information</h3>
	<form method="post" name="payForm" id="payForm">
		<div class="oneCol">
			<h4>Name on Card</h4>
			<input type="text" name="name" />
			<h4>Address</h4>
			<input type="text" name="address" />
			<h4>City</h4>
			<input type="text" name="city" />
			<h4>State</h4>
			<select name="state">
				<option>NY</option>
			</select>
			<h4>Zip Code</h4>
			<input type="text" name="zip" />
			<h4>Country</h4>
			<select name="country">
				<option>USA</option>
			</select>
		</div>
		<div class="oneCol">
			<h4>Card Number</h4>
			<input type="text" name="cardNumber" />
			<h4>Expiration Month</h4>
			<select name="expMonth">
				<option>Jan</option>
			</select>
			<h4>Expiration Year</h4>
			<select name="expYear">
				<option>2014</option>
			</select>		
			<h4>CVV2</h4>
			<input type="text" name="cvv2" maxlength="3"/>
		</div>
		<div>
			<input type="submit" class="btn-primary" value="Submit" />
		</div>
	</form>

</body>

</html>
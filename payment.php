<!DOCTYPE html>
<html>

<head>
	<?php

		//require_once 'bootstrap.php';
		session_start();

		if(isset($_SESSION['logged_in']) && 
			($_SESSION['process_step'] == 'schedule_apt' || $_SESSION['process_step'] == 'payment')){
			
			$_SESSION['process_step'] = 'payment';
			setcookie("process_step", "payment");

			$back = true;
			$next = true;
			$finish = true;
			$cancel = true;
		}
		else if(!isset($_SESSION['logged_in'])){
			header("Location: " . __DIR__ . "/index.php");
		}
		else{
			echo 'Came from wrong step!';
		}

	?>
	<title>Schedule an Appointment | Payment</title>

	<script>

		window.onload = function(){
			updateHeader("payment");
			switchPayButton("next");
		};

		
	</script>
</head>

<body>
	<?php require_once('header.php'); ?>
	<?php require_once('cancelModal.php') ?>

	<div id="loaderDiv">
		<img src="./img/loader.gif" alt="Loading..."/>
		<h4>Please wait...</h4>
	</div>

	<div class="container-fluid">
		<div class="row">
			<div class="offset1">
				<h3>Select your Payment Type</h3>
				<hr />
			</div>
		</div>

		<div class="row">
			<div class="offset1">
				<select id="select_pay_type" onchange="showHideCredit(this);">
					<option>PayPal</option>
					<!--<option>Credit Card</option>-->
				</select>
			</div>
		</div>

		<div id="creditForm">
			<div class="row title">
				<div class="offset1">
					<h3>Credit Card Information</h3>
					<hr />
				</div>
			</div>

			<div class="row">
				<div class="offset1 span3">
					<label for="creditName">Name on Card</label>
				</div>
				<div class="offset1 span3">
					<input type="text" id="creditName"/>
				</div>
			</div>
				
			<div class="row">
				<div class="offset1 span3">
					<label for="cardNumber">Card Number</label>
				</div>
				<div class="offset1 span3">
					<input type="text" id="cardNumber" />
				</div>
			</div>

			<div class="row">
				<div class="offset1 span3">
					<label for="expMonth">Expiration</label>
				</div>
				<div class="offset1 span1">
					<select id="expMonth" style="width: 70px;">
						<option>01</option>
						<option>02</option>
						<option>03</option>
						<option>04</option>
						<option>05</option>
						<option>06</option>
						<option>07</option>
						<option>08</option>
						<option>09</option>
						<option>10</option>
						<option>11</option>
						<option>12</option>
					</select>
				</div>
				<div class="span2">
					<select id="expYear" style="width: 100px; margin-left: 20px;"><!-- TODO: Set automatic filling of years -->
						<option>2014</option>
						<option>2015</option>
						<option>2016</option>
						<option>2017</option>
						<option>2018</option>
						<option>2019</option>
						<option>2020</option>
						<option>2021</option>
						<option>2022</option>
					</select>
				</div>
			</div>

			<div class="row">
				<div class="offset1 span3">
					<label for="cvvNum">CVV2</label>
				</div>
				<div class="offset1 span1">
					<input type="text" name="cvv2" maxlength="3"/>
				</div>
			</div>
			<br />
			<div class="row">
				<div class="offset1 span1">
					<input type="button" class="btn btn-primary"
						value="Save Credit Card" onclick="saveCreditCard();"/>
				</div>
				<div class="offset3 span6">
					<p class="success" id="card_saved_success">Credit Card Saved!</p>
					<p class="error" id="card_saved_error">Error saving Credit Card. Please try again.</p>
				</div>
			</div>
		</div>
	</div>

	<?php require_once('footer.php'); ?>

</body>

</html>
<?php

require_once __DIR__ . '/../bootstrap.php';
session_start();

$amount = $_GET['order']['amount'];
$description = $_GET['order']['description'];

$availableFundingInstruments = array();
$availableFundingInstruments[] = 'paypal';
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<title>Confirm Event Payment</title>
</head>
<body>
	<div class='container' id='content'>
		<h2>Order Confirmation</h2>
		<form action="order_place.php" id='order' method='post'>
			<div class='control-group'>
				<label for="order_amount">Amount</label>
				<div>
					<label><?php echo $amount; ?></label><input id="order_amount"
						name="order[amount]" type="hidden" value="<?php echo $amount; ?>" />
				</div>
			</div>
			<div>
				<label for="order_description">Description</label>
				<div>
					<label><?php echo $description; ?></label><input id="order_description"
						value="<?php echo $description; ?>" type="hidden" name="order[description]" />
				</div>
			</div>
			<div>
				<label for="order_payment_method">Payment Method</label>
				<div>
					<select id="order_payment_method" name="order[payment_method]">
						<?php foreach ($availableFundingInstruments as $fi){ ?>
						<option value="<?php echo $fi; ?>"><?php echo $fi ?></option>option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div>
				<input class="btn btn-primary" name="commit" type="submit" value="Place Order" />
			</div>
		</form>
	</div>
</body>
</html>
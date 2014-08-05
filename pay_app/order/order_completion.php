<!DOCTYPE html>
<html>

<head>
	<?php

		// Page used when PayPal is the payment method

		require_once __DIR__ . '/../../bootstrap.php';
		session_start();

		$next = $back = $cancel = $finish = false;

		if(isset($_GET['success'])){

			if($_GET['success'] == 'true' && isset($_GET['PayerID']) && isset($_GET['orderId'])){

				$orderId = $_GET['orderId'];
				require_once './../common/appointment.php';
				require_once './../common/user.php';
				require("../../phpmailer/class.phpmailer.php");

				try{
					$order = getOrder($orderId);
					$appt = getAppt($order['appt_id']);
					$startDateTime = new DateTime($appt['start_time']);
					$startDateTime = date_format($startDateTime, 'g:ia \o\n l, F jS Y');

					if($_SESSION['logged_in']){
						$email = getUser($order['user_id'])['email'];
					}
					else{
						$email = $_SESSION['guest_email'];
					}

					$payment = executePayment($order['payment_id'], $_GET['PayerID']);
					updateOrder($orderId, $payment->getState());

					$messageType = "success";
					$message = "Your payment was successful! Your order id is $orderId, and a confirmation
						 email has been sent to $email";

					if(isset($_SESSION['featured']) && $_SESSION['featured'] == true){
						echo '
							<script>
								window.onload = function(){
									createAppointment(' . $_SESSION['appt_id'] . ');
								}
							</script>
						';
					}
					
					$mail = new PHPMailer();
					$mail->IsSMTP();
					$mail->SMTPAuth = true;
					$mail->Username="thesandboxplayground@gmail.com";
					$mail->Password="playground";
					$webmaster_email = "noreply@thesandboxplayground.com";
					
					$mail->From = $webmaster_email;
					$mail->FromName = "The Sandbox Playground";
					$mail->AddAddress($email);
					$mail->AddReplyTo($webmaster_email, "The Sandbox Playground");
					$mail->WordWrap = 50;
					$mail->IsHTML(true);		

					$subject = "The Sandbox Playground Order - Confirmation";
					$body = "<p>Dear Sandbox User,</p>
							<br /><p>Thank you for scheduling an event at The Sandbox Playground!
							Your order id is: $orderId,<br />
							and your event is scheduled for " . $startDateTime . "
							</p><br />
							<p>We can't wait to see you there!</p><br /><br />
							<p>Sincerely,</p>
							<p>The Sandbox Playground</p>";
					$altBody = "Dear Sandbox User,\n
							Thank you for scheduling an event at The Sandbox Playground!\n\n
							Your order id is: $orderId,\n
							and your event is scheduled for $startDateTime .\n\n
							We can't wait to see you there!\n\n\n
							Sincerely,\n
							The Sandbox Playground";

					$mail->Subject = $subject;
					$mail->Body = $body;
					$mail->AltBody = $altBody;
					$mail->Send();

					unset($_SESSION['appt_id']);
					unset($_SESSION['process_step']);
					unset($_SESSION['order_price']);
					unset($_SESSION['order_desc']);

				} catch(PPConnectionException $ex){
					$message = parseApiError($ex->getData());
					$messageType = "error";
				} catch(Exception $ex){
					$message = $ex->getMessage();
					$messageType = "error";
				}
			}
			else{
				$messageType = "error";
				$message = "Your payment was cancelled.";
			}
		}
	?>

	<title>Schedule an Appointment | Complete</title>

	<link href="./../../css/header.css" rel="stylesheet" type="text/css" />
	<link href="./../../css/metro-bootstrap.css" rel="stylesheet" type="text/css" />
	<link href="./../../css/site.css" rel="stylesheet" type="text/css" />
	<script src="./../../scripts/jquery.js"></script>
	<script src="./../../scripts/site.js"></script>
	<script src="./../../scripts/bootstrap-modal.js"></script>
	<script src="./../../scripts/bootstrap-transition.js"></script>
	<link href="./../../css/footer.css" rel="stylesheet" type="text/css" />
</head>

<body>

	<div class="container">
		<?php if(isset($message) && isset($messageType)) { ?>
		<div class="row title">
			<?php if($messageType == "success"){ ?>
			<div class="span10 offset1 alert alert-success">
				<button class="close" data-dismiss="alert">&times;</button>
				<?php echo $message; ?>
			</div>
			<?php } else { ?>
			<div class="span10 offset1 alert">
				<button class="close" data-dismiss="alert">&times;</button>
				<?php echo $message; ?>
			</div>
			<?php } ?>
		</div>
		<?php } ?>

		<?php if($messageType == "success"){ ?>
			<div class="row">
				<div class="offset1 span10">
					<h3>Order Summary</h3>
					<hr />
				</div>
			</div>

			<div class="row">
				<div class="offset2">
					<h3><?php echo $appt['title']; ?></h3>
				</div>
			</div>

			<div class="row">
				<div class="offset2">
					<h4>Price: $<?php echo $order['amount']; ?></h4>
				</div>
			</div>

			<div class="row">
				<div class="offset2">
					<h4>Date/Time: <?php 

						$date = date_create($appt['start_time']);
						echo date_format($date, 'g:i A \o\n m/d/Y');

					?></h4>
				</div>
			</div>
		<?php } ?>


		<?php if($_SESSION['logged_in'] == true){ ?>
			<div class="row title">
				<div class="offset5">
					<a href="./orders.php">Return to my orders</a>
				</div>
			</div>
		<?php }
		else if($_SESSION['guest_account'] == true){ ?>
			<div class="row title">
				<div class="offset5">
					<a href="http://thesandboxplayground.com/">Return to The Sandbox Playground</a>
				</div>
			</div>
		<?php } ?>

	</div>

	<?php require_once './../../footer.php'; ?>

</body>
</html>
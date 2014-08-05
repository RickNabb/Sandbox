<?php

require_once __DIR__ . '/../../bootstrap.php';

$back = $next = $cancel = $finish = false;

if(!isset($_SESSION)){
	session_start();
}

try{
	$orders = getOrders($_SESSION['user_id']);// GET USER ID)
}
catch(Exception $ex){
	if(!isset($message)){
		$message = $ex->getMessage();
		$messageType = "error";
	}
	$orders = array();
}
?>
<!DOCTYPE html>
<html lang='en'>
<head>
	<title>Sandbox Event Orders</title>

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
	<div class="container-fluid">
		<!--<div class="row" id="local_storage_alert">	-->		
		<?php if($_SESSION['logged_in']){ ?>

			<div class="row title">
				<div class="offset1 span11">
					<h3>Orders</h3>
					<hr />
				</div>
			</div>
			<div class="row">
				<div class="offset1 span12">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Payment ID</th>
								<th>Amount ($)</th>
								<th>Date & Time</th>
								<th>Description</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach($orders as $order){ 
								if($order['state'] == 'approved'){ ?>
							<tr class="hoverRow">							
								<td><?php echo $order['payment_id'];?></td>
								<td><?php echo $order['amount'];?></td>
								<td><?php echo $order['created_time'];?></td>
								<td><?php echo $order['description'];?></td>
							</tr>
							<?php } } ?>
						</tbody>
					</table>
				</div>
			</div>

			<div class="row title">
				<div class="offset1">
					<a href="http://localhost/Sandbox/">Back to event creation</a>
				</div>
			</div>

		<?php } else { ?>

			<div class="row">
				<h3 style="text-align: center;">Sorry!</h3>
			</div>
			<div class="row">
				<h3 style="text-align: center;">You must be logged in to access this feature.</h3>
			</div>

			<div class="row title">
				<div class="offset1">
					<a href="http://localhost/Sandbox/">Back to event creation</a>
				</div>
			</div>
		<?php } ?>		
	</div>

	<?php include('../../footer.php'); ?>
</body>
</html>
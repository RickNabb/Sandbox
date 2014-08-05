<!DOCTYPE html>
<html>
<head>
	<link href="./css/metro-bootstrap.css" rel="stylesheet" type="text/css" />
	<link href="./css/site.css" rel="stylesheet" type="text/css" />
	<script src="./scripts/jquery.js"></script>
	<script src="./scripts/site.js"></script>

	<?php

		require_once 'bootstrap.php';
		require_once __DIR__ . '/pay_app/common/user.php';
		session_start();

		$next = false;
		$back = false;
		$finish = false;
		$cancel = false;

		$userId = $guid = '';

		if(isset($_GET['userId'])){
			$userId = $_GET['userId'];
		}

		if(isset($_GET['guid'])){
			$guid = $_GET['guid'];
		}

		$row = getUser($userId);

		if($row != ''){			
			$unactivated = ($row['confirmGUID'] == $guid) && ($row['active'] == '0');
		}
	?>

	<title>Schedule an Appointment</title>

	<script>

		window.onload = function(){			
			$("#loaderDiv")
				.hide()
				.ajaxStart(function(){
					$(this).show();
				})
				.ajaxStop(function(){
					$(this).hide();
				});
		}		
	</script>

</head>

<body>	
	<div id="loaderDiv">

	</div>

	<div class="container-fluid">
		<div class="row title">
			<div class="offset1">
				<h3>Confirm your Account</h3>
				<hr />
			</div>
		</div>
		
		<?php if(isset($unactivated) && $unactivated == true){ 

			activateUser($userId);
			?>
		<div class="row">
			<div class="offset1">
				<h4 class="success">Your account has been activated!</h4>
				<a href="http://thesandboxplayground.com">Go back to The Sandbox Playground</a>
			</div>
		</div>
		<?php } else if(isset($unactivated)){ ?>
		<div class="row">
			<div class="offset1">
				<p>Your account is already activated.</p>
				<a href="http://thesandboxplayground.com">Go back to The Sandbox Playground</a>
			</div>
		</div>
		<?php } else { ?>
		<div class="row">
			<div class="offset1">
				<h4 style="color: #60a0ff;">Sorry! We encountered an error.</h4>
				<a href="http://thesandboxplayground.com">Go back to The Sandbox Playground</a>
			</div>
		</div>
		<?php } ?>
	</div>

	<?php require_once('footer.php'); ?>
</body>

</html>
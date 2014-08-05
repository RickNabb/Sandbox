<!DOCTYPE html>
<html>
<head>

	<?php

		require_once 'bootstrap.php';
		session_start();

		$next = true;
		$back = false;
		$finish = false;
		$cancel = false;

		if(isset($_SESSION['process_step']) && $_SESSION['process_step'] != 'login'){
			if($_SESSION['process_step'] == 'schedule_apt'){
				header("Location: /Sandbox/schedule.php?method=update");
			}
			else if($_SESSION['process_step'] == 'payment'){			
				header("Location: /Sandbox/payment.php");
			}
		}
		else{
			unset($_SESSION['logged_in']);
			unset($_SESSION['user_id']);
			unset($_SESSION['appt_id']);
			$_SESSION['process_step'] = 'login';
		}
		
	?>

	<title>Schedule an Appointment</title>

	<script>

		window.onload = function(){
			updateHeader("login");

			$("#ftr_next")[0].setAttribute("onclick", "validateLogin();");
			$("#ftr_next").onclick = function(){
				validateLogin();
			}			
		}
	</script>

</head>

<body>
	<?php require_once('header.php'); ?>

	<div id="loaderDiv">
		<img src="./img/loader.gif" alt="Loading..."/>
		<h4>Please wait...</h4>
	</div>

	<div id="createAccountModal" class="modal hide fade" tabindex="-1" aria-labelledby="createAccountModalLabel"
		aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="createAccountModalLabel">Create An Account</h3>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="span3">
					<label for="createActUsername">Email Address</label>
				</div>
				<input id="createActUsername"type="text" placeholder="example@email.com"
					onblur="validateCreateActUsername();"/>
				<span id="createActUsernameValidate" class="error">Invalid email address</span><br />
			</div>

			<div class="row">
				<div class="span3">
					<label for="createActUsername">Password</label>
				</div>
				<input id="createActPwd" type="password" /><br />
			</div>

			<div class="row">
				<div class="span3">
					<label for="createActUsername">Confirm Password</label>
				</div>
				<input id="createActPwdConfirm" type="password" onblur="validateCreateActPassMatch();"/><br />
				<span id="createActPwdValidate" class="error">Passwords don't match</span>
			</div>
		</div>
		<div class="modal-footer">		
			<input type="button" class="btn btn-info" value="Create Account" onclick="createAccount();"/>
		</div>
	</div>

	<div id="guestEmailModal" class="modal hide fade" tabindex="-1" aria-labelledby="guestEmailModalLabel"
		aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="guestEmailModalLabel">Use a Guest Account</h3>
		</div>
		<div class="modal-body">
			<div class="row">
				<div class="span7">
					<p>Please enter your email address, just so we can send you some confirmation emails
					 once you complete your order!</p>
				</div>				
			</div>
			<div class="row">
				<div class="span4">
					<input id="guestEmail"type="text" placeholder="example@email.com"
					onblur="validateGuestEmail();"/>
					<span id="guestEmailValidate" class="error">Invalid email address</span><br />			
				</div>
			</div>		
		</div>
		<div class="modal-footer">		
			<input type="button" class="btn btn-info" value="Continue" onclick="submitGuestEmail();"/>			
		</div>
	</div>

	<div class="container-fluid">
		<div class="row">
			<div class="offset1">
				<h3>Please Log In to Start Planning your Party!</h3>
				<hr />
			</div>			
		</div>

		<div class="row" id="validateText" style="display: none;">
			<div class="offset2">
				<p class="error">Invalid username/password. Please try again.</p>
			</div>
		</div>

		<div class="row">
			<div class="offset2">				
				<input type="text" id="username" placeholder="Email Address..." />
			</div>
		</div>
		<div class="row">
			<div class="offset2">
				<input type="password" id="password" />
			</div>
		</div>		
		<div class="row">
			<div class="offset2">				
				<br /><a href="#createAccountModal" data-toggle="modal">Create an account</a>
			</div>
		</div>
		<div class="row">
			<br /><br />
			<div class="offset2 span10 alert">				
				<p>You can continue without logging in, but you will be missing out on some features
					such as saving credit card information and order history.</p>
				<a href="#guestEmailModal" data-toggle="modal">Continue without logging in?</a>
			</div>
		</div>
	</div>

	<?php require_once('footer.php'); ?>
</body>

</html>
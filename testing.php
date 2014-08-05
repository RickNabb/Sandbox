<?php

	require_once __DIR__ . '/bootstrap.php';
	require_once __DIR__ . '/pay_app/common/util.php';
	require("phpmailer/class.phpmailer.php");

	function validateLogin(){

		if($_SERVER['method'] == "GET"){
			$username = $password = $remember = '';

			$username = 'nrabb@outlook.com';
			$password = 'password1234';
			$remember = 'true';

			$conn = getConnection();
			$query = sprintf("SELECT * FROM %s WHERE email='%s' AND password='%s'",
				USERS_TABLE,
				mysql_real_escape_string($username),
				mysql_real_escape_string(encrypt($password)));

			$result = mysql_query($query, $conn);

			if(!$result){
				$errMsg = "Error validating login: " . mysql_error($conn);
				mysql_close($conn);
				throw new Exception($errMsg);
			}

			if($remember){
				setcookie('sandbox_event_scheduler_login', $username);
			}

			$row = mysql_fetch_assoc($result);
			echo $row['email'] . '<br />';
			
			mysql_close();

			echo $row != '' ? 'true' : 'false';
		}
	}	

	function sendConfirmEmail($userId, $email, $guid){
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

		$subject = "The Sandbox Playground Account - Confirmation";
		$body = "Dear Sandbox User,\n
				Thank you for registering as a user of The Sandbox Playground!\n\n
				Please follow this confirmation address to finalize your account creation,
				and have full access to our web services: http://localhost/Sandbox/confirmAccount?userId=$userId&&guid=$guid\n\n\n
				Sincerely,\n
				The Sandbox Playground";

		$mail->Subject = $subject;
		$mail->Body = $body;

		if($mail->Send()){
			echo "success";
		}
		else{
			echo "failure";
		}
	}	

	//sendConfirmEmail('1003', 'nrabb@outlook.com', 'FE3D08ED-55BC-44DC-9583-91629841');

?>

<script src="./scripts/jquery.js"></script>

<script>

	function createAppointment(appt_id){

		var input_data = {"appt_id": appt_id};

		$.post('./createApt.php',
		input_data,
		function(data, success){
			if(data == "refreshed"){

				$.post('./createApt.php',
					input_data,
					function(data, success){

						if(data == "success"){
							alert("success with second call!");
						}
					})
			}
			else if(data == "success"){
				alert("success x 1!");
			}
			else{
				alert(data);
			}
		});
	}

	window.onload = function(){

		createAppointment(16);
	}

</script>

<!--<script>

	window.onload = function(){
		$.ajax({
			type: "GET",
			url: "./validateLogin.php?email=nrabb@outlook.com&password=password1234&remember=true",
			/*data: {
				email: 'nrabb@outlook.com',
				password: 'password1234',
				remember: 'true'
			},
			dataType: "json",*/
			success: function(data){
				alert(data);
			},
			error: function(jqXHR, textStatus, errorThrown){
				alert("failure!" + errorThrown);
			}
		});

	}

</script>-->
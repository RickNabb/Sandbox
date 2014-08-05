<?php

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/util.php';

if($_SERVER['REQUEST_METHOD'] == "POST"){

	require_once __DIR__ . '/../../bootstrap.php';
	require("/../../phpmailer/class.phpmailer.php");

	if(isset($_POST['method'])){
		$method = $_POST['method'];
	}
	
	if($method == "addUser"){

		$email = $_POST['email'];
		$password = $_POST['password'];

		$guid = addUser($email, $password);
		echo $guid;
	}

}
else if($_SERVER['REQUEST_METHOD'] == "GET"){
	echo '';
}

/**
 * Create a new User
 * @param string $userId Buyer's user id
 * @param string $paymentId payment id returned by paypal
 * @param string $state state of this User
 * @param string $amount payment amount in DD.DD format
 * @param string $description a description about this payment
 * @throws Exception
 */
function addUser($email, $password){
	$conn = getConnection();
	$guid = GUID();

	$checkQuery = sprintf("SELECT * FROM %s WHERE email='%s'",
		USERS_TABLE,
		mysql_real_escape_string($email));
	$result = mysql_query($checkQuery, $conn);

	if(mysql_fetch_assoc($result) == ""){
		$query = sprintf("INSERT INTO %s(email, password, confirmGUID, active)
			VALUES('%s', '%s', '%s', '%d')",
			USERS_TABLE,
			mysql_real_escape_string($email),
			mysql_real_escape_string(encrypt($password)),
			mysql_real_escape_string($guid),
			0);

		$result = mysql_query($query, $conn);
		if(!$result){
			$errMsg = "Error creating new user: " . mysql_error($conn);
			mysql_close($conn);
			throw new Exception($errMsg);
		}
		$userId = mysql_insert_id($conn);
		mysql_close($conn);

		$success = sendConfirmEmail($userId, $email, $guid);

		return $success;
	}
	else{
		return "existing";
	}
}

/*
* CREDIT TO The phunction PHP framework (http://sourceforge.net/projects/phunction/)
*/
function GUID()
{
    if (function_exists('com_create_guid') === true)
    {
        return trim(com_create_guid(), '{}');
    }

    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
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
	$body = "<p>Dear Sandbox User,</p>
			<br /><p>Thank you for registering as a user of The Sandbox Playground!\n\n
			Please follow this confirmation address to finalize your account creation,
			and have full access to our web services: <a href='http://localhost/Sandbox/confirmAccount.php?userId=$userId&&guid=$guid'>
			http://localhost/Sandbox/confirmAccount?userId=$userId&&guid=$guid</a>
			</p><br /><br />
			<p>Sincerely,</p>
			<p>The Sandbox Playground</p>";
	$altBody = "Dear Sandbox User,\n
			Thank you for registering as a user of The Sandbox Playground!\n\n
			Please follow this confirmation address to finalize your account creation,
			and have full access to our web services: http://localhost/Sandbox/confirmAccount.php?userId=$userId&&guid=$guid\n\n\n
			Sincerely,\n
			The Sandbox Playground";

	$mail->Subject = $subject;
	$mail->Body = $body;
	$mail->AltBody = $altBody;

	if($mail->Send()){
		echo "success";
	}
	else{
		echo "failure";
	}
}

function updateUser($userId, $email, $password){

	$conn = getConnection();

	$query = sprintf("UPDATE %s SET (email, password) = ('%s', '%s') WHERE user_id='%s'",
		mysql_real_escape_string($email),
		mysql_real_escape_string(encrypt($password)));
	$result = mysql_query($query, $conn);
	if(!$result){
		$errMsg = "Error updating user record: " . mysql_error($conn);
		mysql_close($conn);
		throw new Exception($errMsg);
	}
	$isUpdated = mysql_affected_rows($conn);

	return $isUpdated;
}

function activateUser($userId){

	$conn = getConnection();
	$query = "UPDATE " . USERS_TABLE . " SET active = 1 WHERE user_id=$userId";

	$result = mysql_query($query, $conn);
	if(!$result){
		$errMsg = "Error activating user $userId: " . mysql_error($conn);
		mysql_close($conn);
		throw new Exception($errMsg);
	}

	$isUpdated = mysql_affected_rows($conn);
	return $isUpdated;
}

function getUser($userId){
	$conn = getConnection();
	$query = sprintf("SELECT * FROM %s WHERE user_id='%s'",
		USERS_TABLE,
		mysql_real_escape_string($userId));
	$result = mysql_query($query, $conn);
	if(!$result){
		$error = "Error retrieving user: " . mysql_error($conn);
		mysql_close($conn);
		throw new Exception($error);
	}

	$row = mysql_fetch_assoc($result);
	mysql_close($conn);
	return $row;
}

?>
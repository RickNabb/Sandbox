<?php

	require_once __DIR__ . '/bootstrap.php';
	require_once __DIR__ . '/pay_app/common/util.php';

	if($_SERVER['REQUEST_METHOD'] == "GET"){
		session_start();
		$email = $password = '';

		$email = $_GET['email'];
		$password = $_GET['password'];

		$conn = getConnection();
		$query = sprintf("SELECT * FROM %s WHERE email='%s' AND password='%s'",
			USERS_TABLE,
			mysql_real_escape_string($email),
			mysql_real_escape_string(encrypt($password)));

		$result = mysql_query($query, $conn);

		if(!$result){
			$errMsg = "Error validating login: " . mysql_error($conn);
			mysql_close($conn);
			throw new Exception($errMsg);
		}

		$row = mysql_fetch_assoc($result);

		mysql_close();

		if($row != ''){
			$_SESSION['logged_in'] = true;
			$_SESSION['user_id'] = $row['user_id'];
			echo 'true';
		}
		else{
			echo 'false';
		}
	}
	else if($_SERVER['REQUEST_METHOD'] == 'POST'){

		session_start();
		$_SESSION['guest_account'] = true;
		$_SESSION['logged_in'] = false;
		$_SESSION['guest_email'] = $_POST['guest_email'];

		echo 'guest_account';
	}
?>
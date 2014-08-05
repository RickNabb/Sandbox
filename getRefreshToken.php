<?php

	require_once __DIR__ . '/bootstrap.php';
	require_once('google-api-php-client/src/Google_Client.php');
	require_once('google-api-php-client/src/contrib/Google_CalendarService.php');

	$cScope = 'https://www.googleapis.com/auth/calendar';
	$cClientId = '443906651386-tqgubedlbak5ivv1tjrs89qkhaipmcvl.apps.googleusercontent.com';
	$cClientSecret = 'bQlbBMTL8IvO25iBqS5pbN4c';
	$cRedirectURI = 'urn:ietf:wg:oauth:2.0:oob';

	$cAuthCode = '4/KZap8QDvMfs8EJzT9bM47_fR6rcV.ssn2s8SKw4kfXmXvfARQvtiiehsbjwI';

	$cTokenUrl = 'https://accounts.google.com/o/oauth2/token';
	/*$rsPostData = array(
			'code' => $cAuthCode,
			'client_id' => $cClientId,
			'client_secret' => $cClientSecret,
			'redirect_uri' => $cRedirectURI,
			'grant_type' => 'authorization_code'
		);

	$ch = curl_init($cTokenUrl);
	//'1/uwW6roZbwU1LZJi7dSiJYgwWZKZQQW09SWk4fxzv0uA';

	//curl_setopt($ch, CURLOPT_URL, $cTokenUrl);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $rsPostData);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$cTokenReturn = curl_exec($ch);
	curl_close($ch);
	$oToken = json_decode($cTokenReturn);

	var_dump($oToken);

	if(isset($oToken->refresh_token)){
		echo("You needed a new Refresh Token for your application. Do not lose this!\n");
		echo("Refresh Token = '" . $oToken->refresh_token . "';\n");	
	}*/

	$cRefreshToken = '1/HuCtyi0NgkDOE_BcijwcZrMe_iACkaP-QPxcncLKJg8';

	$rsPostData = array(
			'client_id' => $cClientId,
			'client_secret' => $cClientSecret,
			'refresh_token' => $cRefreshToken,
			'grant_type' => 'refresh_token'
		);

	$ch = curl_init($cTokenUrl);

	//ya29.UwB9GUp0IVrz3CEAAAC0D1Iy4msL9rP8n4TNlmiJOk54E-UQQk3qF4LbZ6l35ccsBPcaciHI-IWrUwvnTtQ

	//curl_setopt($ch, CURLOPT_URL, $cTokenUrl);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $rsPostData);
	curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$cTokenReturn = curl_exec($ch);

	var_dump($cTokenReturn);
	$access_token = json_decode($cTokenReturn)->access_token;

	//session_start();
	//session_destroy();

	/*$client = new Google_Client();
	$client->setClientId($cClientId);
	$client->setClientSecret($cClientSecret);
	$client->setRedirectUri($cRedirectURI);
	$client->setAccessType("offline");

	$service = new Google_CalendarService($client);

	if(isset($_GET['code'])){
		$client->authenticate($_GET['code']);
		$_SESSION['google-api']['access_token'] = $client->getAccessToken();
		header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
	}

	echo '<br/><br/>';

	if(isset($_SESSION['google-api']['access_token'])){
		var_dump($_SESSION);
		$client->setAccessToken($_SESSION['google-api']['access_token']);
		echo "Access token set<br/><br/><br/>";
	}

	if($client->isAccessTokenExpired()){
		echo "Access token epxpired<br/><br/><br/>";
		$conn = getConnection();
		$query = sprintf("SELECT * FROM %s",
			GOOGLE_AUTH_TABLE);

		$result = mysql_query($query, $conn);
		$row = mysql_fetch_assoc($result);

		echo 'Setting refresh token as ' . $row['refresh_token'] . "<br/><br/><br/>";

		$client->refreshToken($row['refresh_token']);
	}
	else{
		echo("Location: http://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'] . "?code="
			. json_decode($client->getAccessToken())->access_token);
	}

	$access_tokens = json_decode($client->getAccessToken());

	if($access_tokens){
		if(isset($access_tokens->refresh_token)){
			// Store in DB the refresh token
			$conn = getConnection();
			$query = sprintf("UPDATE %s SET refresh_token='%s'",
				GOOGLE_AUTH_TABLE,
				$access_tokens->refresh_token);

			$result = mysql_query($query, $conn);

			if(!$result){
				$errMsg = "Error updating refresh token: " . mysql_error($conn);
				mysql_close($conn);
				throw new Exception($errMsg);
			}

			echo $result;
		}

		var_dump($access_tokens);
		$_SESSION['google-api']['access_token'] = $client->getAccessToken();
		var_dump($_SESSION);
		//header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);
	}*/

	/*if(empty($cAuthCode)){
		$rsParams = array(
				'response_type' => 'code',
				'client_id' => $cClientId,
				'redirect_uri'=> $cRedirectURI,
				'scope' => $cScope
			);

		$cOauthURL = 'https://accounts.google.com/o/oauth2/auth?' . http_build_query($rsParams);
		echo("Go to \n$cOauthURL\nand enter the given value into this script under \$cAuthCode\n");
		exit();
	}
	elseif (!empty($cAuthCode)) {
		$cTokenUrl = 'https://accounts.google.com/o/oauth2/token';
		/*$rsPostData = array(
				'code' => $cAuthCode,
				'client_id' => $cClientId,
				'client_secret' => $cClientSecret,
				'redirect_uri' => $cRedirectURI,
				'grant_type' => 'authorization_code'
			);

		$ch = curl_init($cTokenUrl);

		//curl_setopt($ch, CURLOPT_URL, $cTokenUrl);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $rsPostData);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$cTokenReturn = curl_exec($ch);
		curl_close($ch);
		$oToken = json_decode($cTokenReturn);

		if(isset($oToken->refresh_token)){
			echo("You needed a new Refresh Token for your application. Do not lose this!\n");
			echo("Refresh Token = '" . $oToken->refresh_token . "';\n");	
		}

		$ch = curl_init($cTokenUrl);

		$rsPostData = array(
				'client_id' => $cClientId,
				'client_secret' => $cClientSecret,
				'refresh_token' => '1/0c8bmmZ7lsRzhfbByRVW-iAcD2nwh_WYAiCAIB_AKXY',
				'grant_type' => 'authorization_code'
			);

		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $rsPostData);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		$cAccessToken = curl_exec($ch);
		curl_close($ch);
		echo gettype($cAccessToken);
		$accessToken = json_decode($cAccessToken);
		
		if(isset($accessToken->access_token)){

			echo "Here's your access token: " . $accessToken->access_token . ";\n";
		}
		else{
			echo $cAccessToken;
		}*/


	//}

?>
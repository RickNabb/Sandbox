<?php

	session_start();
	require_once("google-api-php-client/src/Google_Client.php");


	$client_id = '14798831816-798jk1fcv8qjfhbq9ci844fdsckj0euv.apps.googleusercontent.com';
	$client_secret = 'w9271HiaQnHLx99nRyU_QMqU';
	$redirect_uri = 'http://localhost/Sandbox/test.php';

	$client = new Google_Client();
	$client->setClientId($client_id);
	$client->setClientSecret($client_secret);
	$client->setRedirectUri($redirect_uri);
	$client->setScopes('email');

	// If logging out - clear access token

	if(isset($_REQUEST['logout'])){
		unset($_SESSION['access_token']);
	}

	// If we have a code back from OAuth2.0, store the result of the authenticate() function in session

	if(isset($_GET['code'])){
		$client->authenticate($_GET['code']);
		$_SESSION['access_token'] = $client->getAccessToken();
		$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
		header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
	}

	// If we have an access token, we are good
	// ELSE redirect to an auth URL

	if(isset($_SESSION['access_token']) && $_SESSION['access_token']){
		$client->setAccessToken($_SESSION['access_token']);
	}
	else{
		$authUrl = $client->createAuthUrl();
	}

	// If we're signed in, we can get the ID token

	if($client->getAccessToken()){
		$_SESSION['access_token'] = $client->getAccessToken();
		$token_data = $client->verifyIdToken()->getAttributes();
	}

	if($client_id == '14798831816-798jk1fcv8qjfhbq9ci844fdsckj0euv.apps.googleusercontent.com' ||
		$client_secret == 'w9271HiaQnHLx99nRyU_QMqU' ||
		$redirect_uri == 'http://people.rit.edu/nmr9601/sandbox/test.php'){

		echo 'missing client secrets';//missingClientSecretsWarning();
	}
?>
<h1>TESTING</h1>
<div class="box">
	<div class="request">
		<?php if (isset($authUrl)): ?>
			<a class='login' href='<?php echo $authUrl; ?>'>Connect Me!</a>
		<?php else: ?>
			<a class='logout' href='?logout'>Logout</a>
		<?php endif ?>
	</div>

	<?php if (isset($token_data)): ?>
		<div class="data">
			<?php var_dump($token_data); ?>
		</div>
	<?php endif ?>
</div>
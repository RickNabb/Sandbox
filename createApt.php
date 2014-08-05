<?php

	require_once __DIR__ . '/bootstrap.php';

	require_once('google-api-php-client/src/Google_Client.php');
	require_once('google-api-php-client/src/contrib/Google_CalendarService.php');

	if($_SERVER['REQUEST_METHOD'] == "POST"){
		
		session_start();

		$appt_id = $_POST['appt_id'];

		$cScope = 'https://www.googleapis.com/auth/calendar';
		$cClientId = '443906651386-tqgubedlbak5ivv1tjrs89qkhaipmcvl.apps.googleusercontent.com';
		$cClientSecret = 'bQlbBMTL8IvO25iBqS5pbN4c';
		$cRedirectURI = 'urn:ietf:wg:oauth:2.0:oob';

		$client = new Google_Client();
		$client->setUseObjects(true);
		$client->setApplicationName("The Sandbox Playground Events");
		$client->setAccessType("offline");
		$client->setScopes($cScope);
		$client->setClientId($cClientId);
		$client->setClientSecret($cClientSecret);
		$client->setRedirectUri($cRedirectURI);		

		// to get auth code
		//$client->authenticate();

		/*
		*{"access_token":"ya29.UwAVDYdQ41ExtiEAAABFKmp2jfe5mJgwSHROeEsh9MzjNyAv9XEhbJRXf8hD51YoezE_jL87yhDcYepGJvY","expires_in":3600,"created":1406829268}
		*{"access_token":"ya29.UwDHCJIM5Mzi8iEAAAD8BA8qPnLiRMXWea8WOS5x9W92ycfNaA9ZePGqzsnZ3yGg2hW0HR9-Yo4K-6stLU8","expires_in":3600,"created":1406839753}
		*/

		$conn = getConnection();
		$query = sprintf("SELECT* FROM %s",
			GOOGLE_AUTH_TABLE);

		$result = mysql_query($query, $conn);
		$row = mysql_fetch_assoc($result);

		$client->setAccessToken($row['access_token']);

		//echo $row['access_token'];
		//$tokens =  json_decode($row['access_token']);

		//echo '<br/><br/>';
		//var_dump($tokens);

		if($client->isAccessTokenExpired()){
			//echo '<br/><br/>test<br/><br/><br/>';
			$client->refreshToken($row['refresh_token']);
			
			$query = sprintf("UPDATE %s SET access_token='%s'",
				GOOGLE_AUTH_TABLE,
				$client->getAccessToken());

			mysql_query($query, $conn);

			$result = mysql_affected_rows($conn) == '1' && !$client->isAccessTokenExpired();

			//header('Location: http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF']);

			echo $result == true ? 'refreshed' : 'failure';
		}
		else{
			$client->setAccessToken($row['access_token']);

			$cal = new Google_CalendarService($client);

			$conn = getConnection();
			$query = sprintf("SELECT * FROM %s WHERE appt_id='%s'",
				APTS_TABLE,
				mysql_real_escape_string($appt_id));

			$result = mysql_query($query, $conn);

			if(!$result){
				$errMsg = "Error creating google calendar event for appointment $appt_id: " . mysql_error($conn);
				mysql_close($conn);
				throw new Exception($errMsg);
			}

			$row = mysql_fetch_assoc($result);
			$startDateTime = $row['start_time'];
			$endDateTime = $row['end_time'];

			$startDateTimeObj = new DateTime($startDateTime);
			$endDateTimeObj = new DateTime($endDateTime);

			$startDateTimeObj->add(new DateInterval('PT6H'));
			$endDateTimeObj->add(new DateInterval('PT6H'));

			$title = $row['title'];
			$location = $row['location'];

			$event = new Google_Event();
			$event->setSummary($title);
			$event->setLocation($location);

			date_default_timezone_set("America/New_York");		

			$start = new Google_EventDateTime();
			$start->setTimeZone('America/New_York');
			$start->setDateTime($startDateTimeObj->format(DateTime::RFC3339));

			$event->setStart($start);		

			$end = new Google_EventDateTime();
			$end->setTimeZone('America/New_York');
			$end->setDateTime($endDateTimeObj->format(DateTime::RFC3339));

			$event->setEnd($end);

			/*$attendees = array();
			foreach($attendants as $attendant){
				$attendee = new Google_EventAttendee();
				$attendee->setEmail($attendant);
				$attendees[] = $attendee;
			}

			$event->attendees = $attendees;*/
			$finalEvent = $cal->events->insert('primary', $event);
			$success = true;

			echo 'success';
		}
	}
		
?>
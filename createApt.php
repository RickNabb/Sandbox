<?php

	echo ("<h1>Test1</h1>");

	if($_SERVER['REQUEST_METHOD'] == 'POST'){
		echo ("<h1>Test2</h1>");
		require_once('google-api-php-client/src/Google_Client.php');
		require_once('google-api-php-client/src/contrib/Google_CalendarService.php');		

		$client = new Google_Client();
		$client->setApplicationName("Google Calendar PHP Starter Application");

		// Visit https://code.google.com/apis/console?api=calendar to generate your
		// client id, client secret, and to register your redirect uri.
		$client->setClientId('14798831816-798jk1fcv8qjfhbq9ci844fdsckj0euv.apps.googleusercontent.com');
		$client->setClientSecret('w9271HiaQnHLx99nRyU_QMqU');
		$client->setRedirectUri('http://localhost/Sandbox');
		//$client->setDeveloperKey('AIzaSyDfHKFFZv2W07hpjnp3GTSrGiLUTyCqW9A');

		echo ("<h1>Test3</h1>");		

		$cal = new Google_CalendarService($client);

		$title = $date = $startTimeHour = $startTimeMinute = $endTimeHour = $endTimeMinute = $attendants = '';

		$title = $_POST['title'];
		$location = $_POST['location'];
		$startDate = $_POST['startDate'];
		$endDate = $_POST['endDate'];
		$startTimeHour = $_POST['startTimeHour'];
		$startTimeMinute = $_POST['startTimeMinute'];
		$startTimeAMPM = $_POST['startTimeAMPM'];
		$endTimeHour = $_POST['endTimeHour'];
		$endTimeMinute = $_POST['endTimeMinute'];
		$endTimeAMPM = $_POST['endTimeAMPM'];
		$attendants = array();

		foreach($_POST as $key=>$val){
			if(strstr($key, 'attendant')){
				echo $key . ":" . $val;
				$attendants[] = $val;
			}
		}

		echo ("<h1>Attendants:</h1><p>" . implode($attendants, ", ") . "</p>");

		$event = new Google_Event();
		$event->setSummary($title);
		$event->setLocation($location);

		date_default_timezone_set("America/New_York");

		$startDateSplit = explode("/", $startDate);
		$startDateTime = new DateTime();
		$startDateTime->setDate(intval($startDateSplit[2]), intval($startDateSplit[0]), intval($startDateSplit[1]));		

		if($startTimeAMPM == "AM"){
			$startDateTime->setTime(intval($startTimeHour), intval($startTimeMinute));
		}
		else{
			$startDateTime->setTime(intval($startTimeHour)+12, intval($startTimeMinute));
		}

		echo "<h1>Start Time:</h1><p>" . $startDateTime->format(DateTime::RFC3339) . "</p>";
		echo "<h1>Start Time:</h1><p>" . $startDateTime->format('m/d/Y h:M:s') . "</p>";

		$start = new Google_EventDateTime();
		$start->setTimeZone('America/New_York');
		$start->setDateTime($startDateTime->format(DateTime::RFC3339));
		//$start->setDateTime('2014-03-09T10:00:00.000-07:00');

		$event->setStart($start);
		
		$endDateSplit = explode("/",$endDate);
		$endDateTime = new DateTime();
		$endDateTime->setDate(intval($endDateSplit[2]), intval($endDateSplit[0]), intval($endDateSplit[1]));
		
		if($endTimeAMPM == "AM"){
			$endDateTime->setTime(intval($endTimeHour), intval($endTimeMinute));
		}
		else{
			$endDateTime->setTime(intval($endTimeHour)+12, intval($endTimeMinute));
		}
		
		echo "<h1>End Time:</h1><p>" . $endDateTime->format(DateTime::RFC3339) . "</p>";
		echo "<h1>End Time:</h1><p>" . $endDateTime->format('m/d/Y h:m:s') . "</p>";

		$end = new Google_EventDateTime();
		$end->setTimeZone('America/New_York');
		$end->setDateTime($endDateTime->format(DateTime::RFC3339));
		//$end->setDateTime('2014-03-09T10:25:00.000-07:00');		

		$event->setEnd($end);

		//var_dump($_POST);

		$attendees = array();
		foreach($attendants as $attendant){
			$attendee = new Google_EventAttendee();
			$attendee->setEmail($attendant);
			$attendees[] = $attendee;
			echo "<h1>$attendant</h1>";
		}

		$event->attendees = $attendees;
		$finalEvent = $cal->events->insert('primary', $event);

		var_dump($finalEvent);

		echo ("<h1>Created event: " . $finalEvent['id'] . "</h1>");
	}

?>

<input type="button" value="Return to form" onclick="window.location = 'http://people.rit.edu/nmr9601/sandbox';"/>
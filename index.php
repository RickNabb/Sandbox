<!DOCTYPE html>
<html>
<head>

	<?php
		require_once('google-api-php-client/src/Google_Client.php');
		require_once('google-api-php-client/src/contrib/Google_CalendarService.php');

		if($_SERVER['REQUEST_METHOD'] != "POST"){
			session_start();			
			$client_id = '443906651386-s9o3bt0qid9vkcdjupuhg2e0l5iqket0.apps.googleusercontent.com';
			$client_secret = 'QAM6rn7wfPf5cBBoNz5K9iCa';
			$redirect_uri = 'http://localhost/Sandbox';

			$client = new Google_Client();
			$client->setClientId($client_id);
			$client->setClientSecret($client_secret);
			$client->setRedirectUri($redirect_uri);
			$client->setScopes("https://www.googleapis.com/auth/calendar");

			if(isset($_GET['logout'])){
				unset($_SESSION['access_token']);
			}

			if(isset($_GET['code'])){
				$client->authenticate($_GET['code']);
				$_SESSION['access_token'] = $client->getAccessToken();
				$redirect = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
				header('Location: ' . filter_var($redirect, FILTER_SANITIZE_URL));
			}

			if(isset($_SESSION['access_token']) && $_SESSION['access_token']){
				$client->setAccessToken($_SESSION['access_token']);
			}		
			else{
				$authUrl = $client->createAuthUrl();
				//print("<a class='login' href='$authURL'>Connect Me!</a>");
			}

			if($client->getAccessToken()){			
				//print "<h1>Calendar List</h1><pre>" . print_r($calList, true) . "</pre>";
				$_SESSION['access_token'] = $client->getAccessToken();
			}			
		}
		else{
			session_start();
			$client = new Google_Client();
			$client->setAccessToken($_SESSION['access_token']);
			$client->setApplicationName("Google Calendar PHP Starter Application");

			// Visit https://code.google.com/apis/console?api=calendar to generate your
			// client id, client secret, and to register your redirect uri.
			$client->setClientId('14798831816-798jk1fcv8qjfhbq9ci844fdsckj0euv.apps.googleusercontent.com');
			$client->setClientSecret('w9271HiaQnHLx99nRyU_QMqU');
			$client->setRedirectUri('http://localhost/Sandbox');

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
					$attendants[] = $val;
				}
			}

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

			$start = new Google_EventDateTime();
			$start->setTimeZone('America/New_York');
			$start->setDateTime($startDateTime->format(DateTime::RFC3339));

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

			$end = new Google_EventDateTime();
			$end->setTimeZone('America/New_York');
			$end->setDateTime($endDateTime->format(DateTime::RFC3339));

			$event->setEnd($end);

			$attendees = array();
			foreach($attendants as $attendant){
				$attendee = new Google_EventAttendee();
				$attendee->setEmail($attendant);
				$attendees[] = $attendee;
			}

			$event->attendees = $attendees;
			$finalEvent = $cal->events->insert('primary', $event);
			$success = true;
		}
	?>

	<title>Schedule an Appointment</title>

	<link href="./css/metro-bootstrap.css" rel="stylesheet" type="text/css" />
	<link href="./css/site.css" rel="stylesheet" type="text/css" />

<script>
	function addAttendant(){
		var div = document.getElementById("attendants");
		var count = 0;
		for(var i = 0; i < div.children.length; i++){
			var child = div.children[i];
			if(child.id.indexOf('attendant') > -1){
				count++;
			}
		}
		count++;
		var newInput = document.createElement("input");
		newInput.id = 'attendant' + count;
		newInput.name = 'attendant' + count;
		newInput.type = "text";
		div.appendChild(document.createElement("br"));
		div.appendChild(newInput);
	}
</script>

<style>

	body{
		padding: 20px;
	}

</style>

</head>

<body>

	<?php if (isset($authUrl)): ?>

	<h1>You must first log in before you can schedule an appointment!</h1>
	<a href='<?php echo $authUrl; ?>'>Connect Me!</a>

	<?php else: if($_SERVER['REQUEST_METHOD'] != 'POST'): ?>
	
	<!--<a class='logout' href='?logout'>Logout</a>	-->
	<h1 id="apt_title">Schedule an Appointment</h1>

	<form action="index.php" method="POST" name="scheduler">
		<div class="container-fluid">
			<div class="row">
				<div class="span3 offset1">
					<p>Title:</p>
					<input type="text" name="title"/>

					<p>Location:</p>
					<input type="text" name="location" />
				</div>
				<div class="span3 offset1">
					<p>Start Date (mm/dd/yyyy):</p>
					<input type="text" name="startDate" />

					<p>Start Time:</p>
					<select name="startTimeHour" style="width:70px;">
						<option>01</option>
						<option>02</option>
						<option>03</option>
						<option>04</option>
						<option>05</option>
						<option>06</option>
						<option>07</option>
						<option>08</option>
						<option>09</option>
						<option>10</option>
						<option>11</option>
						<option>12</option>
					</select>
					<select name="startTimeMinute" style="width:70px;">
						<option>00</option>
						<option>15</option>
						<option>30</option>
						<option>45</option>
					</select>
					<select name="startTimeAMPM" style="width:70px;">
						<option>AM</option>
						<option>PM</option>
					</select>

					<p>End Date (mm/dd/yyyy):</p>
					<input type="text" name="endDate" />

					<p>End Time:</p>
					<select name="endTimeHour" style="width:70px;">
						<option>01</option>
						<option>02</option>
						<option>03</option>
						<option>04</option>
						<option>05</option>
						<option>06</option>
						<option>07</option>
						<option>08</option>
						<option>09</option>
						<option>10</option>
						<option>11</option>
						<option>12</option>
					</select>
					<select name="endTimeMinute" style="width:70px;">
						<option>00</option>
						<option>15</option>
						<option>30</option>
						<option>45</option>
					</select>
					<select name="endTimeAMPM" style="width:70px;">
						<option>AM</option>
						<option>PM</option>
					</select>
			</div>
			<div class="span3 offset1">
				<p>Attendants (email address):</p>
				<div id="attendants">
					<input type="text" name="attendant1" id="attendant1"/>
				</div>
				<br />
				<input type="button" class="btn btn-info" onclick="addAttendant()" value="Add Attendant"/>
			</div>
		</div>
		<hr />
		<br />
		<div class="row">
			<div class="span1 offset11">
				<input id="btn_submit" type="submit" class="btn btn-primary" value="Submit" />
			</div>
		</div>
	</form>

	<?php endif; endif;
		if(isset($success) && $success):?>

	<?php echo ("<h1 id='success_label'>Created event: " . $finalEvent['id'] . "</h1>"); ?>
	
	<input type="button" class="btn btn-primary" value="Back to Sandbox" />		

	<?php endif; ?>
</body>

</html>
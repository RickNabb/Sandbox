<?php

/**
* Class to handle appointment transactions
* @author Nick Rabb - nrabb@outlook.com
*
*/

require_once __DIR__ . '/db.php';

// Logic to handle POST requests

if($_SERVER['REQUEST_METHOD'] == "POST"){

	session_start();
	require_once __DIR__ . '/../../bootstrap.php';

	if(isset($_POST['method'])){
		$method = $_POST['method'];
	} else{
		$method = '';
	}

	if($method == "addAppt" || $method == "updateAppt"){
		$title = $location = $start_time = $end_time = $attendants = $user_id = $order_id = '';

		$title = $_POST['title'];
		$location = $_POST['location'];
		$startDateDay = $_POST['startDateDay'];
		$startDateMonth = $_POST['startDateMonth'];
		$startDateYear = $_POST['startDateYear'];
		$endDateDay = $_POST['endDateDay'];
		$endDateMonth = $_POST['endDateMonth'];
		$endDateYear = $_POST['endDateYear'];
		$startTimeHour = $_POST['startTimeHour'];
		$startTimeMinute = $_POST['startTimeMinute'];
		$startTimeAMPM = $_POST['startTimeAMPM'];
		$endTimeHour = $_POST['endTimeHour'];
		$endTimeMinute = $_POST['endTimeMinute'];
		$endTimeAMPM = $_POST['endTimeAMPM'];
		$orderPrice = $_POST['order_price'];
		$orderDesc = $_POST['order_desc'];
		$attendants = array();

		foreach($_POST as $key=>$val){
			if(strstr($key, 'attendant')){
				$attendants[] = $val;
			}
		}

		$months = array(
			"January" => 1, "February" => 2, "March" => 3, "April"=>4, "May"=>5, "June"=>6,
				"July"=>7, "August"=>8, "September"=>9, "October"=>10, "November"=>11, "December"=>12);

		$startDateMonth = $months[$startDateMonth];

		$startDateTime = new DateTime();
		$startDateTime->setDate(intval($startDateYear), intval($startDateMonth), intval($startDateDay));		

		if($startTimeAMPM == "AM"){
			$startDateTime->setTime(intval($startTimeHour), intval($startTimeMinute));
		}
		else{
			$startDateTime->setTime(intval($startTimeHour)+12, intval($startTimeMinute));
		}

		$endDateMonth = $months[$endDateMonth];
		
		$endDateTime = new DateTime();
		$endDateTime->setDate(intval($endDateYear), intval($endDateMonth), intval($endDateDay));
		
		if($endTimeAMPM == "AM"){
			$endDateTime->setTime(intval($endTimeHour), intval($endTimeMinute));
		}
		else{
			$endDateTime->setTime(intval($endTimeHour)+12, intval($endTimeMinute));
		}

		if($method == "addAppt"){
			if($_SESSION['logged_in'] == true){
	
				try{
					$aptId = addAppt($_SESSION['user_id'], $title, $location, 
						$startDateTime->format('Y-m-d H:i:s'), $endDateTime->format('Y-m-d H:i:s'), $attendants);
					$_SESSION['appt_id'] = $aptId;
					$_SESSION['order_price'] = $orderPrice;
					$_SESSION['order_desc'] = $orderDesc;
					echo $aptId;
				}
				catch(Exception $ex){
					echo $ex;
				}
			}
			else if($_SESSION['guest_account'] == true){
	
				try{
					$aptId = addAppt('0000', $title, $location, 
						$startDateTime->format('Y-m-d H:i:s'), $endDateTime->format('Y-m-d H:i:s'), $attendants);
					$_SESSION['appt_id'] = $aptId;
					$_SESSION['order_price'] = $orderPrice;
					$_SESSION['order_desc'] = $orderDesc;
					echo $aptId;
				}
				catch(Exception $ex){
					echo $ex;
				}
			}
		}
		else if($method == "updateAppt"){
			try{
				$updated = updateAppt($_SESSION['appt_id'], $title, $location,
					$startDateTime->format('Y-m-d H:i:s'), $endDateTime->format('Y-m-d H:i:s'), $attendants);
				$_SESSION['order_price'] = $orderPrice;
				$_SESSION['order_desc'] = $orderDesc;
				echo $updated;
			}
			catch(Exception $ex){
				echo $ex;
			}
		}
	}
	else if($method == "addFeaturedAppt"){

		$title = $_POST['title'];
		$location = $_POST['location'];
		$startDateTime = date_create($_POST['startDateTime']);
		$endDateTime = date_create($_POST['endDateTime']);
		$orderPrice = $_POST['order_price'];
		$orderDesc = $_POST['order_desc'];
		$attendants = array();

		if($_SESSION['logged_in'] == true){
	
			try{
				$aptId = addAppt($_SESSION['user_id'], $title, $location, 
					$startDateTime->format('Y-m-d H:i:s'), $endDateTime->format('Y-m-d H:i:s'), $attendants);
				$_SESSION['appt_id'] = $aptId;
				$_SESSION['order_price'] = $orderPrice;
				$_SESSION['order_desc'] = $orderDesc;
				$_SESSION['featured'] = true;
				echo $aptId;
			}
			catch(Exception $ex){
				echo $ex;
			}
		}
		else if($_SESSION['guest_account'] == true){

			try{
				$aptId = addAppt('0000', $title, $location, 
					$startDateTime->format('Y-m-d H:i:s'), $endDateTime->format('Y-m-d H:i:s'), $attendants);
				$_SESSION['appt_id'] = $aptId;
				$_SESSION['order_price'] = $orderPrice;
				$_SESSION['order_desc'] = $orderDesc;
				$_SESSION['featured'] = true;
				echo $aptId;
			}
			catch(Exception $ex){
				echo $ex;
			}
		}
	}
	else if($method == "removeAppt"){	
		if($_SESSION['process_step'] == 'schedule_apt' && !isset($_SESSION['appt_id'])){
			echo '1';
		}
		else{
			try{
				$removed = removeAppt($_SESSION['appt_id']);
				if($removed){
					unset($_SESSION['appt_id']);
					unset($_SESSION['logged_in']);
					unset($_SESSION['process_step']);
					unset($_SESSION['user_id']);
					unset($_SESSION['order_price']);
					unset($_SESSION['order_desc']);
				}
				echo $removed;
			}
			catch(Exception $ex){
				echo $ex;
			}
		}
	}
}
else if($_SERVER['REQUEST_METHOD'] == "GET"){

	require_once __DIR__ . '/../../bootstrap.php';

	if(isset($_GET['method'])){
		$method = $_GET['method'];
	}
	else{
		$method = '';
	}

	if($method == "getAppt"){
		echo 'getAppt';
	}
	else if($method == "getAppts"){
		echo 'getAppts';
	}
}


function addAppt($userId, $title, $location, $startTime, $endTime, $attendants){
	
	$conn = getConnection();
	
	$query = sprintf("INSERT INTO %s(user_id, title, location, start_time, end_time) VALUES
		('%s', '%s', '%s', '%s', '%s')",
		APTS_TABLE,
		mysql_real_escape_string($userId),
		mysql_real_escape_string($title),
		mysql_real_escape_string($location),
		mysql_real_escape_string($startTime),
		mysql_real_escape_string($endTime));

	$result = mysql_query($query, $conn);

	if(!$result){
		$errMsg = "Error creating new appointment: " . mysql_error($conn);
		mysql_close($conn);
		throw new Exception($errMsg);
	}	
	
	$aptId = mysql_insert_id($conn);

	//adding attendants
	foreach($attendants as $attendant){
		$query = sprintf("INSERT INTO %s(apt_id, attendant)
			VALUES('%s', '%s')",
			ATTENDANTS_TABLE,
			mysql_real_escape_string($aptId),
			mysql_real_escape_string($attendant));
		$result = mysql_query($query, $conn);
		if(!$result){
			$errMsg = "Error adding attendant for appointment $aptId: " . mysql_error($conn);
			mysql_close($conn);
			throw new Exception($errMsg);
		}
	}

	mysql_close($conn);

	return $aptId;
}

function updateAppt($apptId, $title, $location, $start_time, $end_time, $attendants){
	$conn = getConnection();
	$query = sprintf("UPDATE %s SET title='%s', location='%s', start_time='%s',
		end_time='%s' WHERE appt_id = '%s'",
		APTS_TABLE,
		mysql_real_escape_string($title),
		mysql_real_escape_string($location),
		mysql_real_escape_string($start_time),
		mysql_real_escape_string($end_time),
		mysql_real_escape_string($apptId));

	$result = mysql_query($query, $conn);
	if(!$result){
		$errMsg = "Error updating appointment $apptId: " . mysql_error($conn);
		mysql_close($conn);
		throw new Exception($errMsg);
	}

	foreach($attendants as $attendant){
		$query = sprintf("UPDATE %s SET attendant = '%s' WHERE apt_id = '%s'",
			ATTENDANTS_TABLE,
			mysql_real_escape_string($attendant),
			mysql_real_escape_string($apptId));

		$result = mysql_query($query, $conn);
		if(!$result){
			$errMsg = "Error updating attendants for appointment $apptId: " . mysql_error($conn);
			mysql_close($conn);
			throw new Exception($errMsg);
		}
	}

	$isUpdated = mysql_affected_rows($conn);
	return $isUpdated;
}

function removeAppt($apptId){
	$conn = getConnection();
	$query = sprintf("DELETE FROM %s WHERE appt_id = '%s'",
		APTS_TABLE,
		mysql_real_escape_string($apptId));

	$result = mysql_query($query, $conn);
	if(!$result){
		$errMsg = "Error deleting appointment $apptId: " . mysql_error($conn);
		mysql_close($conn);
		throw new Exception($errMsg);
	}

	return $result;
}

function getAppts($user_id){
	$conn = getConnection();
	$query = sprintf("SELECT * FROM %s WHERE user_id = '%s'",
		APTS_TABLE,
		mysql_real_escape_string($user_id));

	$result = mysql_query($query, $conn);
	if(!$result){
		$errMsg = "Error retrieving appointments for $user_id: " . mysql_error($conn);
		mysql_close($conn);
		throw new Exception($errMsg);
	}

	$rows = array();
	while(($row = mysql_fetch_assoc($result))){
		$rows[] = $row;
	}

	mysql_close($conn);
	return $rows;
}

function getAppt($appt_id){
	$conn = getConnection();
	$query = sprintf("SELECT * FROM %s WHERE appt_id = '%s'",
		APTS_TABLE,
		mysql_real_escape_string($appt_id));

	$result = mysql_query($query, $conn);
	if(!$result){
		$errMsg = "Error retrieving appointment $apptId: " . mysql_error($conn);
		mysql_close($conn);
		throw new Exception($errMsg);
	}

	$row = mysql_fetch_assoc($result);
	mysql_close($conn);

	return $row;
}

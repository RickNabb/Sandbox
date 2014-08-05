<!DOCTYPE html>
<html>
<head>

	<?php

		require_once 'bootstrap.php';
		session_start();

		if(isset($_SESSION['logged_in']) || isset($_SESSION['guest_account'])){
			$_SESSION['process_step'] = 'schedule_apt';
			setcookie("process_step", "schedule_apt");

			$next = true;
			$back = false;
			$finish = false;
			$cancel = true;
		}
		else{
			header("Location: " . __DIR__ . "/index.php");
		}
		
	?>

	<title>Schedule an Appointment</title>

<script>

	window.onload = function(){
		updateHeader("schedule");
		if(location.search.indexOf("method=update") != -1){
			if(localStorage){
				fillEventInfo();
			}
			else{
				$("#local_storage_alert").show();
			}
		}
		showHideEventCreate();
	}	

</script>

</head>

<body>
	<?php require_once('header.php'); ?>
	<?php require_once('cancelModal.php') ?>

	<div id="loaderDiv">
		<img src="./img/loader.gif" alt="Loading..."/>
		<h4>Please wait...</h4>
	</div>

	<div class="container-fluid">
		<div class="row" id="local_storage_alert">
			<div class="offset1">
				<div class="alert alert-warning alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss"alert">
						<span aria-hidden="true">&times;</span></button>
					<p><strong>Alert:</strong> We could not auto-fill your previously filled in fields because your
					browser does not support that feature.</p>
					<p>Please download a newer browser to access the full features of this application.</p>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="offset1 span6">
				<h3 id="eventSignupTitle">Sign up for an Event</h3>					
			</div>
			<div class="offset1 span6">
				<h3 id="createEventTitle" onclick="showHideEventCreate();">(Create Your Own Event?)</h3>
			</div>
		</div>

		<div class="row">
			<div class="offset1">
				<hr />
			</div>
		</div>		
		
		<div id="eventSignup">
			<div class="row offset1">
				<?php

					$conn = getConnection();
					$query = sprintf("SELECT * FROM %s", EVENTS_TABLE);

					$result = mysql_query($query, $conn);
					$event_descriptions = [];
					$event_prices = [];
					$event_titles = [];
					$event_durations = [];

					if($result){
						while($row = mysql_fetch_assoc($result)){
							if($row['isFeatured'] == '1'){
								echo '<div class="span2 eventItem eventHover" featured="true" id="event_' .
									 $row['eventId'] . '" onclick="changeEvent(this, ' . $row['eventId'] 
									 	. ');" startDateTime="' . $row['startDateTime'] . 
										'" endDateTime="' . $row['endDateTime'] . '">';
								echo '<img src="' . $row['imageURL'] . '" alt="' . $row['title'] . '" />';
								echo '</div>';

								$event_descriptions[$row['eventId']] = $row['description'];
								$event_prices[$row['eventId']] = $row['price'];
								$event_titles[$row['eventId']] = $row['title'];
								$event_durations[$row['eventId']] = $row['duration'];
							}
						}
					}
					else{
						echo '<div class="span1">';
						echo "<h4>Sorry! We couldn't load our events, please try again.</h4>";
						echo '</div>';
					}

				?>
			</div>

			<?php

				foreach($event_prices as $eventId => $price){

					echo '<div class="row title" id="' . $eventId . '_price" style="display: none;">';

					$desc = $event_descriptions[$eventId];
					echo '<div class="span8 offset2">';
					echo '<p style="font-size: 30px;">' . $event_titles[$eventId] . ' - &nbsp;<span class="eventPrice">$' .  $price . '</span></p>';
					echo '</div>';

					echo '</div>';

					echo '<div class="row" id="' . $eventId . '_desc" style="display: none;">';
					echo '<div class="span10 offset2">';
					echo '<p class="eventDesc">Duration: ' . $event_durations[$eventId] . ' hour(s)</p><br />';
					echo '<p class="eventDesc">' . $desc . '</p>';				
					echo '<input type="hidden" id="' . $eventId . '_duration_val" duration="' . $event_durations[$eventId] . '" />';
					echo '<input type="hidden" id="' . $eventId . '_title_val" title="' . $event_titles[$eventId] . '" />';
					echo '<input type="hidden" id="' . $eventId . '_price_val" price="' . $event_prices[$eventId] . '" />';
					echo '<input type="hidden" id="' . $eventId . '_desc_val" desc="' . $desc . '" />';
					echo '</div></div>';
				}

			?>
		</div>		

		<div id="createEvent">
			<div class="row">
				<div class="offset1">
					<h3>Select an Event Type</h3>
					<br />
				</div>
			</div>

			<div class="row offset1">
				<?php

					$conn = getConnection();
					$query = sprintf("SELECT * FROM %s", EVENTS_TABLE);

					$result = mysql_query($query, $conn);

					if($result){
						while($row = mysql_fetch_assoc($result)){
							if($row['isFeatured'] == '0'){
								echo '<div class="span2 eventItem eventHover" id="event_' . $row['eventId'] . '" onclick="changeEvent(this, ' . $row['eventId'] . ');">';
								echo '<img src="' . $row['imageURL'] . '" alt="' . $row['title'] . '" />';
								echo '</div>';

								$event_descriptions[$row['eventId']] = $row['description'];
								$event_prices[$row['eventId']] = $row['price'];
								$event_titles[$row['eventId']] = $row['title'];
								$event_durations[$row['eventId']] = $row['duration'];
							}
						}
					}
					else{
						echo '<div class="span1">';
						echo "<h4>Sorry! We couldn't load our events, please try again.</h4>";
						echo '</div>';
					}

				?>
			</div>

			<?php

				foreach($event_prices as $eventId => $price){

					echo '<div class="row title" id="' . $eventId . '_price" style="display: none;">';

					$desc = $event_descriptions[$eventId];
					echo '<div class="span8 offset2">';
					echo '<p style="font-size: 30px;">' . $event_titles[$eventId] . ' - &nbsp;<span class="eventPrice">$' .  $price . '</span></p>';
					echo '</div>';

					echo '</div>';

					echo '<div class="row" id="' . $eventId . '_desc" style="display: none;">';
					echo '<div class="span10 offset2">';
					echo '<p class="eventDesc">Duration: ' . $event_durations[$eventId] . ' hour(s)</p><br />';
					echo '<p class="eventDesc">' . $desc . '</p>';				
					echo '<input type="hidden" id="' . $eventId . '_duration_val" duration="' . $event_durations[$eventId] . '" />';
					echo '<input type="hidden" id="' . $eventId . '_title_val" title="' . $event_titles[$eventId] . '" />';
					echo '<input type="hidden" id="' . $eventId . '_price_val" price="' . $event_prices[$eventId] . '" />';
					echo '<input type="hidden" id="' . $eventId . '_desc_val" desc="' . $desc . '" />';
					echo '</div></div>';
				}

			?>

			<div class="row title">
				<div class="offset1">
					<h3>Event Time</h3>
					<hr />
				</div>
			</div>

			<div class="row">
				<div class="span1 offset1">
					<label for="startDateMonth">Start Date:</label>
				</div>
				<div class="span6 offset1">
					<select name="startDateMonth" id="startDateMonth" onchange="changeDays(this); changeEndDayTime(this);">
						<?php

							$months = ['January', 'February', 'March', 'April', 'May', 'June',
								'July', 'August', 'September', 'October', 'November', 'December'];
							$curMonth = '';

							foreach($months as $month){

								if($month == date('F')){
									echo '<option selected>' . $month . '</option>';
									$curMonth = $month;
								}
								else{
									echo '<option>' . $month . '</option>';
								}
							}
						?>					
					</select>

					<select name="startDateDay" id="startDateDay" onchange="changeEndDayTime(this);">
						<?php

							$thirtyDays = array(
								'September' => True,
								'April' => True,
								'June' => True,
								'November' => True);
							$curDay = '';

							switch($curMonth){
								case(isset($thirtyDays[$curMonth])):
									for($i = 1; $i <= 30; $i++){
										if($i == date('j')){
											echo '<option selected>' . $i . '</option>';
											$curDay = $i;
										}
										else{
											echo '<option>' . $i . '</option>';
										}
									}
									break;
								case($curMonth == "February"):
									for($i = 1; $i <= 28; $i++){
										if($i == date('j')){
											echo '<option selected>' . $i . '</option>';
											$curDay = $i;
										}
										else{
											echo '<option>' . $i . '</option>';
										}
									}
									break;
								default:
									for($i = 1; $i <= 31; $i++){
										if($i == date('j')){
											echo '<option selected>' . $i . '</option>';
											$curDay = $i;
										}
										else{
											echo '<option>' . $i . '</option>';
										}
									}
									break;
							}
						?>
					</select>

					<select name="startDateYear" id="startDateYear" onchange="changeEndDayTime(this);">
						<option>2014</option>
						<option>2015</option>
						<option>2016</option>
						<option>2017</option>
						<option>2018</option>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="span1 offset1">
					<label for="startTimeHour">Start Time:</label>
				</div>
				<div class="span6 offset1">
					<select name="startTimeHour" id="startTimeHour" style="width:70px;" onchange="changeEndDayTime(this);">
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
					<select name="startTimeMinute" style="width:70px;" id="startTimeMinute" onchange="changeEndDayTime(this);">
						<option>00</option>
						<option>15</option>
						<option>30</option>
						<option>45</option>
					</select>
					<select name="startTimeAMPM" style="width:70px;" id="startTimeAMPM" onchange="changeEndDayTime(this);">
						<option>AM</option>
						<option>PM</option>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="span1 offset1">
					<label for="endDateMonth">End Date:</label>
				</div>
				<div class="span6 offset1">
					<select name="endDateMonth" id="endDateMonth" disabled>
						<option><?php echo $curMonth; ?></option>
					</select>

					<select name="endDateDay" id="endDateDay" disabled>
						<option><?php echo $curDay; ?></option>
					</select>

					<select name="endDateYear" id="endDateYear" disabled>
						<option>2014</option>
					</select>
				</div>
			</div>
			<div class="row">
				<div class="span1 offset1">
					<label for="endTimeHour">End Time:</label>
				</div>
				<div class="span6 offset1">
					<select name="endTimeHour" style="width:70px;" id="endTimeHour" disabled>
						<option>01</option>					
					</select>
					<select name="endTimeMinute" style="width:70px;" id="endTimeMinute" disabled>
						<option>00</option>
					</select>
					<select name="endTimeAMPM" style="width:70px;" id="endTimeAMPM" disabled>
						<option>AM</option>
					</select>
				</div>
			</div>

			<div class="row title">
				<div class="offset1">
					<h3>Attendees</h3>
					<hr />
				</div>
			</div>

			<div class="row">
				<div class="span4 offset1">
					<label for="attendant1">Attendants (email address):</label>
				</div>
			</div>
			<div class="row">
				<div class="span4 offset1">
					<div id="attendants">
						<input type="text" name="attendant1" id="attendant1" placeholder="Attendant email..."/>
					</div>
				</div>
				<div class="span3 offset1">	
					<button class="btn btn-success" onclick="addAttendant()" id="btn_addAttendant">
						<i class="icon-white icon-plus-sign"></i>&nbsp;Add Attendant</button>
				</div>
			</div>
		</div>
	</div>

	<?php require_once('footer.php'); ?>
</body>

</html>
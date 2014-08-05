<?php

	if($_SERVER['REQUEST_METHOD'] == 'POST'){

		if(isset($_POST['fileAddress']) && isset($_POST['message'])){
			logError($_POST['fileAddress'], $_POST['message']);
			echo 'success';
		}
		else{
			echo 'failure';
		}
	}

	function logError($fileAddress, $message){

		$file = $fileAddress;
		$fs = fopen($file, 'w');

		$timestamp = new Date();
		fwrite($fs, "\n\n" . date_format($timestamp, 'm-d-Y H:i:s') . ": " . $message);
		fclose($fs);
	}
?>
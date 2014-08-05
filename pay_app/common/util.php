<?php

function getBaseUrl() {

	$protocol = 'http';
	if ($_SERVER['SERVER_PORT'] == 443 || (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) == 'on')) {
		$protocol .= 's';
		$protocol_port = $_SERVER['SERVER_PORT'];
	} else {
		$protocol_port = 80;
	}

	$host = $_SERVER['HTTP_HOST'];
	$port = $_SERVER['SERVER_PORT'];
	$request = $_SERVER['PHP_SELF'];
	return dirname($protocol . '://' . $host . ($port == $protocol_port ? '' : ':' . $port) . $request);
}

function getLink(array $links, $type) {
	foreach($links as $link) {
		if($link->getRel() == $type) {
			return $link->getHref();
		}
	}
	return "";
}

function parseApiError($errorJson) {
	$msg = '';
	
	$data = json_decode($errorJson, true);
	if(isset($data['name']) && isset($data['message'])) {
		$msg .= $data['name'] . " : " .  $data['message'] . "<br/>";
	}
	if(isset($data['details'])) {
		$msg .= "<ul>";
		foreach($data['details'] as $detail) {
			$msg .= "<li>" . $detail['field'] . " : " . $detail['issue'] . "</li>";	
		}
		$msg .= "</ul>";
	}
	if($msg == '') {
		$msg = $errorJson;
	}	
	return $msg;
}

function encrypt($pass){
	$newPass = '';
	for($i = 0; $i < strlen($pass); $i++){
		$newPass .= chr((ord($pass[$i])*13) % 48 + 48);
	}

	return $newPass;
}
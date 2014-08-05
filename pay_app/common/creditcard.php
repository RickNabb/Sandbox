<?php

/**
* Class to handle credit card transactions
* @author Nick Rabb - nrabb@outlook.com
*
*/

require_once __DIR__ . '/db.php';

function addCreditCard($holderName, $number, $expMonth, $expYear, $cvv, $userId){

	$conn = getConnection();

	$query = sprintf("INSERT INTO %s (holderName, {'number'}, expMonth, expYear, cvv, userId)
		VALUES ('%s', '%s', '%s', '%s', '%s', '%s')",
		CREDIT_CARD_TABLE,
		mysql_real_escape_string($holderName),
		mysql_real_escape_string($number),
		mysql_real_escape_string($expMonth),
		mysql_real_escape_string($expYear),
		mysql_real_escape_string($cvv),
		mysql_real_escape_string($userId));

	$result = mysql_query($query, $conn);
	if(!$result){
		$errMsg = "Error saving credit card: " . mysql_error($conn);
		mysql_close($conn);
		throw new Exception($errMsg);
	}

	$cardId = mysql_insert_id($conn);

	mysql_close();

	return $cardId;
}

function updateCreditCard($cardId, $holderName, $number, $expMonth, $expYear, $cvv, $userId){

	$conn = getConnection();

	$query = sprintf("UPDATE %s SET (holderName, {'number'}, expMonth, expYear, cvv, userId)=
		('%s', '%s', '%s', '%s', '%s', '%s')",
			CREDIT_CARD_TABLE,
			mysql_real_escape_string($holderName),
			mysql_real_escape_string($number),
			mysql_real_escape_string($expMonth),
			mysql_real_escape_string($expYear),
			mysql_real_escape_string($cvv),
			mysql_real_escape_string($userId));

	$result = mysql_query($query, $conn);

	if(!$result){
		$errMsg = "Error updating credit card: " . mysql_error($conn);
		mysql_close($conn);
		throw new Exception($errMsg);
	}

	$isUpdated = mysql_affected_rows($conn);
	return $isUpdated;
}

function removeCreditCard($cardId){

	$conn = getConnection();
	$query = sprintf("DELETE FROM %s WHERE creditcardId = '%s'",
		CREDIT_CARD_TABLE,
		mysql_real_escape_string($cardId));

	$result = mysql_query($query, $conn);
	if(!$result){
		$errMsg = "Error deleting credit card $cardId: " . mysql_error($conn);
		mysql_close($conn);
		throw new Exception($errMsg);
	}

	return $result;
}

function getCreditCard($cardId){

	$conn = getConnection();
	$query = sprintf("SELECT * FROM %s WHERE creditcardId = '%s'",
		CREDIT_CARD_TABLE,
		mysql_real_escape_string($cardId));

	$result = mysql_query($query, $conn);

	if(!$result){
		$errMsg = "Error retrieving credit card $cardId: " . mysql_error($conn);
		mysql_close($conn);
		throw new Exception($errMsg);
	}

	$row = mysql_fetch_assoc($result);

	return $row;
}

function getCreditCards($userId){

	$conn = getConnection();
	$query = sprintf("SELECT * FROM %s WHERE userId = '%s'",
		CREDIT_CARD_TABLE,
		mysql_real_escape_string($userId));

	$result = mysql_query($query, $conn);

	if(!$result){
		$errMsg = "Error retrieving credit cards for user $userId: " . mysql_error($conn);
		mysql_close($conn);
		throw new Exception($errMsg);
	}

	$rows = array();
	while($row = mysql_fetch_assoc($result)){
		$rows[] = $row;
	}

	return $rows;
}


?>
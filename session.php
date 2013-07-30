<?php
	// Connect to mysql
	include 'sqlconnect.php'; 
?>
<?php

	####################################################################
	# Query database to see if session is still valid
	# done via Ajax/json response
	####################################################################
	
	# Declare POST variables
	$userID = $_POST['userName'];
	$sessID = $_POST['loginSess'];
	
	// Variables to track outcome
	$status; // e.g. 0 for success, -1 for unable to update cookie
	$message; // error messages to display to user
	
	// Use mysql real escape to make input safe for SQL
	$uidSafe = $mysqli->real_escape_string($userID);
	$sidSafe = $mysqli->real_escape_string($sessID);

	// Check expiration of session cookie on server
	if (!($query = $mysqli->prepare("select case when TIME_TO_SEC((select expires 
			from session_md where user = '$uidSafe' and sessionID = '$sidSafe')) 
			> TIME_TO_SEC(CURRENT_TIMESTAMP) then 0 else -1 end"))) {
		$message =  "Unable to prepare database"; 
		$status = -1;
	}
	if(!$query->execute()) {
		$message = "Unable to execute query"; 
		$status = -1;
	}
	if(!($query->bind_result($status))) {
		$message = "Unable to retrieve data"; 
		$status = -1;
	}
	
	$query->fetch();
	
	if ($status != 0) {
		$message = "Session has expired, please log in";
		$status = -1;
	}
	else {
		$message = null;
		$status = 0;
	}
	
	
	// Output to JSON
	
	$loginObj = new stdClass;
	$loginObj->status = htmlspecialchars($status);
	$loginObj->user = htmlspecialchars($userID);
	$loginObj->sessID = htmlspecialchars($sessID);
	$loginObj->expiration = NULL;
	$loginObj->message = htmlspecialchars($message);
	
	// Encode in JSON
	// header('Content-Type: application/json');
	echo json_encode($loginObj);
	
	exit;
  
?>


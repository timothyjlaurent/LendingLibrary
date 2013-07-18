<?php
	// Connect to mysql
	include 'sqlconnect.php'; 
?>


<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN">-->

<?php

	####################################################################
	# Query database and display results as a pseudo XML file so
	# it can used with AJAX; based on various examples from W3 Schools
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
		$message = "Session has expired, please log in ***";
		$status = -1;
	}
	else {
		$message = null;
		$status = 0;
	}
	
	
	// Output to pseudo HTML
	echo "<xml>" . "\n";
		echo "    <sessioninfo>" . "\n";
		echo "        <status>" . htmlspecialchars($status) . "</status>" . "\n"; 
		echo "        <message>" . htmlspecialchars($message) . "</message>" . "\n";
		echo "    </sessioninfo>" . "\n";
	echo "</xml>";

	// $query->close();        
?>


<?php
	// Connect to mysql
	include 'sqlconnect.php'; 
?>


<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN">-->

<?php

	####################################################################
	# Refresh the session expiration date on the server
	####################################################################
	
	# Declare POST variables
	$userID = $_POST['userName'];
	$sessID = $_POST['loginSess'];
	unset($_POST);
	
	// Variables to track outcome
	$status = 0; // e.g. 0 for success, -1 for unable to update cookie
	$message = null; // error messages to display to user
	
	// Use mysql real escape to make input safe for SQL
	$uidSafe = $mysqli->real_escape_string($userID);
	$sidSafe = $mysqli->real_escape_string($sessID);
	
	
	####################################################################
	# Update session expiration on server
	####################################################################
	

	
	// Add record to sesson table: user, sessionID and expiration date (10 minutes)
	// MD5 from: http://www.thingy-ma-jig.co.uk/blog/10-07-2008/generate-random-string-mysql
	if (!($add = $mysqli->query("UPDATE session SET expires = 
			(SELECT DATE_ADD(NOW(), INTERVAL 10 MINUTE)) WHERE userid = 
			'$uidSafe' and sessionid = '$sidSafe'"))) {
		$message =  "Unable to refresh session 2"; 
		$status = -1;
	}
	
	// Output to pseudo HTML to confirm (UNNECCESSARY???)
	echo "<xml>" . "\n";
		echo "    <sessioninfo>" . "\n";
		echo "        <status>" . $status . "</status>" . "\n"; 
		echo "        <message>" . $message . "</message>" . "\n";
		echo "    </sessioninfo>" . "\n";
	echo "</xml>";

	// $query->close();        
?>


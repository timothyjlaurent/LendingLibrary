<?php
	// Connect to mysql
	include 'sqlconnect.php'; 
?>
<?php
	# Declare POST variables
	$uid = $_POST['uid'];
	unset($_POST);
	
	// echo "uid: " . $uid;

	// Use mysql real escape to make input safe for SQL
	$uidSafe = $mysqli->real_escape_string($uid);
	
	// Validate data before sending to sql
	
	$message = null; // field to track error messages
	
	####################################################################
	# Make sure user exists on our DB
	####################################################################

	if (!($query = $mysqli->prepare("SELECT COUNT(user) FROM userdb WHERE user = '$uidSafe'"))) {
		outputJSON(-1, null, null, null, "Unable to prepare database");
	}

	if(!$query->execute()) {
		outputJSON(-1, null, null, null, "Unable to execute query");
	}

	if(!($query->bind_result($userCount))) {
		outputJSON(-1, null, null, null, "Unable to bind results");
	}

	$query->fetch();
	if($userCount === 0) {
		outputJSON(-1, null, null, null, "Please register first");
	}
	$query->close();
	
	####################################################################
	# Add to session table once user validates
	####################################################################
	
	// First delete any prior sessions for the user
	if(!($delete = $mysqli->query("DELETE from session_md WHERE user = '$uidSafe'"))) {
		outputJSON(-1, null, null, null, "Unable to delete prior session info from database");
	}
	
	// Add record to sesson table: user, sessionID and expiration date (10 minutes)
	// MD5 from: http://www.thingy-ma-jig.co.uk/blog/10-07-2008/generate-random-string-mysql
	if (!($add = $mysqli->query("INSERT INTO session_md VALUES ('$uidSafe', 
			(SELECT SUBSTRING(MD5(RAND()) FROM 1 FOR 20)), 
			(SELECT DATE_ADD(NOW(), INTERVAL 10 MINUTE)))"))) {
		outputJSON(-1, null, null, null, "Unable to create session");
	}
	
	// ####################################################################
	// # Retrieve session id to store in cookie
	// ####################################################################
	
	if (!($session = $mysqli->prepare("SELECT sessionID, expires FROM session_md WHERE user = '$uidSafe'"))) {
		outputJSON(-1, null, null, null, "Unable to prepare database");
	}

	if(!$session->execute()) {
		outputJSON(-1, null, null, null, "Unable to execute query");
	}

	if(!($session->bind_result($sessionID, $expires))) {
		outputJSON(-1, null, null, null, "Unable to bind query");
	}

	$session->fetch();
	if($userCount === 0) {
		outputJSON("-1", null, null, null, "Unable to retrieve session");
	}
	$session->close();
	outputJSON(0, $uidSafe, $sessionID, $expires, "You are now logged in");

	// ####################################################################
	// # Function to  output JSON on login status
	// ####################################################################
	
	function outputJSON($status, $userid, $sessionID, $expires, $message) {
		
		// Instantiate object to store login information
		$loginObj = new stdClass;
		$loginObj->status = htmlspecialchars($status);
		$loginObj->user = htmlspecialchars($userid);
		$loginObj->sessID = htmlspecialchars($sessionID);
		$loginObj->expiration = htmlspecialchars($expires);
		$loginObj->message = htmlspecialchars($message);

		// Encode in JSON
		// header('Content-Type: application/json');
		echo json_encode($loginObj);
		
		exit;
	}
?>

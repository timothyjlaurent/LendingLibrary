<?php
	// Connect to mysql
	include 'sqlconnect.php'; 
?>
<?php
	# Declare POST variables (GET FOR TESTING)
	$uid = $mysqli->real_escape_string($_POST['uname']);
	$fname = $mysqli->real_escape_string($_POST['fname']);
	$lname = $mysqli->real_escape_string($_POST['lname']);
	unset($_POST);
	
	// echo "uid: " . $uid . " first: " . $fname . " last: " . $lname . "<br>";

	####################################################################
	# Validate data before sending to sql
	####################################################################
	
	// Make sure first name only contains letters, spaces and hyphens, 2-25 char
	if (!preg_match('/^[a-zA-Z- ]{2,25}$/i', $fname)) {
		outputJSON(-1, null, null, null, "First name is invalid");
	}
	
	// Make sure last name also valid (but 2-35 char)
	if (!preg_match('/^[a-zA-Z- ]{2,35}$/i', $lname)) {
		outputJSON(-1, null, null, null, "Last name is invalid");
	}	

	// Make sure user name is valid 
	// Doing minimal checks since don't know what ONID standard is
	// Website says 7-30, can include punctation, but is vague
	// Setting looser requirements to prevent errors
	if (!preg_match('/^[0-9a-zA-Z- `~@#$%*()_=+?]{2,50}$/i', $uid)) {
		outputJSON(-1, null, null, null, "User name is invalid");
	}	
	####################################################################
	# Make sure user id does not exist on our DB
	####################################################################

	if (!($query = $mysqli->prepare("SELECT COUNT(userid) FROM userdb WHERE userid = '$uid'"))) {
		outputJSON(-1, null, null, null, "Unable to prepare database");
	}

	if(!$query->execute()) {
		outputJSON(-1, null, null, null, "Unable to execute query");
	}

	if(!($query->bind_result($userCount))) {
		outputJSON(-1, null, null, null, "Unable to bind results");
	}

	$query->fetch();
	if($userCount != 0) {
		outputJSON(-1, null, null, null, "User id already registered");
	}
	$query->close();
	
	####################################################################
	# Add to user table
	####################################################################
	
	if (!($adduser = $mysqli->prepare("insert into userdb (userid, fname, lname) 
			values ('$uid', '$fname', '$lname')"))) {
		outputJSON(-1, null, null, null, "Unable to prepare database");
	}
	
	if(!$adduser->execute()) {
		outputJSON(-1, null, null, null, "Unable to execute query");
	}
	
	// Check if insert was successful 
	$adduser->store_result();
	if ($adduser->affected_rows == 0) {
		outputJSON(-1, null, null, null, "Unable to register user");
	}
	
	$adduser->close();
	
	####################################################################
	# Log user in by adding to session table
	####################################################################
	// Note all errors are logged as 0 since user was registered; simply
	// tell user to log in
	
	// Add record to sesson table: user, sessionID and expiration date (10 minutes)
	// MD5 from: http://www.thingy-ma-jig.co.uk/blog/10-07-2008/generate-random-string-mysql
	if (!($addsession = $mysqli->query("INSERT INTO session VALUES ('$uid', 
			(SELECT SUBSTRING(MD5(RAND()) FROM 1 FOR 20)), 
			(SELECT DATE_ADD(NOW(), INTERVAL 10 MINUTE)))"))) {
		outputJSON(0, $uid, null, null, "1You are registered, please log in");
	}


	// Retrieve session data needed for cookie
	if (!($session = $mysqli->prepare("SELECT sessionID, expires FROM session WHERE userid = '$uid'"))) {
		outputJSON(0, $uid, null, null, "2You are registered, please log in");
	}

	if(!$session->execute()) {
		outputJSON(0, $uid, null, null, "3You are registered, please log in");
	}

	// Check if any results
	$session->store_result();
	if ($session->affected_rows == 0) {
		outputJSON(0, $uid, null, null, "4You are registered, please log in");
	}
	
	if(!($session->bind_result($sessionID, $expires))) {
		outputJSON(0, $uid, null, null, "5You are registered, please log in");
	}

	$session->fetch();
	
	$session->close();


	// Output results to JSON
	outputJSON(0, $uid, $sessionID, $expires, "Congratulations, you are registered");
	
	

	####################################################################
	# Function to  output JSON on login status
	####################################################################
	
	function outputJSON($status, $userid, $sessionID, $expires, $message) {
		
		// Instantiate object to store login information
		$loginObj = new stdClass;
		$loginObj->status = htmlspecialchars($status);
		$loginObj->user = htmlspecialchars($userid);
		$loginObj->sessID = htmlspecialchars($sessionID);
		$loginObj->expiration = htmlspecialchars($expires);
		$loginObj->message = htmlspecialchars($message);

		// Encode in JSON
		header('Content-Type: application/json');
		echo json_encode($loginObj);
		
		exit;
	}
?>

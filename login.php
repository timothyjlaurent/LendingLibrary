<?php
	// Connect to mysql
	include 'sqlconnect.php'; 
?>

<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN">-->
<?php
	# Declare POST variables
	$uid = $_POST['uid'];
	$password = $_POST['pw'];
	unset($_POST);
	
	// echo "uid: " . $uid;
	// echo "password: " . $password;
	// echo $referURL;

	// Use mysql real escape to make input safe for SQL
	$uidSafe = $mysqli->real_escape_string($uid);
	$pwSafe = $mysqli->real_escape_string($password);
	
	// Validate data before sending to sql
	
	$message = null; // field to track error messages
	
	if(empty($uidSafe) || empty($pwSafe)) {
		setLoginCookie(-1, null, null, null, "User name / password required");
	}

	// Make sure name/pw contains valid characters, min/max length
	// From: http://forums.phpfreaks.com/topic/149318-solved-only-letters-and-numbers-min-8-max-12-characters/
	if(!preg_match('/^[a-zA-Z0-9]{5,20}$/i', $uidSafe) 
		|| !preg_match('/^[a-zA-Z0-9]{5,20}$/i', $pwSafe)) {
		setLoginCookie(-1, null, null, null, "User name / password required can contain 5-20 letters/numbers");
	}
	
	// ####################################################################
	// # Query database using MD5 encryption to verify user/password
	// # http://mysqldatabaseadministration.blogspot.com/2006/08/storing-passwords-in-mysql.html
	// ####################################################################

	if (!($query = $mysqli->prepare("SELECT COUNT(user) FROM userdb WHERE user = '$uidSafe' and password = md5('$pwSafe')"))) {
		setLoginCookie(-1, null, null, null, "Unable to prepare database");
	}

	if(!$query->execute()) {
		setLoginCookie(-1, null, null, null, "Unable to execute query");
	}

	if(!($query->bind_result($userCount))) {
		setLoginCookie(-1, null, null, null, "Unable to bind results");
	}

	$query->fetch();
	if($userCount === 0) {
		setLoginCookie(-1, null, null, null, "Unable to login with user name / password");
	}
	$query->close();
	
	####################################################################
	# Add to session table once user validates
	####################################################################
	
	// First delete any prior sessions for the user
	if(!($delete = $mysqli->query("DELETE from session_md WHERE user = '$uidSafe'"))) {
		setLoginCookie(-1, null, null, null, "Unable to delete prior session info from database");
	}
	
	// Add record to sesson table: user, sessionID and expiration date (10 minutes)
	// MD5 from: http://www.thingy-ma-jig.co.uk/blog/10-07-2008/generate-random-string-mysql
	if (!($add = $mysqli->query("INSERT INTO session_md VALUES ('$uidSafe', 
			(SELECT SUBSTRING(MD5(RAND()) FROM 1 FOR 20)), 
			(SELECT DATE_ADD(NOW(), INTERVAL 10 MINUTE)))"))) {
		setLoginCookie(-1, null, null, null, "Unable to create session");
	}
	
	// ####################################################################
	// # Retrieve session id to store in cookie
	// ####################################################################
	
	if (!($session = $mysqli->prepare("SELECT sessionID, expires FROM session_md WHERE user = '$uidSafe'"))) {
		setLoginCookie(-1, null, null, null, "Unable to prepare database");
	}

	if(!$session->execute()) {
		setLoginCookie(-1, null, null, null, "Unable to execute query");
	}

	if(!($session->bind_result($sessionID, $expires))) {
		setLoginCookie(-1, null, null, null, "Unable to bind query");
	}

	$session->fetch();
	if($userCount === 0) {
		setLoginCookie("-1", null, null, null, "Unable to retrieve session");
	}
	$session->close();
	setLoginCookie(0, $uidSafe, $sessionID, $expires, "You are now logged in");

	// ####################################################################
	// # Function to  output JSON on login status
	// ####################################################################
	
	function setLoginCookie($status, $userid, $sessionID, $expires, $message) {
		
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

<?php
	// Connect to mysql
	include 'sqlconnect.php'; 
?>


<!--<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN">-->
<html>
<head>
	<script src="jquery-1.9.1.min.js"></script>
	<script src="jquery.cookie.js"></script>
<?php
	# Declare POST variables
	$uid = $_POST['uid'];
	$password = $_POST['pw'];
	$fname = $_POST['fname'];
	$lname = $_POST['lname'];
	$currWgt = $_POST['currWgt'];
	$tarWgt = $_POST['tarWgt'];
	$coachName = $_POST['coachName'];
	
	unset($_POST);
	
	// echo "uid: " . $uid;
	// echo "password: " . $password;
	// echo "first: " . $fname;
	// echo "last: " . $lname;
	// echo "current: " . $currWgt;
	// echo "target: " . $tarWgt;
	// echo "coach: " . $coachName;

	// Use mysql real escape to make input safe for SQL
	$uidSafe = $mysqli->real_escape_string($uid);
	$pwSafe = $mysqli->real_escape_string($password);
	$fnSafe = $mysqli->real_escape_string($fname);
	$lnSafe = $mysqli->real_escape_string($lname);
	$cwSafe = $mysqli->real_escape_string($currWgt);
	$twSafe = $mysqli->real_escape_string($tarWgt);
	$cnSafe = $mysqli->real_escape_string($coachName);
	
	// Validate data before sending to sql
	
	$message = null; // field to track error messages
	
	if(empty($uidSafe) || empty($pwSafe) 
		|| empty($fnSafe) || empty($lnSafe)
		|| empty($cwSafe) || empty($twSafe)
		|| empty($cnSafe)) {
		setRegCookie(-1, "All fields are required");
	}

	// Make sure name/pw contains valid characters, min/max length
	// From: http://forums.phpfreaks.com/topic/149318-solved-only-letters-and-numbers-min-8-max-12-characters/

	if(!preg_match('/^[a-zA-Z0-9]{5,20}$/i', $uidSafe) 
		|| !preg_match('/^[a-zA-Z0-9]{5,20}$/i', $pwSafe)) {
		setRegCookie(-1, "User name / password required can contain 5-20 letters/numbers");
	}
	
	// Make sure first/name only contains letters, spaces and hyphens
	if (!preg_match('/^[a-zA-Z- ]{2,50}$/i', $fnSafe)
		|| !preg_match('/^[a-zA-Z- ]{2,50}$/i', $lnSafe)) {
		setRegCookie(-1, "First/last name can only contain 2-50 letters");
	}
	
	// Check weight values to make sure they are reasonable
	if (intval($cwSafe) < 0 || intval($twSafe) < 0) {
		setRegCookie(-1, "Weights can not be negative");
	}
	
	if (intval($cwSafe) < intval($twSafe)) {
		setRegCookie(-1, "Target weight can not be higher than current weight");
	}
	
	// ####################################################################
	// # Query database to see if user name already exists
	// ####################################################################

	if (!($query = $mysqli->prepare("SELECT COUNT(user) FROM userdb WHERE user = '$uidSafe'"))) {
		setRegCookie(-1, "Unable to prepare database");
	}

	if(!$query->execute()) {
		setRegCookie(-1, "Unable to execute query");
	}

	if(!($query->bind_result($userCount))) {
		setRegCookie(-1, "Unable to bind results");
	}

	$query->fetch();
	if($userCount > 0) {
		setRegCookie(-1, "Username already exists");
	}
	$query->close();
	
	// ####################################################################
	// # Add to user table once user validates
	// ####################################################################

	// Add record to user db
	if (!($adduser = $mysqli->query("INSERT INTO userdb VALUES ('$uidSafe', md5('$pwSafe'), 
			'$fnSafe', '$lnSafe', $cwSafe, $twSafe, '$cnSafe')"))) {
		setRegCookie(-1, "Unable to add username / password");
	}
	else
		setRegCookie(0, "Congratulations - please log in");

	// ####################################################################
	// # Function to output to cookie and redirect page
	// ####################################################################
	
	function setRegCookie($status, $message) {
		if ($status === -1) {
			echo "    <script language='javascript'>" . "\n";
			echo "        $.cookie('userMsg', '" . $message . "', { path: '/'});" . "\n";
			echo "    </script>" . "\n";
			echo "    <script language='javascript'>window.location.replace(\"register.html\");</script>";
		}
		else {
			echo "    <script language='javascript'>" . "\n";
			echo "        $.cookie('userMsg', '" . $message . "', { path: '/'});" . "\n";
			echo "    </script>" . "\n";
			echo "    <script language='javascript'>window.location.replace(\"index.html\");</script>";
		}
		exit;
	}
	
	// echo "all done";
?>
</head>
</html>
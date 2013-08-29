<?php
	// Connect to mysql
	include 'sqlconnect.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Login: Lending Library</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet" media="screen">
	<link href="css/Lend.css" rel="stylesheet" >
	<script src="jquery-1.9.1.min.js"></script>
	<script src="jquery.validate.min.js"></script>
	<script src="jquery.form.min.js"></script>
	<script src="jquery.cookie.js"></script>
	<script type="text/javascript" src="login.js"></script>
	<script type="text/javascript" src="checkinout.js"></script>
	<script type="text/javascript" src="navbar.js"></script>
	<script>
		// **** DOES THIS PAGE REQUIRE LOGIN ???? *****
		$(document).ready(loadNavBar);
		$(document).ready(checkLogin);
		$(document).ready(checkMessages);
	</script>
</head>
<body>


<!-- start content - display user messages -->
<div id="usermsg">
</div>

<div class="container">
<?php

	// ####################################################################
	// # Setup variables
	// ####################################################################

	// Get itemID variable from form
	if ( isset($_GET['id']) && !empty($_GET['id']) )
		$itemID = $mysqli->real_escape_string($_GET['id']);
	else
		$itemID = "";
	// echo "itemid: " . $itemID . "<br>";
	
	// Get userID from cookie
	
	if (isset($_COOKIE['sessUser']) && !empty($_COOKIE['sessUser']))
		$user = $mysqli->real_escape_string($_COOKIE['sessUser']);
	else {
		$user = "";
	}
	// echo "userid: " . $user . "<br>";
	
	// Validate item ID is integer before quering SQL (just digits, no "." or "-")
	
	if (!preg_match('/^[0-9]+$/', $itemID))
		queryError();

	// ####################################################################
	// # Query item type / availability from main table
	// ####################################################################

	if (!($title = $mysqli->prepare("select type, available from items
			where itemID = '$itemID'"))) {
		queryError();
	}
	
	if(!$title->execute()) {
		queryError();
	}
	
	// Check if there are any results
	$title->store_result();
	if ($title->num_rows == 0)
		queryError();
	// echo "Count: " . $title->num_rows . "<br>";
	
	if(!($title->bind_result($type, $available))) {
		queryError();
	}
	
	$title->fetch();
	// echo "type: " . $type;
	echo "<legend>Item #" . $itemID . " - " . htmlspecialchars($type) . "</legend>";
	
	$title->close();
	
	
	// ####################################################################
	// # Query item details
	// ####################################################################

	if (!($details = $mysqli->prepare("select field, units, strValue, numValue from itemDesc 
			where itemID = '$itemID'"))) {
		queryError();
	}
	
	if(!$details->execute()) {
		queryError();
	}

	if(!($details->bind_result($field, $units, $strValue, $numValue))) {
		queryError();
	}

	// Output bootstrap table tags
	echo "<table class =\"table\"><tbody>";

	while($details->fetch()){
		// echo "Field: " . htmlspecialchars($field) . " | ";
		// echo "Units: " . htmlspecialchars($units) . " | ";
		// echo "Value: " . htmlspecialchars($strValue) . " | ";
		// echo "Field: " . htmlspecialchars($numValue) . "<br>";
		
		$fieldDisp = preg_replace('/[-_]/', ' ', htmlspecialchars($field));
		
		$value = $units == "NULL" 
			? htmlspecialchars($strValue) 
			: floatval(htmlspecialchars($numValue)) . " " . htmlspecialchars($units);
			
		echo "<tr><td>" . $fieldDisp . "</td><td>" . $value . "</td></tr>";
	}
	$details->close();
	
	// Output bootstrap closing tags
	echo "</tbody></table>";
	
	// Output checkout button only if user is logged in and item is available
	echo "<div id = 'chkbutton' class='col-12 '>";
	if ($available == 0 || $available == NULL)
		echo "<button type='submit' class='btn-block btn-large btn-default' id=\"checkout\" disabled=\"disabled\" style=\"color:black;background-color:lightgray\">Item Not Available</button>";
	else if (empty($user))
		echo "<button type='submit' class='btn-block btn-large btn-default' id=\"checkout\" disabled=\"disabled\" style=\"color:black;background-color:lightgray\">Login Before Checking Out</button>";
	else
		echo "<button type='submit' class='btn-block btn-large btn-default' id=\"checkout\" value=\"" . $itemID . "\" onclick=\"checkout(" . $itemID . ")\">Check Out</button>";
	echo "</div>";

	// ####################################################################
	// # Query error function
	// ####################################################################
	
	function queryError() {
		echo "Sorry there is an error, try again later <br>";
		exit;
	}
?>
</div>
</body>
</html>
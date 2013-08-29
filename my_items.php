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

	// Get userID from cookie
	
	if (isset($_COOKIE['sessUser']) && !empty($_COOKIE['sessUser']))
		$user = $mysqli->real_escape_string($_COOKIE['sessUser']);
	else {
		errorOutcome(-1, "Must be logged in");
	}
	
	// Display title
	echo "<legend>" . $user . " - Items on Loan</legend>";	
	// ####################################################################
	// # Query items loaned out to user
	// ####################################################################

	if (!($query = $mysqli->prepare("select tx.itemID, tx.checkOutDate, tx.dueDate, tx.checkInDate, itm.type
			from trans as tx left outer join items as itm on tx.itemID = itm.itemID where tx.userID = '$user'"))) {
		queryError();
	}
	
	if(!$query->execute()) {
		queryError();
	}

	if(!($query->bind_result($itemID, $checkDate, $dueDate, $returnDate, $type))) {
		queryError();
	}

	// Output bootstrap table tags
	echo "<table class =\"table\"><thead><tr>";
	echo "<th>Item ID</th>";
	echo "<th>Type</th>";
	echo "<th>Check Out</th>";
	echo "<th>Due Date</th>";
	echo "<th>Check In</th>";
	echo "</tr></thead><tbody>";
	
	// Display retrieved from DB that are still oustanding
	while($query->fetch()){
		

		echo "<tr><td>" . htmlspecialchars($itemID) . 
			"</td><td>" . htmlspecialchars($type) .
			"</td><td>" . htmlspecialchars($checkDate) .
			"</td><td>" . htmlspecialchars($dueDate);
		if (empty($returnDate))
			echo "</td><td>" . "<div id='checkin" . $itemID . "'><div id='btn" . $itemID 
			. "'><button type='submit' class='btn-block btn btn-default' ' onclick='checkin(" 
			. $itemID .")'>Return</button></div></div></td></tr>";
		else
			echo "</td><td>" . htmlspecialchars($returnDate) . "</td></tr>";
			
	}
	
	
	$query->close();
	
	// Output bootstrap closing tags
	echo "</tbody></table>";
	
	// ####################################################################
	// # Function to display error messages
	// ####################################################################
	
	function errorOutcome($status, $message) {
		echo "Status: " . $status . " Message: " . $message . "<br>";
		exit;
	}
?>
</div>
</body>
</html>
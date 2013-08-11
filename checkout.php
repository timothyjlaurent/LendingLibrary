<?php
	// Connect to mysql
	include 'sqlconnect.php'; 
?>
<?php

	// ####################################################################
	// # Setup variables
	// ####################################################################

	// ******* CHANGE TO POST AFTER TESTING *************
	// Get itemID variable from form
	if ( isset($_POST['id']) && !empty($_POST['id']) )
		$itemID = $mysqli->real_escape_string($_POST['id']);
	else
		jsonOutcome(-1, "No item selected");
	// echo "itemid: " . $itemID . "<br>";
	
	// Get userID from cookie
	
	if (isset($_COOKIE['sessUser']) && !empty($_COOKIE['sessUser']))
		$user = $mysqli->real_escape_string($_COOKIE['sessUser']);
	else {
		jsonOutcome(-1, "Must be logged in");
	}
	// echo "userid: " . $user . "<br>";
	
	// Validate item ID is integer before quering SQL (just digits, no "." or "-")
	
	if (!preg_match('/^[0-9]+$/', $itemID))
		jsonOutcome(-1, "Invalid item ID");

	// ####################################################################
	// # Attempt to Checkout Item
	// ####################################################################
	// Optimistic locking help from :
	// http://www.dbasupport.com/forums/showthread.php?7282-What-is-Optimistic-Locking-vs.-Pessimistic-Locking
	// Use multiple where clauses to verify availability status hasn't changed

	if (!($lock = $mysqli->prepare("update items set available = 0 
			where itemID = '$itemID' and available = 1 and available IS NOT NULL"))) {
		jsonOutcome(-1, "Can't access database");
	}
	
	if (!$lock->execute())
		jsonOutcome(-1, "Can't execute query");
	
	// Check if checkout was successful 
	$lock->store_result();
	// echo "Updated rows: " . $lock->affected_rows;

	// ********** TURN BACK ON AFTER TESTING **************
	if ($lock->affected_rows == 0)
		jsonOutcome(-1, "Unable to check out");
	
	$lock->close();
		
	// ####################################################################
	// # Create transaction record
	// ####################################################################
	// Does not need to be atomic since insert new record and not editing existing

	// Calculate dates for check in/out, help from
	// http://stackoverflow.com/questions/277247/increase-days-to-php-current-date
	$today = date('Y-m-d H:i:s');
	// echo "today: " .  $today . "<br>";
	
	$dueDate = date('Y-m-d H:i:s', strtotime("+7 days"));
	// echo "due date: " .  $dueDate . "<br>";
	
	if (!($tran = $mysqli->prepare("insert into trans (userId, itemID, checkOutDate, dueDate) 
			values ('$user', '$itemID', '$today', '$dueDate')"))) {
		jsonOutcome(-1, "Can't access database");
	}
	
	if (!$tran->execute())
		jsonOutcome(-1, "Can't execute query");
	
	// Check if checkout was successful 
	$tran->store_result();
	// echo "Updated rows: " . $tran->affected_rows;

	// Unwind earlier checkout if query was unsuccessful
	if ($tran->affected_rows == 0) {
		reverseTran();
		jsonOutcome(-1, "Unable to check out");
	}
	
	$tran->close();

	// Output sucess to JSON with due date
	jsonOutcome(0, $dueDate);
	
	
	// ####################################################################
	// # Reverse checkout if unable to finish write to transaction table
	// ####################################################################
	// Needed since earlier transaction was successful and item was set to
	// Not available before failing
	
	function reverseTran() {
		// No need to call query error on error since it will be called
		// upon return
		
		$reverse = $mysqli->prepare("update items set available = 1 
				where itemID = '$itemID' and available = 0 and available IS NOT NULL");
		
		$reverse->execute();
	}
	
	// ####################################################################
	// # Function to output results to JSON
	// ####################################################################
	
	function jsonOutcome($status, $message) {

		// Instantiate object to store login information
		$outcomeObj = new stdClass;
		$outcomeObj->status = htmlspecialchars($status);
		$outcomeObj->message = htmlspecialchars($message);

		// Encode in JSON
		header('Content-Type: application/json');
		echo json_encode($outcomeObj);
		
		exit;
	}
	
	
	
?>
<?php
	// Connect to mysql
	include 'sqlconnect.php'; 
?>
<?php

	// ####################################################################
	// # Query itemTypes table to list of item types
	// ####################################################################

	if (!($query = $mysqli->prepare("select distinct type from itemTypes"))) {
		queryError();
	}
	
	if(!$query->execute()) {
		queryError();
	}

	if(!($query->bind_result($value))) {
		queryError();
	}
	// Create array to return as JSON object
	$menu = array();
	
	// Write records retrieved from DB
	while($query->fetch()){
		array_push($menu, htmlspecialchars($value));
	}
	
	// Output query results to JSON
	$query->close();
	header('Content-Type: application/json');
	echo json_encode($menu);
	exit;
	
	
	// ####################################################################
	// # function for database query error situatons
	// ####################################################################
	
	function queryError() {
		$menu = array();
		array_push($menu, null);
		header('Content-Type: application/json');
		echo json_encode($menu);
		exit;
	}

?>

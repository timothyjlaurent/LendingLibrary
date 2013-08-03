<?php
	// Connect to mysql
	include 'sqlconnect.php'; 
?>
<?php
	// Declare POST variables
	$type = $mysqli->real_escape_string($_GET['type']);
	// echo $type;


	
	// ####################################################################
	// # Query itemTypes table to list of item types
	// ####################################################################

	if (!($query = $mysqli->prepare("select field, units, 
			isnumber from itemTypes where searchable = '1' 
			and type = '$type'"))) {
		queryError();
	}
	
	if(!$query->execute()) {
		queryError();
	}

	if(!($query->bind_result($field, $units, $isnumber))) {
		queryError();
	}

	// Create array to return as JSON object
	$menu = array();
	
	// Write records retrieved from DB
	while($query->fetch()){
		
		// Write each element to array
		$attribute = array(
			"field" => htmlspecialchars($field),
			"units" => htmlspecialchars($units),
			"numeric" => htmlspecialchars($isnumber)
		);
	
		array_push($menu, $attribute);
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

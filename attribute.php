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

	$query = "select field, units, 
			isnumber, required from itemTypes where ";
	if ($_GET['search']==1){
		$query .= "searchable = '1' and ";
	}		
	$query .= "type = '$type'";

	if (!($query = $mysqli->prepare($query))) {
		queryError();
	}
	
	if(!$query->execute()) {
		queryError();
	}

	if(!($query->bind_result($field, $units, $isnumber, $required))) {
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
			"numeric" => htmlspecialchars($isnumber),
			"required" => htmlspecialchars($required)
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
		alert('error');
		$menu = array();
		array_push($menu, null);
		header('Content-Type: application/json');
		echo json_encode($menu);
		exit;
	}

?>

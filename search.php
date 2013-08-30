<?php
	// Connect to mysql
	include 'sqlconnect.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Search Results: Lending Library</title>
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
<?php
	// Declare form GET variables
	$type = $_GET['typeoptions'];
	$status = $_GET['available'];
	
	$schParams = array();
	$compOpt   = array();

	foreach ( $_GET as $key => $value){
		if (!empty($value)){
			if (preg_match("/val-(.+)$/", $key, $matches)){
				$schParams[$matches[1]] = $value;
			} elseif ( preg_match("/comp-(.+)$/", $key, $matches) ){
				$compOpt[$matches[1]] = $value;
			}
		}
	}
	$ids = array();

	$query = "select itemDesc.itemID, field, units, strValue, numValue, available from items join itemDesc on items.itemID=itemDesc.itemID where type = '";
	$query .= "$type' ";
	if ($_GET['available'] == 'all'){
		
	}
	elseif ($_GET['available'] == 0){
		$query .= " and available = 0 ";
	} elseif ($_GET['available'] == 1) {
		$query .= " and available = 1 ";
	}
	$query .= " and itemDesc.itemID in ( ";
	foreach ( $schParams as $key => $val){
		// echo $key." ".$val."<br>";
		$key = $mysqli->real_escape_string($key);
		$val = $mysqli->real_escape_string($val);
		$query .= "select itemDesc.itemId from itemDesc where field = '$key' and ";
		if (isset($compOpt[$key])){
			if ($compOpt[$key] == 'gt'){
				$comp = ">";
			} elseif ($compOpt[$key] == 'lt'){
				$comp = "<";
			}
			$num = 1;
		} else {
			$comp = "=";
		}
		if ($num){
			$query .= "numValue $comp $val";
		} else {
			$query .= "strValue = $val";
		}
		$query .= " and itemDesc.itemID in ( ";
	}
	$query = substr($query, 0, -25);
	foreach ( $schParams as $key){
		$query .= ")";
	}
	// echo "Query   ".$query."<br>";
	if(!($query = $mysqli->prepare($query))){
		echo "<br>".$mysqli->error;	
	}
	if(!($query->execute())){
		queryError();
	}
	if(!($query->bind_result($itemID, $field, $units, $strValue, $numValue, $available))){
		queryError();
	}
	
	if (isset($_COOKIE['sessUser']) && !empty($_COOKIE['sessUser']))
		$user = $mysqli->real_escape_string($_COOKIE['sessUser']);
	else {
		$user = "";
	}	
	$flds = array();
	$availArr = array();
	while ( $query->fetch()){
		$availArr[$itemID] = $available;
		// echo "$itemID, $field, $units, $strValue, $numValue<br>";
		$fldDisp = preg_replace('/[-_]/', ' ', htmlspecialchars($field));
		$value = $units == "NULL" 
			? htmlspecialchars($strValue) 
			: floatval(htmlspecialchars($numValue)) . " " . htmlspecialchars($units);
		 
		
// check if array is initialized 
		if ( !array_key_exists ($itemID , $flds )){
			$flds[$itemID] = array($fldDisp => $value);
		} else {
			$flds[$itemID][$fldDisp] = $value;
		}
		
	}
	// print_r ($flds);
	foreach ( $flds as $id => $atts ) { 
		echo "<table class =\"table\"><tbody>";

		foreach ($atts as $name => $val){
			echo "<tr><td>" . $name . "</td><td>" . $val . "</td></tr>";
		}

		echo "</tbody></table>";		
		echo "<div id = 'chkbutton' class='col-12 '>";
		if ($availArr[$id] == 0 || $availArr[$id] == NULL) // Changed array reference from $available to $availArr
			echo "<button type='submit' class='btn-block btn-large btn-default' id=\"checkout\" disabled=\"disabled\" style=\"color:black;background-color:lightgray\">Item Not Available</button>";
		else if (empty($user))
			echo "<button type='submit' class='btn-block btn-large btn-default' id=\"checkout\" disabled=\"disabled\" style=\"color:black;background-color:lightgray\">Login Before Checking Out</button>";
		else
			echo "<button type='submit' class='btn-block btn-large btn-default' id='checkout" . $id . "' value='" . $id . "' onclick='checkout(" . $id . ")'>Check Out</button>";
		echo "</div>";
	}


	function queryError() {
		echo "Sorry there is an error, try again later <br>";
		exit;	}
?>
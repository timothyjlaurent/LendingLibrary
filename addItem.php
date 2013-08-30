<?php
	// Connect to mysql
	include 'sqlconnect.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<title>Add Item: Lending Library</title>
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
<?php

	// Declare form GET variables
	$type = $_GET['typeoptions'];
	
	$schParams = array();

	foreach ( $_GET as $key => $value){
		if (!empty($value)){
			if (preg_match("/val-(.+)$/", $key, $matches)){
				$schParams[$matches[1]] = $value;
			} 
		}
	}
	$ids = array();

	$query = "insert into items (`type`,`available`) values ('$type', 1)";
	echo $query;
	if(!($query = $mysqli->prepare($query))){
		echo "<br>".$mysqli->error;	
		queryError();
	}
	if(!($query->execute())){
		echo "<br>".$mysqli->error;	
		queryError();
	}
	$id = $mysqli->insert_id;
	echo $id;

	include 'itemTypeMaps.php';
	print_r ($typeMap);
	echo "<br>type $type<br>";
	foreach ( $schParams as $key => $val){
		if ( $typeMap[$type][$key] != 0 || $typeMap[$type][$key] != "NULL"){
			$query = "insert into itemDesc (`itemID`,`field`, `units`, `numValue`) values ($id,'$key','".$typeMap[$type][$key]."',$val)";
			echo "<br>query $query<br>";
		} else {
			$query = "insert into itemDesc (`itemID`,`field`, `strValue`) values ($id,'$key' ,'$val')";
			echo "<br>query $query<br>";
		}
		if(!($query = $mysqli->prepare($query))){
			echo "<br>".$mysqli->error;	
		}
		// echo "<br>query ".$query."<br>";
		if(!($query->execute())){
			queryError();
		}
	}

	function queryError() {
		echo "Sorry there is an error, try again later <br>";
		exit;	}
?>
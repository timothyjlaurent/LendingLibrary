<?php
	// Connect to mysql
	include 'sqlconnect.php'; 
?>
<?php
	// Declare form GET variables
	$type = $_GET['typeoptions'];
	$status = $_GET['checkout'];
	$option1 = $_GET['attrib1opt'];
	$option2 = $_GET['attrib2opt'];
	$option3 = $_GET['attrib3opt'];
	
	// NEED TO MAKE THIS CONDITION IF OPT1 IS SELECTED
	$comp1 = $_GET['attrib1comp'];
	$comp2 = $_GET['attrib2comp'];
	$comp3 = $_GET['attrib3comp'];

	$input1 = $_GET['attrib1input'];
	$input2 = $_GET['attrib2input'];
	$input3 = $_GET['attrib3input'];
	
	$search = $_GET['searchterm'];
	
	echo $type . ":" . $status . ":" . $search . "<br>";
	echo $option1 . " " . $comp1 . " " . $input1 . "<br>";
	echo $option2 . " " . $comp2 . " " . $input2 . "<br>";
	echo $option3 . " " . $comp3 . " " . $input3 . "<br>";

	// do mysqli real escape during input validation
	
?>
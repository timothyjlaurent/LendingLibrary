<?php	

	include 'sqlconnect.php'; 
	$query = "select `type`, `field`, `units` from itemTypes";
	if(!($query = $mysqli->prepare($query))){
		echo "<br>".$mysqli->error;	
	}
	if(!($query->execute())){
		queryError();
	}

	if(!($query->bind_result($typ, $field, $units))){
		queryError();
	}

	$typeMap = array();
	while ($query->fetch()){
		// echo "$type, $field, $units<br>";
		if ( ! array_key_exists($typ, $typeMap) ){
			if ($units){
				$typeMap[$typ] = array( $field => $units);
			}
			 else $typeMap[$typ] = array( $field => 0);
		} else {
			if ( $units ){
				$typeMap[$typ][$field] = $units;
			} else {
				$typeMap[$typ][$field] = 0;
			}
				
		}
	}
	print_r ($typeMap);
?>
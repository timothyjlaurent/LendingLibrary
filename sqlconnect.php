<?php

$dbhost = 'oniddb.cws.oregonstate.edu';
$dbname = 'laurentt-db';
$dbuser = 'laurentt-db';
$dbpass = '9IzmnmSrzfhF3SCb';

$mysqli = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname) or die("Error " . mysqli_error($mysqli));;



echo 'Successfully connected to database!';


?>
<?php
// X921WAgtdSEqpSGb wongbe-db
$dbhost = 'oniddb.cws.oregonstate.edu';
// $dbname = 'laurentt-db';
// $dbuser = 'laurentt-db';
// $dbpass = '9IzmnmSrzfhF3SCb';
$dbname = 'wongbe-db';
$dbuser = 'wongbe-db';
$dbpass = 'X921WAgtdSEqpSGb';
    //Report all PHP errors
    error_reporting(E_ALL);
    ini_set('display_errors', 'On');
    //Connect to database
    $mysqli = new mysqli($dbhost,$dbuser, $dbpass , $dbname);

    /* check connection */
    if ($mysqli->connect_errno) {
        echo "Connection error " . $mysqli->connect_errno . " " . $mysqli->connect_error;
    }
?>
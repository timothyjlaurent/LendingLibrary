<?php

$dbhost = 'oniddb.cws.oregonstate.edu';
$dbname = 'laurentt-db';
$dbuser = 'laurentt-db';
$dbpass = '9IzmnmSrzfhF3SCb';

$mysql_handle = mysql_connect($dbhost, $dbuser, $dbpass)
    or die("Error connecting to database server");

mysql_select_db($dbname, $mysql_handle)
    or die("Error selecting database: $dbname");

echo 'Successfully connected to database!';


?>
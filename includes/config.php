<?php

/* USER.CLASS.PHP CONFIG - CREATED BY 'Poppins' 2011
-----------------------------------------------------
--This is used to set the database details for user.class.php
--to access and use. They must be set correctly and then stored
--in the same directory as user.class.php.
--
--The names of the variables are pretty self explanitory.
-----------------------------------------------------
*/

//Database creditentials (EDIT THESE)
$dbHost = "localhost";
$dbDatabase = "users";
$dbUsername = "root";
$dbPassword = "root";

//Database connect (Most of the time these can be kept the same)
$dbCon = mysql_pconnect($dbHost, $dbUsername, $dbPassword) or die(mysql_error());
mysql_select_db($dbDatabase, $dbCon);

?>
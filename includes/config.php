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
$dbHost     = 'localhost';
$dbDatabase = 'mc';
$dbUsername = 'mcuser';
$dbPassword = 'SGT95eGDsQD6hC9z';

$loginAttemptCount = 3;
$loginAttemptTime  = strtotime( '-2 minutes' );

//Database connect (Most of the time these can be kept the same)
//This is for php 5 - comment out or remove if using php 7
//$dbCon = mysql_pconnect( $dbHost , $dbUsername , $dbPassword ) or die( mysql_error() );
//mysql_select_db( $dbDatabase , $dbCon );
//This is for php 7 - uncomment if using php 7
$dbCon = mysqli_connect( $dbHost , $dbUsername , $dbPassword ) or die( mysql_error() );
mysqli_select_db( $dbCon, $dbDatabase );

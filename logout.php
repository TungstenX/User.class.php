<?php 

session_start();
include( 'includes/version.php' );

$user = new User();

$user->logOut();

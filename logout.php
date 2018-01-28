<?php 

session_start();
//php 5
include( 'includes/user.class.php' ); 
//php 7
//include( 'includes/user.class.7.php' );

$user = new User();

$user->logOut();

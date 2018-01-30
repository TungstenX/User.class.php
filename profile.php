<?php

session_start();
include( 'includes/version.php' );

$user = new User();

// If the user is logged in and the get variable is set and not null
$id = getIntParam('id');
if( $user->isLoggedIn() && !is_null($'id')){
  // Search the ID for the ID supplied
  $results = $user->search( 'id' , $id);
  if( $results ){
    while( $row = fetch_array( $results ) ){
	    printf( 'ID: %s  Name: %s <br />' , $row['id'] , $row['username'] );
    }
  }else{
?>
	<i>Sorry. No results where found.</i>
<?php
  }
}else{
?>
	<i>Sorry. Please enter a search term</i>
<?php
}

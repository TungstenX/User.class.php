<?php 

session_start();
include( 'includes/version.php' );

$user = new User();
$name = getStrParam('name');
if( $user->isLoggedIn() && !is_null($name)){
  $results = $user->search( 'username' , $name));
  if( $results ){
    while( $row = fetch_array( $results ) ){
      printf( 'ID: %s  <a href="profile.php?id=%s">Name: %s</a> <br />' , $row['id'] , $row['id'] , $row['username'] );
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

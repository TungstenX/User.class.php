<?php 

session_start();
include( 'includes/version.php' );

$user = new User();
$message_id = getIntParam('read');

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>User.Class - Members</title>
<link rel="stylesheet" type="text/css" href="css/reset.css" />
<link rel="stylesheet" type="text/css" href="css/main.css" />
<!--WEB FONTS -->
<link href="//fonts.googleapis.com/css?family=Lato:100&v2" rel="stylesheet" type="text/css"/>
<!--&&&&&&&&&-->
</head>
<body>
	<div id="container">
		<h1>Members Area</h1>
<?php
if( $user->isLoggedIn() ){
  $login = $user->userInfo( $_SESSION['userName'] );
?>
		<a href="messages.php"><img src="images/mail.png" style="float:right;padding:15px 15px 0px 0px;margin-left:5px;"/></a>
<?php
  if( $amount = $user->messageNotification( $login['id'] ) ){
?>
		<div id="messages">
			<div id="notification">
				<i><?php echo $amount; ?> new message<?php echo ( $amount!=1 ? 's' : '' ); ?></i>
			</div>
		</div>
<?php
  }
?>
		<div id="content">
			<p class="description" style="margin-bottom:20px">
				Welcome to the members area <strong><?php echo $login['username']; ?></strong>!
        Here you can edit your account, View the news, read your messages and other fun stuff. - Admins
			</p>
		</div>
		<div class="logout"><i><a href="logout.php">[Logout]</a><i></div>
<?php
}else{
?>
			<div id="content">
				<p class="description" style="margin-bottom:20px">
					Error. Please log in <a href="login.php" style="font-weight:bold;">here.</a>
				</p>
			</div>
<?php
}
?>
	</div>
</body>
</html>

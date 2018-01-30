<?php
//Include this file for php7
//TungstenX: Consider using php built in input filter rather than db escape

include( 'config.php' );

class User {

  function __construct(){}

  function randomString( $len=32 ){
    // Initialise a string
    $s = '';
    // Possible characters
    $letters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

    for( $i=0 ; $i<$len ; $i++ ){
      // Grab a random letter for $letters
      $char = $letters[mt_rand( 0 , strlen( $letters )-1 )];
      $s .= $char; //Add it to the string
    }
    return $s;
  }

  function hash( $password , $salt , $created_at ){
    // Reverses the date and removes the dashes
    $date = sha1( strrev( (string) $created_at ) );
    // Yay! Bcrypt
    return crypt($salt . $password . $date . $salt , '$2a$12$' . $salt);
  }

  function salt(){
    $firstSalt = substr( str_replace( '+' , '.' , base64_encode( sha1( microtime( true ) , true ) ) ) , 0 , 22 );
    return $firstSalt;
  }

  function register( $userName , $userPassword ){
    global $dbCon;
    if( $this->exists( $userName ) )
      return false;

    $salt = $this->salt(); //Generate a salt using the username provided
    $date = time();
    $password = $this->hash( $userPassword , $salt , $date ); //Hash the password with the new salt

    //The query for inserting our new user into the DB
    $q1 = sprintf( "INSERT INTO users (username, password, rand, created_at) VALUES ('%s', '%s', '%s', '%s')" ,
            real_escape_string( $userName ) ,
            real_escape_string( $password ) ,
            real_escape_string( $salt ) ,
            real_escape_string( $date )
          );
    if( mysqli_query( $dbCon, $q1 ) )
      return mysqli_insert_id( $dbCon);
    die( mysqli_error( $dbCon ) ); // Run it. If it doesn't go through stop the script and display the error.
    return false;
  }

  function update( $userName , $oldPassword , $newPassword ){
    global $dbCon;
    if( !$this->exists( $userName ) )
      return false;
    $q1 = sprintf( "SELECT password, rand, created_at FROM users WHERE username='%s'" ,
            real_escape_string( $userName )
          );
    $r1 = fetch_array( mysqli_query( $dbCon, $q1 ) );
    $oldHashDB = $this->hash( $r1['password'] , $r1['rand'] , $r1['created_at'] );
    $oldHashIn = $this->hash( $oldPassword , $r1['rand'] , $r1['created_at'] );
    if( $oldHashDB == $oldHashIn ){
      $salt = $this->salt();
      $newHash = $this->hash( $newPassword , $salt , $r1['created_at'] );
      $q2 = sprintf( "UPDATE users SET password='%s', rand='%s' WHERE username='%s'" ,
              real_escape_string( $newHash ) ,
              real_escape_string( $salt ) ,
              real_escape_string( $userName )
            );
      if( mysqli_query( $dbCon, $q2 ) ){
        $this->setLoggedIn( $userName , $newPassword );
        return true;
      }
    }
  }

  function verify( $userName , $userPassword ){
    global $dbCon;
    // Grabbing all the user details with this query
    $q1 = sprintf( "SELECT password, rand, created_at FROM users WHERE username='%s'" ,
            real_escape_string( $userName )
          );
    $r1 = fetch_array( mysqli_query( $dbCon, $q1 ) );
    $ph = $this->hash( $userPassword , $r1['rand'] , $r1['created_at'] );
    // Return whether it is true or false
    return ( $r1['password'] == $this->hash( $userPassword , $r1['rand'] , $r1['created_at'] ) );
  }

  function setLoggedIn($userName, $userPassword) {
    //This function is self explanitory :)
    $_SESSION['loggedIn'] = true;
    $_SESSION['userName'] = $userName;
    $_SESSION['userPassword'] = $userPassword;
  }

  function isLoggedIn() {
    return ( isset( $_SESSION['loggedIn'] )
             && $_SESSION['loggedIn']
             && $this->verify( $_SESSION['userName'] , $_SESSION['userPassword'] ) );
  }

  function redirectTo($page) {
    if( !headers_sent() ){
      header( 'Location: ' . $page . '.php' );
    }
    die( '<a href="'.$page.'.php">Go to '.$page.'.php</a>' );
  }

  function userInfo( $userName ){
    global $dbCon;
    // This function returns all user details to the front end. This is to save storing it all in sessions
    $q1 = sprintf( "SELECT * FROM users WHERE username='%s'" ,
            real_escape_string( $userName )
          );
    // Fetch and Return the array
    return fetch_array( mysqli_query( $dbCon, $q1 ) );
  }

  function userInfoId( $UID ){
    global $dbCon;
    // This function returns all user details to the front end. This is to save storing it all in sessions
    $q1 = sprintf( "SELECT * FROM users WHERE id=%s" ,
            (int) $UID
          );
    // Fetch and Return the array
    return fetch_array( mysqli_query( $dbCon, $q1 ) );
  }

  function logOut(){
    // If they are logged in
    if( isset( $_SESSION['loggedIn'] ) ){
      // Unset the session variables
      unset( $_SESSION['loggedIn'] , $_SESSION['userName'] , $_SESSION['userPassword'] );
      // Redirect to the login page
      $this->redirectTo( 'login' );
    }
  }

  function exists( $userName ){
    global $dbCon;
    // Checks a user exists (for the register page)
    $q1 = sprintf( "SELECT username FROM users WHERE username = '%s'" ,
            real_escape_string( $userName )
          );
    return (bool) mysqli_num_rows( mysqli_query( $dbCon, $q1 ) );
  }

  function search( $field , $term ){
    global $dbCon;
    $sql_field = false;

    switch( $field ){
      case 'id' :
        $sql_field = 'id';
        break;
      case 'username' :
        $sql_field = 'username';
        break;
    }
    if( !$sql_field )
      return false;
    $q1 = sprintf( "SELECT * from users WHERE %s LIKE '%%%s%%'" ,
            real_escape_string( $term )
          );
    $r1 = mysqli_query( $dbCon, $q1 );
    if( !mysqli_num_rows( $r1 ) )
      return false;
    return $r1;
  }

  function messageNotification( $UID ){
    global $dbCon;
    // Select all unread notifications
    $q1 = sprintf( "SELECT * FROM messages WHERE message_to = '%s' AND message_read = '0'" ,
            (int) $UID
          );
    $r1 = mysqli_query( $dbCon, $q1 );
    // Return the number
    return mysqli_num_rows( $r1 );
  }

  function displayMessages( $action , $UID , $ID=NULL ){
    global $dbCon;
    $where = false;

    switch( $action ){
      case 'list' :
        $where = sprintf( "messages.message_to = %s ORDER BY messages.message_id DESC" ,
                   (int) $UID
                 );
        break;
      case 'read' :
        $where = sprintf( "messages.message_id = %s" ,
                   (int) $ID
                 );
    }
    if( !$where )
      return null;
    $q = sprintf( "SELECT * FROM messages INNER JOIN users ON messages.message_from=users.id WHERE %s" ,
           $where
         );
    $r = mysqli_query( $dbCon, $q );
    if( !mysqli_num_rows( $r ) )
      return false;
    return $r;
  }

  function setMessageStatus( $messageID , $status ){
    global $dbCon;
    $q = sprintf( "UPDATE messages SET message_read = %s WHERE message_id = %s" ,
            (int) $status ,
            (int) $messageID
         );
    mysqli_query( $dbCon, $q );
  }

  function setMessageUnread( $messageID ){
    $this->setMessageStatus( $messageID , 0 );
  }
  function setMessageRead( $messageID ){
    $this->setMessageStatus( $messageID , 1 );
  }

  function sendMessage( $to , $from , $subject , $message ){
    global $dbCon;
    $q = sprintf( "INSERT INTO messages (message_to, message_from, message_subject, message, message_read) VALUES ('%s', '%s', '%s', '%s', 0)" ,
           real_escape_string( $to ) ,
           real_escape_string( $from ) ,
           real_escape_string( $subject ) ,
           real_escape_string( $message ));
    return mysqli_query( $dbCon, $q );
  }

  function deleteMessage( $messageID ){
    global $dbCon;
    $q = sprintf( "DELETE FROM messages WHERE message_id = '%s'" ,
           (int) $messageID
         );
    return mysqli_query( $dbCon, $q );
  }

  function string_shorten( $text , $len ){
    // Strip any linebreaks or multiple-spaces
    $text = preg_replace( array( "/\n|\r/" , '\s\s+' ) , ' ' , $text );
    // Split the text using the wordwrap() function
    $lines = explode( "\n" , wordwrap( $text , $len ) );
    // Get the First Line and add continuation ... sign
    return $lines[0].'...'; //Return the value
  }

  function checkLevel( $i ){
    $levels = array( 'Normal' , 'Moderator' , 'Admin' );
    return $levels[$i];
  }

  function smiley($string) { //This is our smileys!
    $smileys = array(
      '0:)' => '<img src="images/angel.png" />', //It looks like this: 'SHORTCODE' => 'HTML'
      ':S' => '<img src="images/awww.png" />', //So type :S and this'll replace it with <img src="images/awww.png" />
      ':|' => '<img src="images/disheartened.png" />',
      'x)' => '<img src="images/ecstatic.png" />',
      ':D' => '<img src="images/great.png" />',
      ':P' => '<img src="images/just-like-that.png" />',
      ':@' => '<img src="images/kill-u.png" />',
      ':x' => '<img src="images/mouthshut.png" />',
      ':)' => '<img src="images/nice.png" />',
      'D:' => '<img src="images/omg.png" />',
      ':(' => '<img src="images/sad.png" />',
      ';)' => '<img src="images/wink.png" />',);
    return(strtr($string, $smileys)); //Return the changed string
  }
}

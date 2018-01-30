<?php
/**
* Helper functions
*/
function real_escape_string($text) {
    return mysql_real_escape_string($text);
}

function fetch_array($input) {
    return mysql_fetch_array( $input);
}

function getIntParam($name) {
  if(isset($_GET[$name])) {
    return real_escape_string($name);
  }
  return null;
}    

function getStrParam($name) {
  if(isset($_GET[$name])) {
    return real_escape_string($name);
  }
  return null;
}

function getPostStrParam($name) {
  if(isset($_POST[$name])) {
    return real_escape_string($name);
  }
  return null;

}


<?php
// PHP_VERSION_ID is available as of PHP 5.2.7, if our 
// version is lower than that, then emulate it
if (!defined('PHP_VERSION_ID')) {
  $version = explode('.', PHP_VERSION);
  define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
}

// PHP_VERSION_ID is defined as a number, where the higher the number 
// is, the newer a PHP version is used. It's defined as used in the above 
// expression:
//
// $version_id = $major_version * 10000 + $minor_version * 100 + $release_version;
//
// Now with PHP_VERSION_ID we can check for features this PHP version 
// may have, this doesn't require to use version_compare() everytime 
// you check if the current PHP version may not support a feature.
//
// For example, we may here define the PHP_VERSION_* constants thats 
// not available in versions prior to 5.2.7
if (PHP_VERSION_ID < 50207) {
  define('PHP_MAJOR_VERSION',   $version[0]);
  define('PHP_MINOR_VERSION',   $version[1]);
  define('PHP_RELEASE_VERSION', $version[2]);
}

//echo "Version: " . PHP_MAJOR_VERSION . "." . PHP_MINOR_VERSION . "." . PHP_RELEASE_VERSION;
/**
* Check php's version
* @return true if version is >= 7.x.x
*/
function isVer7() {
  return PHP_MAJOR_VERSION >= 7;
}

/**
* Check php's version
* @return true if version >= 5.2.x
*/
function isVer52() {
  if(PHP_MAJOR_VERSION > 5) {
    return true;
  } else if(PHP_MAJOR_VERSION == 5) {
    return PHP_MINOR_VERSION >= 2;
  } else {
    return false;
  }
}
// Load the correct files for php version
if(isVer7()) {
    require_once('user.class.7.php');
    require_once('helper.7.php');
} else {
  require_once('user.class.php');
  if(isVer52()) {
    require_once('helper.52.php');
  } else {
    require_once('helper.5.php');
  }
}

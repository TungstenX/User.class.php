<?php
/**
* Helper functions
*/
function real_escape_string($text) {
    return filter_var($text, FILTER_SANITIZE_STRING);
}

function getIntParam($name) {
  return filter_input(INPUT_GET, $name, FILTER_SANITIZE_NUMBER_INT);
}

function getStrParam($name) {
  return filter_input(INPUT_GET, $name, FILTER_SANITIZE_STRING);
}

function getPostStrParam($name) {
  return filter_input(INPUT_POST, $name, FILTER_SANITIZE_STRING);
}


<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Set cookie parameters defined in the php.ini file. 
// The effect of this function only lasts for the duration of the script. 
session_set_cookie_params(['samesite' => 'Strict']);
if (!isset($_SESSION)) {
    session_start();
}

/*
 * is_logged() returns true if the user is authenticated and autorized
 * 
 * session_status() is used to return the current session status.
 * 
  PHP_SESSION_DISABLED if sessions are disabled.
  PHP_SESSION_NONE if sessions are enabled, but none exists.
  PHP_SESSION_ACTIVE if sessions are enabled, and one exists.
 */

function is_logged():bool {
    if (session_status() != PHP_SESSION_ACTIVE) {
        return false;
    }
    if (isset($_SESSION[config("site.url")]['user']) && !empty($_SESSION[config("site.url")]['user'])) {
        return true;
    } else {
        return false;
    }
}

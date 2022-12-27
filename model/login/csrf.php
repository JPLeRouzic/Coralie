<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Generate csrf token and put it in
//  $_SESSION[config("site.url")]['csrf_token']
//  The generated token is specific to the session.
// The date of csrf generation is stored in session variable
function generate_csrf_token() {
    $_SESSION[config("site.url")]['csrf_token'] = bin2hex(random_bytes(32) . mt_rand(10000, 90000));
    $_SESSION[config("site.url")]['csrf_age'] = microtime(true);
}

// Get csrf token
function get_csrf() {
    if (!isset($_SESSION[config("site.url")]['csrf_token']) || empty($_SESSION[config("site.url")]['csrf_token'])) {
        generate_csrf_token();
    }
    return $_SESSION[config("site.url")]['csrf_token'];
}

/* Check the csrf token
 *
 * If you have a security requirement that each CSRF token is allowed to be 
 * usable exactly once, the simplest strategy is to regenerate it after each 
 * successful validation. 
 * However, doing so will invalidate every previous token which doesn't mix 
 * well with people who browse multiple tabs at once.
 */

function is_csrf_proper(string $csrf_token) {
    // As CSRF.length is given in secondes and $time_ok in µsec
    $csrflength = (int) (config('CSRF.length') * 1000000) ;
    if ($csrf_token == get_csrf()) {
        $csrf_now = microtime(true);
        $time_ok = (int) ($csrf_now - $_SESSION[config("site.url")]['csrf_age']) ;
// echo '<br>csrf_age: ' ; var_dump($_SESSION[config("site.url")]['csrf_age']) ;
// echo '<br>csrflength: ' ; var_dump($csrflength) ;
// echo '<br>time_past: ' ; var_dump($time_ok) ;
        if ($time_ok < $csrflength) { // > 200 secondes
            generate_csrf_token();
            return true;
        }
    }
    return false;
}

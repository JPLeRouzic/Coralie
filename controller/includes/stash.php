<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Add content to the dispatch's output buffer
function add_content_stash($value) {
    // Add content (usually a post) to stash()
    $uno = stash('$content$', true, $value);
    return $uno;
}

/*
 * get content from the dispatch's output buffer
 * Usually it is sent to the browse via an "echo get_content_stash() in views/.../layout.html.php
 */

function get_content_stash() {
    // Get the content (usually a post) from stash()
    $uno = stash('$content$', false, null);
    return $uno;
}

/*
 * stash() can be used in two ways depending if $value is set
 * - if $add is set: We add a new content
 * - if $add is false: stash() returns the existing content
 * 
 * $_stash is an array just indexed by '$content$'
 * array(1) { ["$content$"]=> string(1075) "..." }
 */

function stash(string $name, bool $add, string $value = null) {
    static $_stash = array();

    if ($add === false) {
        // The content already exists, return it
        $uno = isset($_stash[$name]) ? $_stash[$name] : null;
        return $uno;
    } else {

        // It's a new route, add it to the array
        $_stash[$name] = $value;

        return $value;
    }
}


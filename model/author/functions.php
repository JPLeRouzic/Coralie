<?php

// Get author names. Unsorted.
// All authors in this site are returned in an array.
function get_author_name():array {
    static $_author = array();

    $url = 'content/index/index-author.txt';
    $_author = unserialize(file_get_contents($url));

    return $_author;
}

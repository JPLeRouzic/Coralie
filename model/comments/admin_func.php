<?php 

// Get author name. Unsorted.
function get_author_name()
{
    static $_author = array();

    if (empty($_author)) {
        $url = 'cache/index/index-author.txt';
        if (!file_exists($url)) {
            rebuilt_cache('all');
        }
        $_author = unserialize(file_get_contents($url));
    }

    return $_author;
}


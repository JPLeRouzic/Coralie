<?php

// Return popular posts lists
function popular_posts(int $count = 20) {
    $_views = array();
    $tmp = array();
    $posts = array();

    if (config('views.counter') == 'true') {
        // Get number of views
        $filename = 'content/data/views.json';
        if (file_exists($filename)) {
            $_views = json_decode(file_get_contents($filename), true);
            if (is_array($_views)) {
                /*
                 * array(959) { 
                    ["content/users/admin/blog/english/post/2020-03-27-19-12-45_alzheimer_disease.md"]=> int(997) 
                 * etc...
                 */
                
                // reverse sort, so most popular posts are the first
                arsort($_views);

                $tmp = tmp_from_key($_views, $count);

                $posts = get_posts($tmp, 1, $count);

                return $posts;
            } else {
                echo '<ul><li>No posts found in content/data/views.json</li></ul>';
            }
        } else {
            echo '<ul><li>Non existent content/data/views.json</li></ul>';
        }
    } else {
        echo '<ul><li>views.counter in config file is false</li></ul>';
    }
}

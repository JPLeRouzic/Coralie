<?php

// Return unpopular posts lists
function unpopular_posts(int $count = 100) {
    $_views = array();
    $tmp = array();
    $posts = array();

    if (config('views.counter') == 'true') {
        // Get number of views
        $filename = 'content/data/views.json';
        if (file_exists($filename)) {
            $_views = json_decode(file_get_contents($filename), true);
            if (is_array($_views)) {
                
                // Direct sort, so less popular posts are the first
                asort($_views);

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

function tmp_from_key(array $_views, int $count): array {
    $i = 1;
    $tmp = array();

    foreach ($_views as $key => $val) {
        if (file_exists($key)) {
            if (stripos($key, 'blog') !== false) {
                $tmp[] = pathinfo($key);
                if ($i++ >= $count) {
                    break;
                }
            }
        }
    }
    return $tmp ;
}

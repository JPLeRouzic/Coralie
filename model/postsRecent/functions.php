<?php

// Return recent posts lists
function recent_posts(int $count = null):array {
    if (empty($count)) {
        $count = config('recent.count');
        if (empty($count)) {
            $count = 5;
        }
    }
    $dir = "content/widget";
    $filename = "content/widget/recent.cache";
    $tmp = array();
    $posts = array();
    if (is_dir($dir) === false) {
        mkdir($dir, 0775, true);
    }
    if (file_exists($filename)) {
        $uno = file_get_contents($filename);
        $posts = unserialize($uno);
        
//        asort($posts) ;
                                                
        if (count($posts) != $count) {
            $posts = get_posts(null, 1, $count);
            $tmp = serialize($posts);
            file_put_contents($filename, print_r($tmp, true));
        }
    } else {
        $posts = get_posts(null, 1, $count);
        $tmp = serialize($posts);
        file_put_contents($filename, print_r($tmp, true));
    }

    /* We only show the recent posts in the category of the browser' user language *
    $lang = 'English';
    if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    }
    
    /* Remove recent posts not in browser' user language *
     * FIXME
     * $posto->catg->category  = 'English'
     * $lang ='en'
    foreach ($posts as $keyo => $posto) {
        if ( (stripos($posto->catg->category, $lang) === false) ) {
            var_dump($lang) ;
            unset($posts[$keyo]);
        }
    }
    */
    
    return $posts;
}

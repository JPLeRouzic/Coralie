<?php

// Get related posts base on post tag.
// Returns array of Tags
function get_related(Tag $tag, int $count = null): array {
    if (empty($count)) {
        $count = config('related.count');
        if (empty($count)) {
            $count = 3;
        }
    }
    $tags = get_tags($tag, 1, $count + 1, true);
    $posts = array();
    $req = urldecode($_SERVER['REQUEST_URI']);

    foreach ($tags as $tag) {
        $url = $tag->url;
        if (stripos($url, $req) === false) {
            $posts[] = $tag;
        }
    }
    $total = count($posts);
    if ($total >= 1) {
        $lang = 'English';
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        }
        $i = 1;
        echo '<ul>';

        foreach ($posts as $post) {

            /* We only show the related posts in the category of the browser' user language */
            if (stripos($post->catg->title, $lang) === false) {
                continue;
            }
            echo '<li><a href="' . $post->url . '">' . $post->title->value . '</a></li>';
            if ($i++ >= $count) {
                break;
            }
        }
        echo '</ul>';
    } else {
        echo '<ul><li>No related post found</li></ul>';
    }
    return $posts ;
}

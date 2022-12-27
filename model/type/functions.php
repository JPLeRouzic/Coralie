<?php  

// Return type page.
function get_type(string $type, int $page, int $perpage): array
{
    $posts = get_posts_sorted();

    $tmp = array();
    
    if (empty($perpage)) {
        $perpage = 10;    
    }

    foreach ($posts as $index => $v) {
    
        $filepath = $v['dirname'] . '/' . $v['basename'];

        // Extract the date
        $arr = explode('_', $filepath);

        // Replaced string
        $replaced = substr($arr[0], 0, strrpos($arr[0], '/')) . '/';

        // Author string
        $str = explode('/', $replaced);
        $tp = $str[count($str) - 2];
    
        if (strtolower($type) === strtolower($tp)) {
            $tmp[] = $v;
        }
    }

    if (empty($tmp)) {
        not_found('get_type');
    }
    
    $tmp = array_unique($tmp, SORT_REGULAR);

    return get_posts($tmp, $page, $perpage);
}


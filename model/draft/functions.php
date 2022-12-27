<?php  

// Return draft list
function get_draft(string $profile, int $page, int $perpage)
{

    $user = $_SESSION[config("site.url")]['user'];
    $role = user('role', $user);
    $posts = get_draft_posts();

    $tmp = array();

    foreach ($posts as $index => $v) {
        $str = explode('/', $v['dirname']);
        $author = $str[count($str) - 4];
        if (strtolower($profile) === strtolower($author) || $role === 'admin') {
            $tmp[] = $v;
        }
    }

    if (empty($tmp)) {
        return;
    }

    return get_posts($tmp, $page, $perpage);
}

// Return draft count. Matching $var and $str provided.
function get_draftcount($var)
{
    $posts = get_draft_posts();

    $tmp = array();

    foreach ($posts as $index => $v) {
    
         $filepath = $v['dirname'] . '/' . $v['basename'];

        // Extract the date
        $arr = explode('_', $filepath);

        // Replaced string
        $replaced = substr($arr[0], 0, strrpos($arr[0], '/')) . '/';

        // Author string
        $str = explode('/', $replaced);
        $cat = $str[count($str) - 3];
        
        if (stripos($cat, "$var") !== false) {
            $tmp[] = $v;
        }
    }

    return count($tmp);
}


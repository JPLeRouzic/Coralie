<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Show the author page
// https://padiracinnovation.org/News/author/admin
get('/author/:name', function($name) {
    authorpage_imp($name);
}
);

function authorpage_imp($name) {
    
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $url = site_url() . 'search/' . remove_accent($search);
        header("Location: $url");
    }

    if (!login()) {
        file_cache($_SERVER['REQUEST_URI']);
    }

    $page = from($_GET, 'page');
    $page = $page ? (int) $page : 1;
    $perpage = config('profile.perpage');

    $posts = get_profile_posts($name, $page, $perpage);

    $total = get_count($name, 'dirname');

    $author = get_author($name);

    if (isset($author[0])) {
        $author = $author[0];
    } else {
        $author = default_profile($name);
    }

    $vroot = rtrim(config('views.root'), '/');

    $lt = $vroot . '/layout--profile--' . strtolower($name) . '.html.php';
    $ls = $vroot . '/layout--profile.html.php';
    if (file_exists($lt)) {
        $layout = 'layout--profile--' . strtolower($name);
    } else if (file_exists($ls)) {
        $layout = 'layout--profile';
    } else {
        $layout = '';
    }

    $pv = $vroot . '/profile--' . strtolower($name) . '.html.php';
    if (file_exists($pv)) {
        $pview = 'profile--' . strtolower($name);
    } else {
        $pview = 'profile';
    }

    if (empty($posts) || $page < 1) {
        render($pview, array(
            'title' => 'Profile for:  ' . $author->name . ' - ' . blog_title(),
            'description' => 'Profile page and all posts by ' . $author->name . ' on ' . blog_title() . '.',
            'canonical' => site_url() . 'author/' . $name,
            'page' => $page,
            'posts' => null,
            'about' => $author->about,
            'name' => $author->name,
            'type' => 'is_profile',
            'bodyclass' => 'in-profile author-' . $name,
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Profile for: ' . $author->name,
            'pagination' => has_pagination($total, $perpage, $page),
            'is_profile' => true,
                ), $layout);
        die;
    }

    render($pview, array(
        'title' => 'Profile for:  ' . $author->name . ' - ' . blog_title(),
        'description' => 'Profile page and all posts by ' . $author->name . ' on ' . blog_title() . '.',
        'canonical' => site_url() . 'author/' . $name,
        'page' => $page,
        'posts' => $posts,
        'about' => $author->about,
        'name' => $author->name,
        'type' => 'is_profile',
        'bodyclass' => 'in-profile author-' . $name,
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Profile for: ' . $author->name,
        'pagination' => has_pagination($total, $perpage, $page),
        'is_profile' => true,
            ), $layout);
}

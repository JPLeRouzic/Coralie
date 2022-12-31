<?php

// The front page of the blog
ini_set('display_errors', 'On');
error_reporting(E_ALL);

// The front page of the blog which presents posts starting from last
// https://padiracinnovation.org/News/index
route('GET', '/index', function () {
    frontpage_imp();
}
);

// The front page of the blog which presents posts starting from last
// https://padiracinnovation.org/News/index
function frontpage_imp() {
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $url = site_url() . 'search/' . $search;
        header("Location: $url");
    }

    $vroot = rtrim(config('views.root'), '/');

    // For static pages
    if (config('static.frontpage') == 'true') {

        $front = get_frontpage();

        $tl = strip_tags(blog_tagline());

        if ($tl) {
            $tagline = ' - ' . $tl;
        } else {
            $tagline = '';
        }

        $pv = $vroot . '/static--front.html.php';
        if (file_exists($pv)) {
            $pview = 'static--front';
        } else {
            $pview = 'static';
        }

        render($pview, array(
            'title' => blog_title() . $tagline,
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'bodyclass' => 'in-front',
            'breadcrumb' => '',
            'p' => $front,
            'type' => 'is_frontpage',
            'is_front' => true,
        ));
    } else {
        // For non-static pages
        $page1 = from($_GET, 'page');
        $page = $page1 ? (int) $page1 : 1;
        $perpage = config('posts.perpage');

        $posts = get_posts(null, $page, $perpage);

        $total = count($posts);

        $tl = strip_tags(blog_tagline());

        if ($tl) {
            $tagline = ' - ' . $tl;
        } else {
            $tagline = '';
        }

        $pv = $vroot . '/main--front.html.php';
        if (file_exists($pv)) {
            $pview = 'main--front';
        } else {
            $pview = 'main';
        }

        if (empty($posts) || $page < 1) {

            // a non-existing page
            render('no-posts', array(
                'title' => blog_title() . $tagline,
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'bodyclass' => 'no-posts',
                'type' => 'is_frontpage',
                'is_front' => true,
            ));

            die;
        }

        /*
         * The front page of the blog which presents posts starting from last
         * It calls main.html.php (see $pview)
         */
        render($pview, array(
            'title' => blog_title() . $tagline,
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'page' => $page,
            'posts' => $posts,
            'bodyclass' => 'in-front',
            'breadcrumb' => '',
            'pagination' => has_pagination($total, $perpage, $page),
            'type' => 'is_frontpage',
            'is_front' => true,
        ));
    }
}

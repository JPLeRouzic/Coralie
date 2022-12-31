<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Show admin/trending 
route('GET', '/admin/trending', function() {
    get_admin_trending();
}
);

// Show admin/trending 
function get_admin_trending() {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    $user = $_SESSION[config("site.url")]['user'];
    $role = user('role', $user);
    if (is_logged()) {

        config('views.root', 'views/admin/views');
        if ( ($role === 'admin') || ($role === 'superadmin') ) {
            config('views.root', 'views/admin/views');
            $page1 = from($_GET, 'page');
            $page = $page1 ? (int) $page1 : 1;
            $perpage = 50;

            $posts = trending_posts($perpage);

            $total = count($posts);

            if (empty($posts) || $page < 1) {

                // a non-existing page or no posts found
                // It uses no-posts.html.php as template
                render('no-posts', array(
                    'title' => 'Trending posts - ' . blog_title(),
                    'description' => strip_tags(blog_description()),
                    'canonical' => site_url(),
                    'is_admin' => true,
                    'bodyclass' => 'admin-trending',
                ));
                die;
            }

            // Posts found, let's publish them
            $tl = strip_tags(blog_tagline());

            if ($tl) {
                $tagline = ' - ' . $tl;
            } else {
                $tagline = '';
            }

            // Usual case
            // It uses trending-posts.html.php as template
            render('trending-posts', array(
                'title' => 'Trending posts - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'heading' => 'Trending posts',
                'page' => $page,
                'posts' => $posts,
                'is_admin' => true,
                'bodyclass' => 'admin-trending',
                'breadcrumb' => '',
                'pagination' => has_pagination($total, $perpage, $page)
            ));
        } else {
            // if ($role != 'admin')
            // It uses denied.html.php as template
            render('denied', array(
                'title' => 'Trending posts - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '',
            ));
        }
    } else {
        // Not logged in
        $login = site_url() . 'login';
        header("location: $login");
    }
}


<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

/* * ** Admin *** */
// Show admin/posts 
https://csrf.4lima.de/News/admin/posts
route('GET', '/admin/posts', function() {
    get_admin_posts();
}
);

// Show admin/mine
route('GET', '/admin/mine', function() {
    get_admin_mine();
}
);

// Show admin/posts 
function get_admin_posts() {
    if (!isset($_SESSION)) {
        session_start();
    }
    
    $user = $_SESSION[config("site.url")]['user'];
    $role = user('role', $user);
    if (is_logged()) {

        config('views.root', 'views/admin/views');
        if ($role === 'admin') {

            config('views.root', 'views/admin/views');
            $page1 = from($_GET, 'page');
            $page = $page1 ? (int) $page1 : 1;
            $perpage = 20;

            $posts = get_posts(null, $page, $perpage);

            $total = count($posts);

            if (empty($posts) || $page < 1) {

                // a non-existing page
                render('no-posts', array(
                    'title' => 'All blog posts - ' . blog_title(),
                    'description' => strip_tags(blog_description()),
                    'canonical' => site_url(),
                    'bodyclass' => 'no-posts',
                ));

                die;
            }

            $tl = strip_tags(blog_tagline());

            if ($tl) {
                $tagline = ' - ' . $tl;
            } else {
                $tagline = '';
            }

            render('posts-list', array(
                'title' => 'All blog posts - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'heading' => 'All blog posts',
                'page' => $page,
                'posts' => $posts,
                'bodyclass' => 'all-posts',
                'type' => 'is_admin-posts',
                'is_admin' => true,
                'breadcrumb' => '',
                'pagination' => has_pagination($total, $perpage, $page)
            ));
        } else {
            render('denied', array(
                'title' => 'All blog posts - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'type' => 'is_admin-posts',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '',
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
}

// Show admin/mine
function get_admin_mine() {
    if (is_logged()) {

        config('views.root', 'views/admin/views');

        $name = $_SESSION[config("site.url")]['user'];

        $page1 = from($_GET, 'page');
        $page = $page1 ? (int) $page1 : 1;
        $perpage = config('profile.perpage');

        $posts = get_profile_posts($name, $page, $perpage);

        $total = get_count($name, 'dirname');

        $author = get_author($name);

        if (!isset($author)) {
            render('no-author', array(
                'title' => blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'bodyclass' => 'no-author',
                'type' => 'is_frontpage',
                'is_front' => false,
                    ));
            die;
        }

        if (empty($posts) || $page < 1) {
            render('user-posts', array(
                'title' => 'My blog posts - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'page' => $page,
                'heading' => 'My posts',
                'posts' => null,
                'about' => $author->about,
                'name' => $author->name,
                'type' => 'is_admin-mine',
                'is_admin' => true,
                'bodyclass' => 'admin-mine',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Profile for: ' . $author->name,
                'pagination' => has_pagination($total, $perpage, $page)
            ));
            die;
        }

        render('user-posts', array(
            'title' => 'My blog posts - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'heading' => 'My posts',
            'page' => $page,
            'posts' => $posts,
            'about' => $author->about,
            'name' => $author->name,
            'type' => 'is_admin-mine',
            'is_admin' => true,
            'bodyclass' => 'admin-mine',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Profile for: ' . $author->name,
            'pagination' => has_pagination($total, $perpage, $page)
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
}



<?php

// Show various page (top-level), admin, login, sitemap, static page.
function get_static($static) {

    if (isset($_GET['search'])) {
        $search = htmlentities($_GET['search']);
        $url = site_url() . 'search/' . $search;
        header("Location: $url");
    }

    if ($static === 'admin') {
    /*
     *  Admin page
     */
        if (is_logged()) {
            config('views.root', 'views/admin/views');
            render('main', array(
                'title' => 'Admin - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'bodyclass' => 'admin-front',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Admin'
            ));
        } else {
            $login = site_url() . 'login';
            header("location: $login");
        }
        die;
    } elseif ($static === 'login') {
        /*
         * Login page
         */
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        config('views.root', 'views/admin/views');
        render('login', array(
            'title' => 'Login - ' . blog_title(),
            'description' => 'Login page from ' . blog_title() . '.',
            'canonical' => site_url() . '/login',
            'bodyclass' => 'in-login',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Login'
        ));
        die;
    } elseif ($static === 'logout') {
        /*
         * Logout page
         */
        if (is_logged()) {
            config('views.root', 'views/admin/views');
            render('logout', array(
                'title' => 'Logout - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'bodyclass' => 'in-logout',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Logout'
            ));
        } else {
            $login = site_url() . 'login';
            header("location: $login");
        }
        die;
    } elseif ($static === 'front') {

        $redir = site_url();
        header("location: $redir", TRUE, 301);
    } else {

        $post1 = get_static_post($static);

        if (!$post1) {
            not_found('73, not found : ' . $static);
        }

        $post = $post1[0];

        if (config("views.counter") == "true") {
            add_view($post->file);
        }

        $vroot = rtrim(config('views.root'), '/');

        $pv = $vroot . '/static--' . strtolower($static) . '.html.php';
        if (file_exists($pv)) {
            $pview = 'static--' . strtolower($static);
        } else {
            $pview = 'static';
        }

        render($pview, array(
            'title' => $post->title->value . ' - ' . blog_title(),
            'description' => $post->description,
            'canonical' => $post->url,
            'bodyclass' => 'in-page ' . strtolower($static),
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . $post->title->value,
            'p' => $post,
            'type' => 'staticPage',
            'is_page' => true,
                ));
    }
}

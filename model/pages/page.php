<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Show the static add page (GET)
// https://padiracinnovation.org/News/add/page
route('GET', '/add/page', function () {
    get_add_page();
}
);

// Submitted static add page data (POST)
route('POST', '/add/page', function () {
    post_add_page();
}
);

// Show the static add page
function get_add_page() {
    if (is_logged()) {

        config('views.root', 'views/admin/views');
        $user = $_SESSION[config("site.url")]['user'];

        render('add-page', array(
            'title' => 'Add page - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_page',
            'is_admin' => true,
            'author' => $user,
            'bodyclass' => 'add-page',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add page'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
}

// Submitted static add page data
function post_add_page() {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    $user = $_SESSION[config("site.url")]['user'];

    $title = from($_REQUEST, 'title');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $description = from($_REQUEST, 'description');
    if ($proper && !empty($title) && !empty($content) && is_logged()) {
        if (!empty($url)) {
            add_page(new Title($title), $url, $content, new Desc($description));
        } else {
            $url = $title;
            add_page(new Title($title), $url, $content, new Desc($description));
        }
    } else {
        $message['error'] = '';
        if (empty($title)) {
            $message['error'] .= '<li>Title field is required.</li>';
        }
        if (empty($content)) {
            $message['error'] .= '<li>Content field is required.</li>';
        }
        if (!$proper) {
            $message['error'] .= '<li>CSRF Token not correct.</li>';
        }
        config('views.root', 'views/admin/views');
        render('add-page', array(
            'title' => 'Add page - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'postTitle' => $title,
            'postUrl' => $url,
            'postContent' => $content,
            'type' => 'is_page',
            'is_admin' => true,
            'author' => $user,
            'bodyclass' => 'add-page',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add page'
        ));
    }
}

<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Edit the profile
route('GET', '/edit/profile', function() {
    get_edit_profile();
}
);

// Get submitted data from edit profile page
route('POST', '/edit/profile', function() {
    post_edit_profile();
}
);

// Edit the profile
function get_edit_profile() {
    if (is_logged()) {

        config('views.root', 'views/admin/views');
        render('edit-profile', array(
            'title' => 'Edit profile - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_profile',
            'is_admin' => true,
            'bodyclass' => 'edit-profile',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Edit profile',
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
}

// Get submitted data from edit profile page
function post_edit_profile() {
    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    $user = $_SESSION[config("site.url")]['user'];
    $title = from($_REQUEST, 'title');
    $content = from($_REQUEST, 'content');
    if ($proper && !empty($title) && !empty($content)) {
        edit_profile(new Title($title), $content, $user);
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

        render('edit-profile', array(
            'title' => 'Edit profile - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'postTitle' => $title,
            'postContent' => $content,
            'type' => 'is_profile',
            'is_admin' => true,
            'bodyclass' => 'edit-profile',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Edit profile'
        ));
    }
}



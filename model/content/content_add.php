<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Show the "Add post" page (GET), in the example the type of content is 'post'
// https://padiracinnovation.org/News/add/content?type=post
route('GET', '/add/content', function () {
    get_add_content();
});

// Submitted add post data (POST)
// https://padiracinnovation.org/News/add/content?type=post
route('POST', '/add/content', function () {
    post_add_content();
});

// Show the "Add post" page (GET), in the example the type of content is 'post'
// https://padiracinnovation.org/News/add/content?type=post
function get_add_content() {
    $req = $_GET['type'];

    $type = 'is_' . $req;

    if (is_logged()) {

        config('views.root', 'views/admin/views');

        render('add-content', array(
            'title' => 'Add content - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => $type,
            'is_admin' => true,
            'bodyclass' => 'add-content',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add post'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
}

// Submitted add post data (POST)
// https://padiracinnovation.org/News/add/content?type=post
function post_add_content() {
    $is_image = from($_REQUEST, 'is_science_ed');
    $is_post = from($_REQUEST, 'is_post');

    $image = from($_REQUEST, 'image');

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    $title = from($_REQUEST, 'title');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $description = from($_REQUEST, 'description');
    $user = $_SESSION[config("site.url")]['user'];
    $draft = from($_REQUEST, 'draft');
    $category = from($_REQUEST, 'category');
    $tag = from($_REQUEST, 'tag');

    if (empty($is_post) && empty($is_image)) {
        $add = site_url() . 'admin/content';
        header("location: $add");
    }

    if (!empty($is_post)) {
        if ($proper && !empty($title) && !empty($tag) && !empty($content)) {
            if (empty($url)) {
                $url = $title;
            }
            add_content(new Title($title), new Tag($tag), $url, $content, $user,
                    $description, $draft, $category, new Type('post'), '');
        } else {
            $message['error'] = '';
            if (empty($title)) {
                $message['error'] .= '<li>Title field is required.</li>';
            }
            if (empty($tag)) {
                $message['error'] .= '<li>Tag field is required.</li>';
            }
            if (empty($content)) {
                $message['error'] .= '<li>Content field is required.</li>';
            }
            if (!$proper) {
                $message['error'] .= '<li>CSRF Token not correct.</li>';
            }
            config('views.root', 'views/admin/views');
            render('add-content', array(
                'title' => 'Add post- ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'error' => '<ul>' . $message['error'] . '</ul>',
                'postTitle' => $title,
                'postTag' => $tag,
                'postUrl' => $url,
                'postContent' => $content,
                'type' => 'is_post',
                'is_admin' => true,
                'author' => $user,
                'bodyclass' => 'add-post',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add post'
            ));
        }
    }

    if (!empty($is_image)) {
        if ($proper && !empty($title) && !empty($tag) && !empty($content) && !empty($image)) {
            if (empty($url)) {
                $url = $title;
            }
            add_content(new Title($title), new Tag($tag), $url, $content, $user,
                    $description, $draft, $category, new Type('post'), '');
        } else {
            $message['error'] = '';
            if (empty($title)) {
                $message['error'] .= '<li>Title field is required.</li>';
            }
            if (empty($tag)) {
                $message['error'] .= '<li>Tag field is required.</li>';
            }
            if (empty($content)) {
                $message['error'] .= '<li>Content field is required.</li>';
            }
            if (empty($image)) {
                $message['error'] .= '<li>Image field is required.</li>';
            }
            if (!$proper) {
                $message['error'] .= '<li>CSRF Token not correct.</li>';
            }
            config('views.root', 'views/admin/views');
            render('add-content', array(
                'title' => 'Add image - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'error' => '<ul>' . $message['error'] . '</ul>',
                'postTitle' => $title,
                'postImage' => $image,
                'postTag' => $tag,
                'postUrl' => $url,
                'postContent' => $content,
                'type' => 'is_science_ed',
                'is_admin' => true,
                'author' => $user,
                'bodyclass' => 'add-image',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add image'
            ));
        }
    }
}

<?php

// Show various page (top-level), admin, login, sitemap, static page.
ini_set('display_errors', 'On');
error_reporting(E_ALL);

/* * ** static pages *** */
// Show various page (top-level), admin, login, sitemap, static page.
route('GET', '/:static', function ($static) {
    get_static($static);
}
);

// Show the add sub static page
route('GET', '/:static/add', function ($static) {
    get_static_add($static);
}
);

// Submitted data from add sub static page
route('POST', '/:static/add', function ($static) {
    post_static_add($static);
}
);

// Show edit the static page
route('GET', '/:static/edit', function ($static) {
    get_static_edit($static);
}
);

// Get edited data from static page
route('POST', '/:static/edit', function () {
    post_static_edit();
}
);

// Deleted the static page
route('GET', '/:static/delete', function ($static) {
    get_static_delete($static);
}
);

// Get deleted data for static page
route('POST', '/:static/delete', function () {
    post_static_delete();
}
);

// Show the sb static page
route('GET', '/:static/:sub', function ($static, $sub) {
    get_static_sub($static, $sub);
}
);

// Edit the sub static page
route('GET', '/:static/:sub/edit', function ($static, $sub) {
    get_static_sub_edit($static, $sub);
}
);

// Submitted data from edit sub static page
route('POST', '/:static/:sub/edit', function ($static, $sub) {
    post_static_sub_edit($static, $sub);
}
);

// Delete sub static page
route('GET', '/:static/:sub/delete', function ($static, $sub) {
    get_static_sub_delete($static, $sub);
}
);

// Submitted data from delete sub static page
route('POST', '/:static/:sub/delete', function () {
    post_static_sub_delete();
}
);

// Show the add sub static page
function get_static_add(string $static) {
    if (is_logged()) {

        config('views.root', 'views/admin/views');

        $post1 = get_static_post($static);

        if (!$post1) {
            not_found('get_static_add 89');
        }

        $post = $post1[0];

        render('add-page', array(
            'title' => 'Add page - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_page',
            'is_admin' => true,
            'bodyclass' => 'add-page',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; <a href="' . $post->url . '">' . $post->value . '</a> Add page'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
}

// Submitted data from add sub static page
function post_static_add(string $static) {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    $title = from($_REQUEST, 'title');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $description = from($_REQUEST, 'description');
    if ($proper && !empty($title) && !empty($content) && is_logged()) {
        if (!empty($url)) {
            add_sub_page(new Title($title), $url, $content, new Desc($description));
        } else {
            $url = $title;
            add_sub_page(new Title($title), $url, $content, new Desc($description));
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
            'bodyclass' => 'add-page',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; <a href="' . $title . '">' . $title . '</a> Add page'
        ));
    }
}

// Show edit the static page
function get_static_edit(string $static) {

    if (is_logged()) {

        config('views.root', 'views/admin/views');
        $post1 = get_static_post($static);

        if (!$post1) {
            not_found('get_static_edit 162');
        }

        $post = $post1[0];

        render('edit-page', array(
            'title' => 'Edit page - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'bodyclass' => 'edit-page',
            'is_admin' => true,
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . $post->title->value,
            'p' => $post,
            'type' => 'staticPage',
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
}

// Get edited data from static page
function post_static_edit() {
    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    if (!is_logged()) {
        $login = site_url() . 'login';
        header("location: $login");
    }

    $title = from($_REQUEST, 'title');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $oldfile = from($_REQUEST, 'oldfile');
    $destination = from($_GET, 'destination');
    $description = from($_REQUEST, 'description');
    if ($proper && !empty($title) && !empty($content)) {
        if (!empty($url)) {
            edit_page(new Title($title), $url, $content, $oldfile, $destination, new Desc($description));
        } else {
            $url = $title;
            edit_page(new Title($title), $url, $content, $oldfile, $destination, new Desc($description));
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

        render('edit-page', array(
            'title' => 'Edit page - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'oldfile' => $oldfile,
            'postTitle' => $title,
            'postUrl' => $url,
            'postContent' => $content,
            'bodyclass' => 'edit-page',
            'is_admin' => true,
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Edit page'
        ));
    }
}

// Deleted the static page
function get_static_delete(string $static) {

    if (is_logged()) {

        config('views.root', 'views/admin/views');
        $post1 = get_static_post($static);

        if (!$post1) {
            not_found('get_static_delete 243');
        }

        $post = $post1[0];

        render('delete-page', array(
            'title' => 'Delete page - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'bodyclass' => 'delete-page',
            'is_admin' => true,
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . $post->value,
            'p' => $post,
            'type' => 'staticPage',
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
}

// Get deleted data for static page
function post_static_delete() {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if ($proper && is_logged()) {
        $file = from($_REQUEST, 'file');
        $destination = from($_GET, 'destination');
        delete_page($file, $destination);
    }
}

// Show the sb static page
function get_static_sub(string $static, string $sub) {

    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $url = site_url() . 'search/' . $search;
        header("Location: $url");
    }

    $father_post = get_static_post($static);
    if (!$father_post) {
        not_found('get_static_sub 286');
    }
    $post1 = get_static_sub_post($static, $sub);
    if (!$post1) {
        not_found('get_static_sub 290');
    }
    $post = $post1[0];

    if (config("views.counter") == "true") {
        add_view($post->file);
    }

    $vroot = rtrim(config('views.root'), '/');

    $pv = $vroot . '/static--' . strtolower($static) . '--' . strtolower($sub) . '.html.php';
    $ps = $vroot . '/static--' . strtolower($static) . '.html.php';
    if (file_exists($pv)) {
        $pview = 'static--' . strtolower($static) . '--' . strtolower($sub);
    } else if (file_exists($ps)) {
        $pview = 'static--' . strtolower($static);
    } else {
        $pview = 'static';
    }

    render($pview, array(
        'title' => $post->value . ' - ' . blog_title(),
        'description' => $post->description,
        'canonical' => $post->url,
        'bodyclass' => 'in-page ' . strtolower($static) . ' ' . strtolower($sub),
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; <a href="' . $father_post[0]->url . '">' . $father_post[0]->value . '</a> &#187; ' . $post->value,
        'p' => $post,
        'type' => 'subPage',
        'is_subpage' => true,
            ));
}

// Edit the sub static page
function get_static_sub_edit(string $static, string $sub) {

    if (is_logged()) {

        config('views.root', 'views/admin/views');
        $post1 = get_static_post($static);

        if (!$post1) {
            not_found('get_static_sub_edit 344');
        }

        $post = $post1[0];

        $page1 = get_static_sub_post($static, $sub);

        if (!$page1) {
            not_found('get_static_sub_edit 352');
        }

        $page = $page1[0];

        render('edit-page', array(
            'title' => 'Edit page - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'bodyclass' => 'edit-page',
            'is_admin' => true,
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; <a href="' . $post->url . '">' . $post->value . '</a> &#187; ',
            'p' => $page,
            'type' => 'subPage',
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
}

// Submitted data from edit sub static page
function post_static_sub_edit(string $static, string $sub) {
    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    if (!is_logged()) {
        $login = site_url() . 'login';
        header("location: $login");
    }

    $title = from($_REQUEST, 'title');
    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $oldfile = from($_REQUEST, 'oldfile');
    $destination = from($_GET, 'destination');
    $description = from($_REQUEST, 'description');
    if ($destination === null) {
        $destination = $static . "/" . $sub;
    }
    if ($proper && !empty($title) && !empty($content)) {
        if (!empty($url)) {
            edit_page(new Title($title), $url, $content, $oldfile, $destination, new Desc($description), $static);
        } else {
            $url = $title;
            edit_page(new Title($title), $url, $content, $oldfile, $destination, new Desc($description), $static);
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

        render('edit-page', array(
            'title' => 'Edit page - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'oldfile' => $oldfile,
            'postTitle' => $title,
            'postUrl' => $url,
            'postContent' => $content,
            'static' => $static,
            'sub' => $sub,
            'bodyclass' => 'edit-page',
            'is_admin' => true,
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Edit page'
        ));
    }
}

// Delete sub static page
function get_static_sub_delete(string $static, string $sub) {

    if (is_logged()) {

        config('views.root', 'views/admin/views');
        $post1 = get_static_post($static);

        if (!$post1) {
            not_found('get_static_sub_delete 438');
        }

        $post = $post1[0];

        $page1 = get_static_sub_post($static, $sub);

        if (!$page1) {
            not_found('get_static_sub_delete 446');
        }

        $page = $page1[0];

        render('delete-page', array(
            'title' => 'Delete page - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'bodyclass' => 'delete-page',
            'is_admin' => true,
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; <a href="' . $post->url . '">' . $post->value . '</a>' . $page->value,
            'p' => $page,
            'type' => 'subPage',
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
}

// Submitted data from delete sub static page
function post_static_sub_delete() {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if ($proper && is_logged()) {
        $file = from($_REQUEST, 'file');
        $destination = from($_GET, 'destination');
        delete_page($file, $destination);
    }
}

<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

/* * ** Categories *** */
// Show the page to fill in with new category information
// https://padiracinnovation.org/News/add/category
route('GET', '/add/category', function () {
    add_category_get();
}
);

// Submitted add category (process POST)
// https://padiracinnovation.org/News/add/category
route('POST', '/add/category', function () {
    add_category_post();
}
);

// Display page showing all categories
// https://padiracinnovation.org/News/admin/categories
route('GET', '/admin/categories', function () {
    get_all_categories();
}
);

// List the posts with this category
// https://padiracinnovation.org/News/category/english/
route('GET', '/category/:category', function ($category) {
    show_posts_in_category($category);
}
);

// Edit the information for this category 
// https://padiracinnovation.org/News/category/english/edit
route('GET', '/category/:category/edit', function ($category) {
    edit_category_get($category);
}
);

// Process the information for this category 
// https://padiracinnovation.org/News/category/english/edit
route('POST', '/category/:category/edit', function () {
    edit_category_post();
}
);

// Delete category
// /News/category/english/delete?destination=admin/categories
route('GET', '/category/:category/delete', function ($category) {
    category_delete_get($category);
}
);

// POST deleted category data
route('POST', '/category/:category/delete', function () {
    category_delete_post();
}
);

// Show the page to fill in with new category information (GET)
// https://padiracinnovation.org/News/add/category
function add_category_get() {
    if (is_logged()) {

        config('views.root', 'views/admin/views');

        render('add-page', array(
            'title' => 'Add category - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_category',
            'is_admin' => true,
            'bodyclass' => 'add-category',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add page'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
}

// Process the information about this new category (POST)
// https://padiracinnovation.org/News/add/category
function add_category_post() {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    $title = from($_REQUEST, 'title');
    $url = from($_REQUEST, 'url');
    $content = '' ; // No content for categories
    $description = from($_REQUEST, 'description');
    if ($proper && !empty($title) && is_logged()) {
        // Everything looks OK, then let's create this category
        if (empty($url)) {
            $url = $title;
        }
        add_category($title, $url, $content, $description);
    } else {
        // Something is wrong
        $message['error'] = '';
        if (empty($title)) {
            $message['error'] .= '<li>Title field is required.</li>';
        }
        if ($content === '') {
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
            'type' => 'is_category',
            'is_admin' => true,
            'bodyclass' => 'add-category',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add page'
        ));
    }
}

// Display page showing all categories
// https://padiracinnovation.org/News/admin/categories
function get_all_categories() {
    if (!isset($_SESSION)) {
        session_start();
    }

    if (is_logged()) {
        config('views.root', 'views/admin/views');
        render('categories', array(
            'title' => 'Categories - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_admin-categories',
            'is_admin' => true,
            'bodyclass' => 'admin-categories',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Categories'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
}

// Show a page with the posts in this category
// https://padiracinnovation.org/News/category/english/
function show_posts_in_category(string $category) {

    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $url = site_url() . 'search/' . $search;
        header("Location: $url");
    }

    $page1 = from($_GET, 'page');
    $page = $page1 ? (int) $page1 : 1;
    $perpage = config('category.perpage');

    if (empty($perpage)) {
        $perpage = 10;
    }

    // Get the posts in this category
    $posts = get_posts_in_category($category, $page, $perpage);

    $total = get_categorycount($category);

    if (empty($posts) || $page < 1) {
        // a non-existing page
        not_found('show_posts_in_category 179');
    }

    $catgry = get_category_info($category);
    /*
     * object(Category)#142 (6) { 
     *     ["category"]=> uninitialized(string) 
     *     ["categoryb"]=> uninitialized(string) 
     *     ["url"]=> string(22) "/News/category/english" 
     *     ["md"]=> string(7) "english" 
     *     ["file"]=> string(32) "content/data/category/english.md" 
     *     ["title"]=> string(7) "English" 
     *     ["body"]=> string(28) " Articles written in English" 
     *     ["description"]=> string(28) "my description" 
     * }
     */

    render('layout', array(
        'title' => $catgry->title . ' - ' . blog_title(),
        'description' => $catgry->description,
        'canonical' => $catgry->url,
        'page' => $page,
        'posts' => $posts,
        'category' => $catgry->title,
        'bodyclass' => 'in-category category-' . strtolower($category),
        'breadcrumb' => get_breadcrumb_cat($catgry),
        'pagination' => has_pagination($total, $perpage, $page),
        'is_category' => true,
            ) );
}

// Edit the information for this category 
// https://padiracinnovation.org/News/category/english/edit
function edit_category_get(string $category) {

    if (is_logged()) {

        config('views.root', 'views/admin/views');
        $catgr = get_category_info($category);

        render('edit-category', array(
            'title' => 'Edit category - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_category',
            'is_admin' => true,
            'bodyclass' => 'edit-category',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . $catgr->url,
            'p' => $catgr
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
}

// Edit the information for this category 
// https://padiracinnovation.org/News/category/english/edit
function edit_category_post() {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    if (!is_logged()) {
        $login = site_url() . 'login';
        header("location: $login");
    }

    $title = from($_REQUEST, 'title');
    $url = from($_REQUEST, 'url');
    $content = '' ; // No content for categories
    $oldfile = from($_REQUEST, 'oldfile');
    $destination = from($_GET, 'destination');
    $description = from($_REQUEST, 'description');
    if ($proper && !empty($title)) {
        if (empty($url)) {
            $url = $title;
        }
//         echo '<br>description: ' . $description ;
        edit_category(new Title($title), $url, $content, $oldfile, $destination, new Desc($description) );
    } else {
        $message['error'] = '';
        if (empty($title)) {
            $message['error'] .= '<li>Title field is required.</li>';
        }
        if ($content === '') {
            $message['error'] .= '<li>1 Content field is required.</li>';
        }
        if (!$proper) {
            $message['error'] .= '<li>CSRF Token not correct.</li>';
        }
        config('views.root', 'views/admin/views');

        render('edit-category', array(
            'title' => 'Edit category - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'oldfile' => $oldfile,
            'postTitle' => $title,
            'postUrl' => $url,
            'postContent' => $content,
            'type' => 'is_category',
            'is_admin' => true,
            'bodyclass' => 'edit-category',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Edit category'
        ));
    }
}

// Delete category (GET)
// /News/category/english/delete?destination=admin/categories
function category_delete_get(string $category) {

    if (is_logged()) {

        config('views.root', 'views/admin/views');
        $catgry = get_category_info($category);

        render('delete-category', array(
            'title' => 'Delete category - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_category',
            'is_admin' => true,
            'bodyclass' => 'delete-category',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . $catgry->title,
            'p' => $catgry,
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
}

// Delete category (POST)
// /News/category/english/delete?destination=admin/categories
function category_delete_post() {
    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if ($proper && is_logged()) {
        $file = from($_REQUEST, 'file');
        $destination = from($_GET, 'destination');
        delete_page($file, $destination);
    }
}

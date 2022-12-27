<?php

// Show the "Add content" page where one can choose the type of post they want to create
// https://padiracinnovation.org/News/admin/content
route('GET', '/admin/content', function () {
    get_admin_content();
});

// Show admin/content
function get_admin_content() {
    if (is_logged()) {
        config('views.root', 'views/admin/views');
        $user = $_SESSION[config("site.url")]['user'];
        render('content-type', array(
            'title' => 'Add content - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_admin-content',
            'is_admin' => true,
            'author' => $user,
            'bodyclass' => 'admin-content',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add content'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
}

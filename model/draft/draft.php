<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Show admin/draft
// https://padiracinnovation.org/News/admin/draft
route('GET', '/admin/draft', function () {
    get_admin_draft();
}
);

// Show admin/draft
// https://padiracinnovation.org/News/admin/draft
function get_admin_draft() {
    if (is_logged()) {

        config('views.root', 'views/admin/views');

        $name = $_SESSION[config("site.url")]['user'];

        $page1 = from($_GET, 'page');
        $page = $page1 ? (int) $page1 : 1;
        $perpage = config('profile.perpage');

        $posts = get_draft($name, $page, $perpage);

        $total = get_count($name, 'dirname');

        $author = get_author($name);

        if (!isset($author)) {
            $author = default_profile($name);
        }

        if (empty($posts) || $page < 1) {
            render('user-draft', array(
                'title' => 'My draft - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'page' => $page,
                'heading' => 'My draft',
                'posts' => null,
                'about' => $author->about,
                'name' => $author->name,
                'type' => 'is_admin-draft',
                'is_admin' => true,
                'bodyclass' => 'admin-draft',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Draft for: ' . $author->name,
                'pagination' => has_pagination($total, $perpage, $page)
            ));
            die;
        }

        render('user-draft', array(
            'title' => 'My draft - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'heading' => 'My draft',
            'page' => $page,
            'posts' => $posts,
            'about' => $author->about,
            'name' => $author->name,
            'type' => 'is_admin-draft',
            'is_admin' => true,
            'bodyclass' => 'admin-draft',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Draft for: ' . $author->name,
            'pagination' => has_pagination($total, $perpage, $page)
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
}

// Get user draft.
function get_draft_posts() {
    static $_draft = array();
    if (empty($_draft)) {
// ex: /default-website/News/content/users/admin/blog/english/draft
        $tmp = glob('content/users/*/*/*/draft/*.md', GLOB_NOSORT);
        if (is_array($tmp)) {
            foreach ($tmp as $file) {
                $_draft[] = pathinfo($file);
            }
        }
        usort($_draft, "sortfile");
    }
    return $_draft;
}

// Find draft.
function find_draft($name) {
    $posts = get_draft_posts();

    foreach ($posts as $index => $v) {
        $arr = explode('_', $v['basename']);
        if (
                strtolower($arr[1]) === strtolower($name . '.md') ||
                strtolower($arr[2]) === strtolower($name . '.md')
        ) {
            find_draft_aux($posts, $index);
        }
    }
}

// Find draft by year/month/name.
function find_draft_full($year, $month, $name) {
    $posts = get_draft_posts();
    foreach ($posts as $index => $v) {
        $arr = explode('_', $v['basename']);
        /*
          array(3) {
          [0]=> string(19) "2022-08-28-11-27-50"
          [1]=> string(7) "medRxiv"
          [2]=> string(24) "2022-04-29-22274496v1.md"
          }
         */
        $uno = strpos($arr[0], "$year-$month") ;
        if ($uno !== false &&
                (
                strtolower($arr[1]) === strtolower($name . '.md') ||
                strtolower($arr[2]) === strtolower($name . '.md')
                )
        ) {
            return find_draft_aux($posts, $index);
        }
    }
}

function find_draft_aux(&$posts, &$index) {

    // Use the get_posts method to return
    // a properly parsed object

    $ar = get_posts($posts, $index + 1, 1);
    $pr = get_posts($posts, $index, 1);
    $nx = get_posts($posts, $index + 2, 1);
    
    if ($index == 0) {
        if (isset($pr[0])) {
            return array(
                'current' => $ar[0],
                'prev' => $pr[0]
            );
        } else {
            return array(
                'current' => $ar[0],
                'prev' => null
            );
        }
    } elseif (count($posts) == $index + 1) {
        return array(
            'current' => $ar[0],
            'next' => $nx[0]
        );
    } else {
        return array(
            'current' => $ar[0],
            'next' => $nx[0],
            'prev' => $pr[0]
        );
    }
}

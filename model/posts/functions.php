<?php

/*
 * Get blog post path. Unsorted. Mostly used on widget.
 * 
 * Search Form
 * Own Search Form	 
 * Menu	
 * Recent Posts	
 * Custom Recent Posts	
 * Popular Posts
 * Archive
 * Tag Cloud
 * Custom Tag Cloud
 * Category List	
 * Related Posts
 * Custom Related Posts
 * Recent Posts by Type
 * 
 * Returns:
 * array(2) { 
 * ["basename"]=> string(38) "2021-11-30-18-30-13_pubmed_34745218.md"  
 * ["dirname"]=> string(37) "content/users/admin/blog/english/post" 
 * }
 */

function get_post_unsorted(): array {
    $sorted1 = array();

    // content/users/admin/blog/english/post
    // files unsorted
    $sorted1 = glob('content/users/*/blog/*/post/*.md', GLOB_NOSORT);

    $_sorted = prepare_v($sorted1);

    return $_sorted;
}

/*
 *  Get blog posts with more info about the path. Sorted by filename.
 * 
 * returns an array of:
  ' <!--t The title of the post. t-->
  <!--d The description of the post.  d-->
  <!--tag The tags of the post. tag-->
  The rest is the content'
 * 
 * This function must return a 'normal' array, not an associative one
 */

function get_posts_sorted(): array {
    $sorted1 = array();

    // content/users/admin/blog/english/post
    $sorted1 = glob('content/users/*/blog/*/post/*.md', GLOB_NOSORT);

    $unsorted = prepare_v($sorted1);

// Sort the files
    arsort($unsorted);

    return $unsorted;
}

/*
 * Create an array of date/dirname/basename array
 * array(2) { 
 * ["basename"]=> string(38) "2022-08-20-20-09-36_system_35983834.md" 
 * ["dirname"]=> string(37) "content/users/admin/blog/english/post" 
 * }
 * This function must return a 'normal' array, not an associative one
 */

function prepare_v(array $sorted1): array {
    $v = array();
    $_sorted = array();

    $indx = 0;
    foreach ($sorted1 as $filename) {

// Fill in the basename
        $tmparray = explode('/', $filename);
        $count1 = count($tmparray) - 1; // The location of basename (xxxx.md)
        $v['basename'] = $tmparray[$count1];

// Remove the basename from the filepath
        unset($tmparray[$count1]);

// Fill the rest of the filepath in the dirname
        $tmppath = implode('/', $tmparray);
        $v['dirname'] = $tmppath;

// Fill in the date
        $tmpdate = explode('_', $v['basename']);
        $v['date'] = $tmpdate[0];

// Use the date as the key of the array
        $_sorted[$indx] = $v;
        $indx++;
    }
    return $_sorted;
}

// Find current post by year, month and name, previous, and next.
// Return an array of posts (previous, current, next)
function find_post_name(string $name): array|null {
    $posts = get_posts_sorted();

    foreach ($posts as $index => $v) {

        $arr = explode('_', $v['basename']);
        /*
         * array(3) { 
         * [0]=> string(19) "2022-11-23-18-53-32" 
         * [1]=> string(9) "alzheimer" 
         * [2]=> string(97) "synthesis-of-andrographolide-triazolyl-alzheimers-disease.md" }
         */
        if (
                isset($arr[2]) && (
                //strtolower($arr[1]) === strtolower($name . '.md') ||
                strtolower($arr[2]) === strtolower($name . '.md')
                )
        ) {
            return supressit($posts, $index);
        }
    }
    return null;
}

function find_posts(string $year, string $month, string $name): array|null {
    $posts = get_posts_sorted();

    foreach ($posts as $index => $v) {

        $arr = explode('_', $v['basename']);
        /*
         * array(3) { 
         * [0]=> string(19) "2022-11-23-18-53-32" 
         * [1]=> string(9) "alzheimer" 
         * [2]=> string(97) "synthesis-of-andrographolide-triazolyl-alzheimers-disease.md" }
         */
        if (
                isset($arr[2]) &&
                (strpos($arr[0], "$year-$month") !== false && (
//strtolower($arr[1]) === strtolower($name . '.md') ||
                strtolower($arr[2]) === strtolower($name . '.md')
                )
                )
        ) {
            return supressit($posts, $index);
        }
    }
    return null;
}

function supressit(&$posts, &$index): array {

// We found our post
// Use the get_post method to return a properly parsed object
// It returns one Post object which is at $index in the array of string $posts[] 
    $arV = $posts[$index]; // current post
    $ar = get_post($arV);

    // Prepare $pr and $nx
    $pr = new Post();
    $nx = new Post();
//
// Getting the keys of $arr using array_keys() function
    $keys = array_keys($posts);
    $size = count($posts);
    for ($x = 0; $x < $size; $x++) {
        if ($keys[$x] == $index) {
            if (isset($keys[$x + 1])) {
                $nxV = $posts[$keys[$x + 1]]; // Next post
                $nx = get_post($nxV);
            }
            if (isset($keys[$x - 1])) {
                $prV = $posts[$keys[$x - 1]]; // Previous post
                $pr = get_post($prV);
            }
            break;
        }
    }

    /*
     * array(3) { 
     * ["basename"]=> string(191) "2022-11-20-18-43-17_parkinson_effects-of-trihexyphenidyl.md" 
     * ["dirname"]=> string(37) "content/users/admin/blog/english/post" 
     * ["date"]=> string(19) "2022-11-20-18-43-17" }
     */

    return array(
        'current' => $ar,
        'next' => $nx,
        'prev' => $pr
    );
}

// Return static page.
function get_static_post(string $static): array {
    $posts = get_static_pages();

    $tmp = array();

    if (!empty($posts)) {

        foreach ($posts as $index => $v) {
            if (stripos($v, $static . '.md') !== false) {

                $post = new Post();

// Replaced string
                $replaced = substr($v, 0, strrpos($v, '/')) . '/';

// The static page URL
                $url = str_replace($replaced, '', $v);
                $post->url = site_url() . str_replace('.md', '', $url);

                $post->file = $v;

// Get the contents and convert it to HTML
                $content = file_get_contents($v);

// Extract the title and body
                $post->title = new Title(get_content_tag('t', $content, $static));

// Get the contents and convert it to HTML
                $post->body = MarkdownExtra::defaultTransform(remove_html_comments($content));

                if (config('views.counter') == 'true') {
                    $post->views = get_views($post->file);
                }

                $uno = get_description($content);
                $post->description->value = get_content_tag("d", $uno->value);

                $tmp[] = $post;
            }
        }
    }

    return $tmp;
}

// Return static page.
function get_static_sub_post($static, $sub_static) {
    $posts = get_static_sub_pages($static);

    $tmp = array();

    if (!empty($posts)) {

        foreach ($posts as $index => $v) {
            if (stripos($v, $sub_static . '.md') !== false) {

                $post = new Post();

// Replaced string
                $replaced = substr($v, 0, strrpos($v, '/')) . '/';

// The static page URL
                $url = str_replace($replaced, '', $v);
                $post->url = site_url() . $static . "/" . str_replace('.md', '', $url);

                $post->file = $v;

// Get the contents and convert it to HTML
                $content = file_get_contents($v);

// Extract the title and body
                $post->title = new Title(get_content_tag('t', $content, $sub_static));

// Get the contents and convert it to HTML
                $post->body = MarkdownExtra::defaultTransform(remove_html_comments($content));

                $post->views = get_views($post->file);

                $post->description = new Desc(get_content_tag("d", $content, get_description($post->description->value)));
// expected Desc, string provided
                $tmp[] = $post;
            }
        }
    }

    return $tmp;
}

// Return post count. Matching $var and $str provided.
function get_count(string $var, string $str): int {
    $posts = get_posts_sorted();

    $tmp = array();

    foreach ($posts as $index => $v) {
        $arr = explode('_', $v[$str]);
        $url = $arr[0];
        if (stripos($url, $var) !== false) {
            $tmp[] = $v;
        }
    }

    return count($tmp);
}

// Show blog post without year-month
function get_post_name(string $name) {
    $var = new Post();

    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $url = site_url() . 'search/' . $search;
        header("Location: $url");
    }

    if (config('permalink.type') != 'post') {
        $post = find_posts(null, null, $name);
        $current = $post['current'];
        $redir = site_url() . date('Y/m', $current->date) . '/' . $name;
        header("location: $redir", TRUE, 301);
    }

    $post = find_post_name($name);

    $prev = new Post();
    $next = new Post();
    $pview = '';
    $author = '';
    $layout = '';

    duplicate_code_1($post, $current, $prev, $next, $pview, $author, $layout);

    render($pview, array(
        'title' => $current->value . ' - ' . blog_title(),
        'description' => $current->description,
        'canonical' => $current->url,
        'p' => $current,
        'author' => $author->name,
        'bodyclass' => 'in-post category-' . $current->ct . ' type-' . $current->type->value,
        'breadcrumb' => get_breadcrumb($current),
        'prev' => has_prev($prev),
        'next' => has_next($next),
        'type' => $var,
        'is_post' => true,
    ));
}

// Edit blog post
function get_post_name_edit(string $name) {

    if (is_logged()) {

        $user = $_SESSION[config("site.url")]['user'];
        $role = user('role', $user);

        config('views.root', 'views/admin/views');
        $post = find_post_name($name);

        if (!$post) {
            $post = find_draft($name);
            if (!$post) {
                not_found('get_post_name_edit 298');
            }
        }

        $current = $post['current'];

        if (isset($current->image)) {
            $type = 'is_science_ed';
        } else {
            $type = 'is_post';
        }

        if ((strcmp($user, $current->author->name) == 0) || (strcmp($role, 'admin') == 0) || ($role === 'superadmin')) {
            render('edit-content', array(
                'title' => $type . ' - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'p' => $current,
                'type' => $type,
                'is_admin' => true,
                'bodyclass' => 'edit-post',
                'breadcrumb' => '<span itemscope="itemscope" itemtype="https://schema.org/BreadcrumbList">
   <span itemprop="itemListElement" itemscope="itemscope" itemtype="https://schema.org/ListItem">
   <a expr:href="' . site_url() . '" itemprop="url">
 <span itemprop="name">Home</span>
  </a>
  <span content="1" itemprop="position">
   </span>
</span>' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->tag->tagb . ' &#187; ' . $current->value
            ));
        } else {
            render('denied', array(
                'title' => $type . ' - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'p' => $current,
                'bodyclass' => 'denied',
                'is_admin' => true,
                'breadcrumb' => '<span itemscope="itemscope" itemtype="https://schema.org/BreadcrumbList">
   <span itemprop="itemListElement" itemscope="itemscope" itemtype="https://schema.org/ListItem">
   <a expr:href="' . site_url() . '" itemprop="url">
 <span itemprop="name">Home</span>
  </a>
  <span content="1" itemprop="position">
   </span>
</span>' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->tag->tagb . ' &#187; ' . $current->value
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
}

// Get edited data from blog post
function post_post_name_edit() {

    $message = array();
    $oldfile = '';
    $title = '';
    $image = '';
    $tag = '';
    $type = '';
    $url = '';
    $content = '';

    duplicate_code_8($message, $oldfile, $title, $image, $tag, $type, $url, $content);

    config('views.root', 'views/admin/views');

    render('edit-content', array(
        'title' => $type . ' - ' . blog_title(),
        'description' => strip_tags(blog_description()),
        'canonical' => site_url(),
        'error' => '<ul>' . $message['error'] . '</ul>',
        'oldfile' => $oldfile,
        'postTitle' => $title,
        'postImage' => $image,
        'postTag' => $tag,
        'postUrl' => $url,
        'type' => $type,
        'is_admin' => true,
        'postContent' => $content,
        'bodyclass' => 'edit-post',
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Edit content'
    ));
}

// Delete blog post
function get_post_name_delete(string $name) {

    if (is_logged()) {

        $user = $_SESSION[config("site.url")]['user'];
        $role = user('role', $user);

        config('views.root', 'views/admin/views');
        $post = find_post_name($name);

        if (!$post) {
            $post = find_draft($name);
            if (!$post) {
                not_found('get_post_name_delete 400');
            }
        }

        $current = $post['current'];

        if ((strcmp($user, $current->author->name) == 0) || (strcmp($role, 'admin') == 0) || ($role === 'superadmin')) {
            render('delete-post', array(
                'title' => 'Delete post - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'p' => $current,
                'is_admin' => true,
                'bodyclass' => 'delete-post',
                'breadcrumb' => get_breadcrumb($current)
            ));
        } else {
            render('denied', array(
                'title' => 'Delete post - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'p' => $current,
                'is_admin' => true,
                'bodyclass' => 'delete-post',
                'breadcrumb' => '<span itemscope="itemscope" itemtype="https://schema.org/BreadcrumbList">
   <span itemprop="itemListElement" itemscope="itemscope" itemtype="https://schema.org/ListItem">
   <a expr:href="' . site_url() . '" itemprop="url">
 <span itemprop="name">Home</span>
  </a>
  <span content="1" itemprop="position">
   </span>
</span>' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->tag->tagb . ' &#187; ' . $current->value
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
}

// Get deleted data from blog post
function post_post_name_delete() {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if ($proper && is_logged()) {
        $file = from($_REQUEST, 'file');
        $destination = from($_GET, 'destination');
        delete_post($file, $destination);
    }
}

// Show blog post with year-month
function get_year_month_name(string $year, string $month, string $name) {
//$var = new Post();
    $var = '';

    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $url = site_url() . 'search/' . $search;
        header("Location: $url");
    }

    if (config('permalink.type') == 'post') {
        $redir = site_url() . 'post/' . $name;
        header("location: $redir", TRUE, 301);
    }

// Returns an array of posts (previous, current, next)
    $post = find_posts($year, $month, $name);
    if (!isset($post)) {
        not_found('get_year_month_name 479');
    }

    $current = new Post();
    $prev = new Post();
    $next = new Post();
    $pview = '';
    $author = '';
    $layout = '';

    duplicate_code_1($post, $current, $prev, $next, $pview, $author, $layout);

// If there are no next post
    if (is_array($next)) {
        $next = new Post();
    }

// If there are no prev post
    if (is_array($prev)) {
        $prev = new Post();
    }

    render($pview, array(
        'title' => $current->title->value . ' - ' . blog_title(),
        'description' => $current->description->value,
        'canonical' => $current->url,
        'p' => $current,
        'author' => $author->name,
        'bodyclass' => 'in-post category-' . $current->ct . ' type-' . $current->type->value,
        'breadcrumb' => get_breadcrumb($current),
        'prev' => has_prev($prev),
        'next' => has_next($next),
        'type' => $var,
        'is_post' => true,
    ));
}

function duplicate_code_1(array &$post, Post &$current, Post &$prev, Post &$next,
        string &$pview, string &$author, string &$layout) {
    $current = $post['current'];

    if (!$current) {
        not_found('duplicate_code_1 531');
    }

    if (config("views.counter") == "true") {
        add_view($current->file);
    }

//$author = get_author($current->author);
    $author = $current->author;

    if (!isset($author)) {
        $author = default_profile($current->author);
    }

    if (array_key_exists('prev', $post)) {
        $prev = $post['prev'];
    }

    if (array_key_exists('next', $post)) {
        $next = $post['next'];
    }

    if (isset($current->image)) {
        $var = 'imagePost';
    } else {
        $var = 'blogPost';
    }

    $vroot = rtrim(config('views.root'), '/');

    $lt = $vroot . '/layout--post--' . $current->ct . '.html.php';
    $pt = $vroot . '/layout--post--' . $current->type->value . '.html.php';
    $ls = $vroot . '/layout--post.html.php';
    if (file_exists($lt)) {
        $layout = 'layout--post--' . $current->ct;
    } else if (file_exists($pt)) {
        $layout = 'layout--post--' . $current->type->value;
    } else if (file_exists($ls)) {
        $layout = 'layout--post';
    } else {
        $layout = '';
    }

    $pv = $vroot . '/post--' . $current->ct . '.html.php';
    $pvt = $vroot . '/post--' . $current->type->value . '.html.php';
    if (file_exists($pv)) {
        $pview = 'post--' . $current->ct;
    } else if (file_exists($pvt)) {
        $pview = 'post--' . $current->type->value;
    } else {
        $pview = 'post';
    }
}

// Edit blog post
function get_year_month_name_edit(string $year, string $month, string $name) {

    if (is_logged()) {

        $user = $_SESSION[config("site.url")]['user'];
        $role = user('role', $user);

        config('views.root', 'views/admin/views');
        $post = find_posts($year, $month, $name);

        if (!$post) {
            $post = find_draft_full($year, $month, $name);
            if (!$post) {
                not_found('get_year_month_name_edit 603');
            }
        }

        $current = $post['current'];

        if (isset($current->image)) {
            $type = 'is_science_ed';
        } else {
            $type = 'is_post';
        }

        if ((strcmp($user, $current->author->name) == 0) || (strcmp($role, 'admin') == 0) || ($role === 'superadmin')) {
            render('edit-content', array(
                'title' => $type . ' - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'p' => $current,
                'type' => $type,
                'bodyclass' => 'edit-post',
                'is_admin' => true,
                'breadcrumb' => '<span itemscope="itemscope" itemtype="https://schema.org/BreadcrumbList">
   <span itemprop="itemListElement" itemscope="itemscope" itemtype="https://schema.org/ListItem">
   <a expr:href="' . site_url() . '" itemprop="url">
 <span itemprop="name">Home</span>
  </a>
  <span content="1" itemprop="position">
   </span>
</span>' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->tag->tagb . ' &#187; ' . $current->title->value
            ));
        } else {
            render('denied', array(
                'title' => $type . ' - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'p' => $current,
                'bodyclass' => 'denied',
                'is_admin' => true,
                'breadcrumb' => '<span itemscope="itemscope" itemtype="https://schema.org/BreadcrumbList">
   <span itemprop="itemListElement" itemscope="itemscope" itemtype="https://schema.org/ListItem">
   <a expr:href="' . site_url() . '" itemprop="url">
 <span itemprop="name">Home</span>
  </a>
  <span content="1" itemprop="position">
   </span>
</span>' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->tag->tagb . ' &#187; ' . $current->value
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
}

// Get edited data from blog post
function post_year_month_name_edit() {

    $message = array();
    $oldfile = '';
    $title = '';
    $image = '';
    $tag = '';
    $type = '';
    $url = '';
    $content = '';

    duplicate_code_8($message, $oldfile, $title, $image, $tag, $type, $url, $content);

    config('views.root', 'views/admin/views');

    render('edit-content', array(
        'title' => $type . ' - ' . blog_title(),
        'description' => strip_tags(blog_description()),
        'canonical' => site_url(),
        'error' => '<ul>' . $message['error'] . '</ul>',
        'oldfile' => $oldfile,
        'postTitle' => $title,
        'postImage' => $image,
        'postTag' => $tag,
        'postUrl' => $url,
        'type' => $type,
        'postContent' => $content,
        'is_admin' => true,
        'bodyclass' => 'edit-post',
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Edit content'
    ));
}

function duplicate_code_8(array &$message, string &$oldfile, string &$title, string &$image,
        string &$tag, string &$type, string &$url, string &$content) {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    $title1 = from($_REQUEST, 'title');
    $title = new Title($title1);

    $is_post = from($_REQUEST, 'is_post');
    $image = from($_REQUEST, 'image');
    $is_image = from($_REQUEST, 'is_science_ed');

    $tag1 = from($_REQUEST, 'tag');
    $tag = new Tag($tag1);

    $url = from($_REQUEST, 'url');
    $content = from($_REQUEST, 'content');
    $oldfile = from($_REQUEST, 'oldfile');
    $destination = from($_GET, 'destination');

    $description1 = from($_REQUEST, 'description');
    $description = new Desc($description1);

    $date = from($_REQUEST, 'date');
    $time = from($_REQUEST, 'time');
    $dateTime = null;
    $revertPost = from($_REQUEST, 'revertpost');
    $publishDraft = from($_REQUEST, 'updatepost');
    $category = from($_REQUEST, 'category');
    if ($date !== null && $time !== null) {
        $dateTime = $date . ' ' . $time;
    }

    if (!empty($is_image)) {
        $type = 'is_science_ed';
    } elseif (!empty($is_post)) {
        $type = 'is_post';
    }

    if ($proper && !empty($title1) && !empty($tag1) && !empty($content) && !empty($image)) {
        if (empty($url)) {
            $url = $title->value;
        }
        edit_content($title, $tag, $url, $content, $oldfile, $destination, $description, $dateTime, $image, $revertPost, $publishDraft, $category, 'image');
    } else if ($proper && !empty($title1) && !empty($tag1) && !empty($content) && !empty($is_post)) {
        if (empty($url)) {
            $url = $title->value;
        }
        edit_content($title, $tag, $url, $content, $oldfile, $destination, $description, $dateTime, null, $revertPost, $publishDraft, $category, 'post');
    } else {
        $message['error'] = '';
        if (empty($title1)) {
            $message['error'] .= '<li>Title field is required.</li>';
        }
        if (empty($tag1)) {
            $message['error'] .= '<li>Tag field is required.</li>';
        }
        if (empty($content)) {
            $message['error'] .= '<li>Content field is required.</li>';
        }
        if (!$proper) {
            $message['error'] .= '<li>CSRF Token not correct.</li>';
        }

        if (!empty($is_image)) {
            if (empty($image)) {
                $message['error'] .= '<li>Image field is required.</li>';
            }
        }
    }
}

// Delete blog post
function get_year_month_name_delete(string $year, string $month, string $name) {

    if (is_logged()) {

        $user = $_SESSION[config("site.url")]['user'];
        $role = user('role', $user);

        config('views.root', 'views/admin/views');
        $post = find_posts($year, $month, $name);

        if (!$post) {
            $post = find_draft_full($year, $month, $name);
            if (!$post) {
                not_found('get_year_month_name_delete 777');
            }
        }

        $current = $post['current'];

        if ((strcmp($user, $current->author->name) == 0) || (strcmp($role, 'admin') == 0) || ($role === 'superadmin')) {
            render('delete-post', array(
                'title' => 'Delete post - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'p' => $current,
                'bodyclass' => 'delete-post',
                'is_admin' => true,
                'breadcrumb' => '<span itemscope="itemscope" itemtype="https://schema.org/BreadcrumbList">
   <span itemprop="itemListElement" itemscope="itemscope" itemtype="https://schema.org/ListItem">
   <a expr:href="' . site_url() . '" itemprop="url">
 <span itemprop="name">Home</span>
  </a>
  <span content="1" itemprop="position">
   </span>
</span>' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->tag->tagb . ' &#187; ' . $current->title->value
            ));
        } else {
            render('denied', array(
                'title' => 'Delete post - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'p' => $current,
                'bodyclass' => 'delete-post',
                'is_admin' => true,
                'breadcrumb' => '<span itemscope="itemscope" itemtype="https://schema.org/BreadcrumbList">
   <span itemprop="itemListElement" itemscope="itemscope" itemtype="https://schema.org/ListItem">
   <a expr:href="' . site_url() . '" itemprop="url">
 <span itemprop="name">Home</span>
  </a>
  <span content="1" itemprop="position">
   </span>
</span>' . config('breadcrumb.home') . '</a></span> &#187; ' . $current->tag->tagb . ' &#187; ' . $current->value
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
}

// Get deleted data from blog post
function post_year_month_name_delete() {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if ($proper && is_logged()) {
        $file = from($_REQUEST, 'file');
        $destination = from($_GET, 'destination');
        delete_post($file, $destination);
    }
}

// Get the meta description from first characters in content and return a description object
function get_description(string $content, int $nbchar = null): Desc {
    if (empty($nbchar)) {
        $nbchar = (int) config('description.char');
        if (empty($nbchar)) {
            $nbchar = 150;
        }
    }

    if (strlen(strip_tags($content)) < $nbchar) {
        $description = safe_html(strip_tags($content));
    } else {
        $description2 = substr($content, 0, $nbchar);
        $description1 = safe_html(strip_tags($description2));
        $description = substr($description1, 0, strrpos($description1, ' '));
    }

    return new Desc($description);
}

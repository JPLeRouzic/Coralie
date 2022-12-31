<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Show the search page
route('GET', '/search/:keyword', function ($keyword) {

    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $url = site_url() . 'search/' . $search;
        header("Location: $url");
    }

    $page1 = from($_GET, 'page');
    $page = $page1 ? (int)$page1 : 1;
    $perpage = config('search.perpage');

    $posts = get_keyword($keyword, $page, $perpage);
    
    $tsearch = new Search() ;
    $tsearch->title = $keyword;
    
    $vroot = rtrim(config('views.root'), '/');
    
    if (!$posts || $page < 1) {
        // a non-existing page or no search result
        render('404-search', array(
            'title' => 'Search results not found! - ' . blog_title(),
            'description' => 'Search results not found!',
            'search' => $tsearch,
            'keyword404' =>  $keyword,
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; No search results',
            'canonical' => site_url(),
            'bodyclass' => 'error-404-search',
            'is_404search' => true,
        ));
        die;
    }

    $total = keyword_count($keyword);
    
    $pv = $vroot . '/main--search.html.php'; 
    if (file_exists($pv)) {
        $pview = 'main--search';
    } else {
        $pview = 'main';
    }

    render($pview, array(
        'title' => 'Search results for: ' . tag_i18n($keyword) . ' - ' . blog_title(),
        'description' => 'Search results for: ' . tag_i18n($keyword) . ' on ' . blog_title() . '.',
        'canonical' => site_url() . 'search/' . strtolower($keyword),
        'page' => $page,
        'posts' => $posts,
        'search' => $tsearch,
        'bodyclass' => 'in-search search-' . strtolower($keyword),
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Search results for: ' . tag_i18n($keyword),
        'pagination' => has_pagination($total, $perpage, $page),
        'is_search' => true,
    ));
});

// The JSON API
route('GET', '/api/json', function () {

    header('Content-type: application/json');

    $page1 = from($_GET, 'page');
    $page = $page1 ? (int)$page1 : 1;
    $perpage = config('json.count');

    echo generate_json(get_posts(null, $page, $perpage));
});

// Show the RSS feed
route('GET', '/feed/rss', function () {

    header('Content-Type: application/rss+xml');

    // Show an RSS feed with the 30 latest posts
    echo generate_rss(get_posts(null, 1, config('rss.count')));
});

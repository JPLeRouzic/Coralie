<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Return search page.
function get_keyword(string $keyword, int $page, int $perpage) {
    $posts = get_posts_sorted();

    $tmp = array();

    $words = explode(' ', $keyword);

    foreach ($posts as $index => $v) {
        $arr = explode('_', $v['basename']);
        if (isset($arr[2])) {
            if (isset($v['content'])) {
                $filter = $arr[1] . ' ' . $arr[2] . ' ' . $v['content'];
            } else {
                $filter = $arr[1] . ' ' . $arr[2];
            }
            foreach ($words as $word) {
                if (stripos($filter, $word) !== false) {
                    $tmp[] = $v;
                }
            }
        }
    }

    if (empty($tmp)) {
        return $tmp;
    }

    return get_posts($tmp, $page, $perpage);
}

// Return search result count
function keyword_count(string $keyword) {
    $posts = get_posts_sorted();

    $tmp = array();

    $words = explode(' ', $keyword);

    foreach ($posts as $index => $v) {
        $arr = explode('_', $v['basename']);
        if (isset($arr[2])) {
            $filter = $arr[1] . ' ' . $arr[2];
            foreach ($words as $word) {
                if (stripos($filter, $word) !== false) {
                    $tmp[] = $v;
                }
            }
        }
    }

    $tmp1 = array_unique($tmp, SORT_REGULAR);

    return count($tmp1);
}

// Search form
function search($text = null) {
    if (!empty($text)) {
        echo <<<EOF
    <form id="search-form" method="get">
        <input type="text" class="search-input" name="search" value="{$text}" onfocus="if (this.value == '{$text}') {this.value = '';}" onblur="if (this.value == '') {this.value = '{$text}';}">
        <input type="submit" value="{$text}" class="search-button">
    </form>
EOF;
    } else {
        echo <<<EOF
    <form id="search-form" method="get">
        <input type="text" class="search-input" name="search" value="Search" onfocus="if (this.value == 'Search') {this.value = '';}" onblur="if (this.value == '') {this.value = 'Search';}">
        <input type="submit" value="Search" class="search-button">
    </form>
EOF;
    }
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $url = site_url() . 'search/' . $search;
        header("Location: $url");
    }
}

// The not found error
function not_found(string $errcode) {
    header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
    render('404', array(
        'title' => 'This page doesn\'t exist! Error code: ',
        'description' => '404 Not Found',
        'canonical' => site_url(),
        'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; 404 Not Found',
        'bodyclass' => 'error-404',
        'error_code' => $errcode,
        'is_404' => true,
    ));
    die();
}

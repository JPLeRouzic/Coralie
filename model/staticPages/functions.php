<?php


// Get static page path. Unsorted.
function get_static_pages(): array
{
    static $_page = array();

    if (empty($_page)) {
        $url = 'content/index/index-page.txt';
        $_page = unserialize(file_get_contents($url));
    }
    return $_page;
}

// Get static page path. Unsorted.
function get_static_sub_pages(string $static = null): array
{
    static $_sub_page = array();

    if (empty($_sub_page)) {
        $url = 'content/index/index-sub-page.txt';
        $_sub_page = unserialize(file_get_contents($url));
    }
    if ($static != null) {
        $stringLen = strlen($static);
        return array_filter($_sub_page, function ($sub_page) use ($static, $stringLen) {
            $x = explode("/", $sub_page);
            if ($x[count($x) - 2] == $static) {
                return true;
            }
            return false;
        });
    }
    return $_sub_page;
}


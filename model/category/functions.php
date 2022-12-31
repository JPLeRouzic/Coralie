<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Get the posts in this category
function get_posts_in_category(string $category, int $page, int $perpage): array {
    $posts = get_posts_sorted();

    $tmp = array();

    if (empty($perpage)) {
        $perpage = 10;
    }

    foreach ($posts as $index => $v) {

        $filepath = $v['dirname'] . '/' . $v['basename'];

        // Extract the date
        $arr = explode('_', $filepath);

        // Replaced string
        $replaced = substr($arr[0], 0, strrpos($arr[0], '/')) . '/';

        // Author string
        $str = explode('/', $replaced);
        $cat = $str[count($str) - 3];

        if (strtolower($category) === strtolower($cat)) {
            $tmp[] = $v;
        }
    }

    if (empty($tmp)) {
        not_found('get_posts_in_category 36');
    }

    $tmp1 = array_unique($tmp, SORT_REGULAR);

    return get_posts($tmp1, $page, $perpage);
}

// Return info about one category  
function get_category_info(string $category): Category {
    $catgries = get_category_files();
//    echo '<br>inp: ' . $category . '<br><br>' ;
//    var_dump($catgries) ;
    /*
     * array(7) { 
     *     [0]=> string(31) "content/data/category/中文.md" 
     *     [1]=> string(30) "content/data/category/jjjjj.md" 
     *     [2]=> string(33) "content/data/category/francais.md" 
     *     [3]=> string(30) "content/data/category/rrrrr.md" 
     *     [4]=> string(32) "content/data/category/new-cat.md" 
     *     [5]=> string(29) "content/data/category/nnnn.md" 
     *     [6]=> string(32) "content/data/category/english.md" 
     * }
     */
    // $catgries is an array of array.
    // Each of those array are compose of an index and a string, which is the file path 
    // of the corresponding category:
    // ex: 'content/data/category/english.md'

    if (!empty($catgries)) {
        // Search for our category in all categories
        foreach ($catgries as $index => $v) {
//echo '<br>plop 2<br>' ;
//var_dump($v) ;
            // Find the position of the first occurrence of a case-insensitive $category in $v
            if (stripos($v, $category . '.md') !== false) {
//echo '<br>plop 3' ;
//                $replaced = substr($v, 0, strrpos($v, '/')) . '/';
//                $namecat = str_replace('.md', '', $replaced);
                $catgr = found_our_category($category, $v);
                return $catgr;
            }
        }
    }
    return default_category();
}

// Return info about categories in an array
function get_categories_info(): array {
    $catgries = get_category_files();
    // $catgries is an array of array.
    // Each of those array are compose of an index and a string, which is the file path 
    // of the corresponding category:
    // ex: 'content/data/category/english.md'

    $rtrn = array();

    if (!empty($catgries)) {

        // Search for our category in all categories
        foreach ($catgries as $index => $v) {
            // Get the contents and convert it to HTML
            $content = file_get_contents($v);
            $category = get_content_tag('t', $content);
            $catgr = found_our_category($category, $v);

            $rtrn[] = $catgr;
        }
    } else {
        $rtrn[] = default_category();
    }
    return $rtrn;
}

function found_our_category(string $category, string $v): Category {
    // We have found our category
    // string(32) "content/data/category/english.md"

    $catgr = new Category($category);

    // Replaced string
    $replaced = substr($v, 0, strrpos($v, '/')) . '/';

    // The static page URL
    $url = str_replace($replaced, '', $v);

    $catgr->url = site_url() . 'category/' . str_replace('.md', '', $url);

    $catgr->file = $v;

    // Get the contents and convert it to HTML
    $content = file_get_contents($v);

    // Extract the title and body
    // function get_content_tag(Tag $tag, string $string, string $alt = null): string  {
    $catgr->title = get_content_tag('t', $content, $category);

    $catgr->description = get_content_tag("d", $content, $category);

    return $catgr;
}

// Return default category
function default_category(): Category {
    $cat = new Category('Uncategorized');

    $cat->title = 'Uncategorized';
    $cat->url = site_url() . 'category/uncategorized';
    $cat->description = 'Topics that don&#39;t need a category, or don&#39;t fit into any other existing category.';

    $cat->file = 'hard_coded value';

    return $cat;
}

/*
  array(4) {
  [0]=> array(2) {
  [0]=> string(7) "english"
  [1]=> string(0) ""
  }
  [2]=> array(2) {
  [0]=> string(8) "francais"
  [1]=> string(0) ""
  }
  [3]=> array(2) {
  [0]=> string(13) "uncategorized"
  [1]=> string(13) "Uncategorized"
  }
  [1]=> array(2) {
  [0]=> string(6) "中文"
  [1]=> string(0) ""
  }
  }
  a:4:{i:0;a:2:{i:0;s:7:"english";i:1;s:0:"";}i:2;a:2:{i:0;s:8:"francais";i:1;s:0:"";}i:3;a:2:{i:0;s:13:"uncategorized";i:1;s:13:"Uncategorized";}i:1;a:2:{i:0;s:6:"中文";i:1;s:0:"";}}
 */

function category_list(): array {

    $dir = "content/widget";
    $filename = "content/widget/category.list.cache";
    $tmp = array();
    $cat = array();

    if (is_dir($dir) === false) {
        mkdir($dir, 0775, true);
    }

    if (file_exists($filename)) {
        $cat = unserialize(file_get_contents($filename));
    } else {
        // If file does not exist, create it
        $arr = get_categories_info();
        foreach ($arr as $catgry) {
//            $cat[] = array($a->md, $a->title);
            $cat[] = array($catgry->title);
        }
        //  Don't forget the hard coded category of uncategorized categories
        array_push($cat, array('Uncategorized'));
        asort($cat);
        $tmp = serialize($cat);
        file_put_contents($filename, print_r($tmp, true));
    }

    /*
     * $cat:
     * array(4) 
     *      { 
     *      [0]=> array(2) 
     *          { 
     *          [0]=> string(7) "english" 
     *          [1]=> string(0) "" 
     *          } 
     *      [2]=> array(2) 
     *          { 
     *          [0]=> string(8) "francais" 
     *          [1]=> string(0) "" 
     *          } 
     *      [3]=> array(2) 
     *          { 
     *          [0]=> string(13) "uncategorized" 
     *          [1]=> string(13) "Uncategorized" 
     *          } 
     *      [1]=> array(2) 
     *          { 
     *          [0]=> string(6) "中文" 
     *          [1]=> string(0) "" 
     *          } 
     *      }
     */
    return $cat;
}

// Return category count. Matching $var.
function get_categorycount(string $var) {
    $posts = get_posts_sorted();

    $tmp = array();

    foreach ($posts as $index => $v) {

        $filepath = $v['dirname'] . '/' . $v['basename'];

        // Extract the date
        $arr = explode('_', $filepath);

        // Replaced string
        $replaced = substr($arr[0], 0, strrpos($arr[0], '/')) . '/';

        // Author string
        $str = explode('/', $replaced);
        $cat = '/blog/' . $str[count($str) - 3];
        if (stripos($cat, "$var") !== false) {
            $tmp[] = $v;
        }
    }

    return count($tmp);
}

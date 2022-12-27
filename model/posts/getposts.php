<?php

/*
 * parameter $post1 is an array:
 * array(2) { 
 * ["basename"]=> string(38) "2022-08-20-20-09-36_system_35983834.md" 
 * ["dirname"]=> string(37) "content/users/admin/blog/english/post" 
 * }
 * Return selected blog posts from an array of posts, each element being a string representing the name of the post. 
 * It returns posts in the form of classes
 */

function get_posts(array|null $posts1, int $page = 1, int $perpage = 0): array {
    if (empty($posts1)) {
        $posts1 = get_posts_sorted();
    }

    $tmp = array();

// array_slice() returns the sequence of elements from the array array as specified 
// by the offset and length parameters.
// For ex: $nx = get_posts($posts, 8, 2);
// will return two posts starting at $posts[8]
//$posts = array_slice($posts1, ($page - 1) * $perpage, $perpage, true);
    $posts = array_slice($posts1, ($page - 1), $perpage, true);

    arsort($posts);

    foreach ($posts as $index => $v) {
        $ret_gp = get_post($v);
        if (isset($ret_gp)) {
            $tmp[] = $ret_gp;
        }
    }
    return $tmp;
}

function get_post(array &$v) {

    $filepath = $v['dirname'] . '/' . $v['basename'];

// Test if this file exists (it may have been moved manually)
    if (!is_file($filepath)) {
        echo '<br>Error filepath: $filepath does not exist<br>';
        return null;
    }

// Extract the date
    $arr = explode('_', $filepath);

// Assemble replaced string
    $replaced = substr($arr[0], 0, strrpos($arr[0], '/')) . '/';

    return create_post($v, $arr, $replaced);
}

function create_post(array $v, $arr, $replaced) {
    $post = new Post();

    $filepath = $v['dirname'] . '/' . $v['basename'];

// Get categories
    $content = file_get_contents($filepath);

    $str = explode('/', $replaced);

    if ($str[count($str) - 3] == 'uncategorized') {
        $post->catg = default_category();
    } else {
        /*
         *  $catC) ;
         * array(4) { 
         *  [2]=> array(2) { 
         *  [0]=> string(7) "english" 
         *  [1]=> string(7) "English" 
         *  } 
         *  [1]=> array(2) { 
         *  [0]=> string(8) "francais" 
         *  [1]=> string(9) "Français" 
         *  } 
         *  [3]=> array(2) { 
         *  [0]=> string(13) "uncategorized" 
         *  [1]=> string(13) "Uncategorized" 
         *  } 
         *  [0]=> array(2) { 
         *  [0]=> string(6) "中文" 
         *  [1]=> string(6) "中文" 
         *  } 
         *  }
         */

        $catC = category_list();

        foreach ($catC as $k => $vc) {
            if ((isset($vc[1])) && (strcmp($vc[1], $str[count($str) - 3]) == 0)) {
                $post->catg = get_category_info($vc[1]);
                break;
            }
        }
    }
    if (!isset($post->catg)) {
        $post->catg = default_category();
    }

    $type = $str[count($str) - 2];
    $post->ct = $str[count($str) - 3];

// The post author
    $author = $str[count($str) - 5];
    $post->author = get_author($author);

    $post->type = new Type($type);

    $dt = str_replace($replaced, '', $arr[0]);

// $dt: string(19) "2021-07-26-19-09-43"
// string(50) "content/admin/blog/中文/post/2021-08-16-18-49-20"
    if (strpos($dt, 'content') !== false) {
// FIXME
        $tmp2 = explode('/', $dt);
        $dt = $tmp2[count($tmp2) - 1];
    }
    $t1 = str_replace('-', '', $dt);
    $time = new DateTime($t1);
    $timestamp = $time->format("Y-m-d H:i:s");

// The post date
    $post->date = strtotime($timestamp);

// The archive per day
    $post->archive = new Archive(site_url() . 'archive/' . date('Y-m', $post->date));

    if (isset($arr[2])) {
        $url1 = $arr[2];
    } else {
//var_dump($arr) ;
        /*
          array(2) {
          [0]=>
          string(57) "content/users/admin/blog/english/post/2020-08-28-20-13-21"
          [1]=>
          string(35) "metastase-as-a-metabolic-disease.md"
          }
         */
        $url1 = $arr[0];
    }
    if (config('permalink.type') == 'post') {
        $post->url = site_url() . 'post/' . str_replace('.md', '', $url1);
    } else {
        $post->url = site_url() . date('Y/m', $post->date) . '/' . str_replace('.md', '', $url1);
    }


    $post->file = $filepath;

    $namemd = explode('.md', $v['basename']);
    $post->md = $namemd[0];

    /*
     *  Extract the title and tags from the post
     */
    $post->title = new Title(get_content_tag('t', $content, 'Untitled: ' . date('l jS \of F Y', $post->date)));

    $tag = array();
    $url = array();
    $bc = array();

    $tagt = get_content_tag('tag', $content);
    $t = explode(',', rtrim($arr[1], ','));

    if (!empty($tagt)) {
        $tl = explode(',', rtrim($tagt, ','));
        $tCom = array_combine($t, $tl);
        foreach ($tCom as $key => $val) {
            if (!empty($val)) {
                $tag[] = array($val, site_url() . 'tag/' . strtolower($key));
            } else {
                $tag[] = array($key, site_url() . 'tag/' . strtolower($key));
            }
        }
    } else {
        foreach ($t as $tt) {
            $tag[] = array($tt, site_url() . 'tag/' . strtolower($tt));
        }
    }

    foreach ($tag as $a) {
        $url[] = '<a rel="tag" href="' . $a[1] . '">' . $a[0] . '</a>';
        $bc[] = tag_bc($a);
    }

    /*
     *  Tags
     */
    $post->tag = new Tag(implode(' ', $url));

    $post->tag->tagb = implode(' » ', $bc);

// Related posts (related by same tags)
    $post->tag->related = new Tag(rtrim($arr[1], ','));

    $more = explode('<!--more-->', $content);
    if (isset($more['1'])) {
        $content = $more['0'] . '<a id="more"></a><br>' . "\n\n" . '<!--more-->' . $more['1'];
    }

    /*
     *  Get the page views count from the views.json file
     */
    if (config('views.counter') == 'true') {
        $post->views = get_views($post->file);
    } else {
        $post->views = 'int to be defined';
    }

    /*
     * Get the content from the file and convert it to HTML
     */
    $post->body = MarkdownExtra::defaultTransform(remove_html_comments($content));

    /*
     *   Get short description labelled by the tag "d"
     */
    $post->description = new Desc(get_content_tag("d", $content));

    return $post;
}

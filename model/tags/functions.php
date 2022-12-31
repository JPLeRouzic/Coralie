<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Returns an array of tags
function get_tags(Tag $tag, int $page, int $perpage, bool $random = false): array {
    $posts = get_posts_sorted();

    if ($random === true) {
        shuffle($posts);
    }

    $tmp = array();

    foreach ($posts as $index => $v) {
        $str = explode('_', $v['basename']);
        $mtag = explode(',', rtrim($str[1], ','));
        $etag = explode(',', $tag->value);
        foreach ($mtag as $t) {
            foreach ($etag as $e) {
                $e = trim($e);
                if (strtolower($t) === strtolower($e)) {
                    $tmp[] = $v;
                }
            }
        }
    }

    if (empty($tmp)) {
        not_found('get_tags');
    }

    $tmp1 = array_unique($tmp, SORT_REGULAR);

    return get_posts($tmp1, $page, $perpage);
}

/* Get tag info files.
 * This function should return an array of:
 * name, description, count for each tag
 */
// FIXME it is useful only the first time the function is ran to create HTMLy tags
function get_tags_info(): array {
    $tagslist = array();

    // Get posts
    $posts = get_post_unsorted();

    // extract tags
    foreach ($posts as $postpath) {
//    var_dump($postpath) ;
        // Get categories
        $pathfile = $postpath["dirname"] . '/' . $postpath["basename"];
        $content = file_get_contents($pathfile);
        $tagsstring = get_content_tag('tag', $content);
        $tagsarray = explode(',', $tagsstring);
        foreach ($tagsarray as $cell) {
            $tagslist[] = $cell;
        }
    }

    /* Keep only one instance of each tag */
    $taglist = array_unique($tagslist);

    /*
     * We have now a tag list compiled from the real usage in post files.
     * Some of those tags have been created with metadata, some have been created 
     * quickly during post creation.
     * We need to search those tags that have metadata.
     * We return a unified view of all tags.
     * We need to update the tag files
     */

    $rtrn = array();

    if (!empty($taglist)) {

        // Search for our tag in all categories
        foreach ($taglist as $index => $tag) {

            $rtrn[] = from_tag_to_tagger($tag);
        }
    }
    return $rtrn;
}

// Create an object Tag from a tag name
function get_tag_info(string $targettag): Tag {
    $tags = get_tags_files();
    // $tags is an array of array.
    // Each of those array are compose of an index and a string, which is the file path 
    // of the corresponding tag:
    // ex: 'content/data/tag/english.md'

    if (!empty($tags)) {

        // Search for our tag in all categories
        foreach ($tags as $index => $tag) {
            if (strpos($tag, $targettag) == false) {
                // strpos returns false if the tag was not found in the tag path.
                continue;
            }
            return from_tag_to_tagger($tag);
        }
    }
    $tager = new Tag('none');
    return $tager;
}

/*
 * Create an object Tag from a path + name string
 * string(30) "content/data/tag/tagtotourl.md"
 */

function from_tag_to_tagger($title) {

    $tager = new Tag($title);
    $content = '' ;

    // Get the contents and convert it to HTML
    // test if Tag file exists
    if (is_file('content/data/tag/' . $title)) {
        $content = file_get_contents('content/data/tag/' . $title);
    } else {
        /*
         *  if it doesn't exist, create it
         *     <!--t title t-->
         *     <!--d description. d-->
         */
        $content = '   <!--t ' . $title . 't-->
         <!--d description to be done. d-->';
        $tagpathname = $title;
        file_put_contents($tagpathname, $content);
    }

    // The static page URL
    $tager->url = site_url() . 'tag/' . $title;

    $tager->tagb = ''; // Tag of related posts

    $tager->tagb = 'tagb to be done';

    // Get the contents and convert it to HTML
    $body = remove_html_comments($content);

    $tager->description = get_content_tag("d", $content, $body);

    return $tager;
}

/*
 * Create an object Tag from a path + name string
 * string(30) "content/data/tag/tagtotourl.md"
 */

function from_tagpath_to_tagger($tag) {

    $patharray = explode('/', $tag);

    $titlemd = $patharray[count($patharray) - 1];
    $title = substr($titlemd, 0, (strlen($titlemd) - 3));

    $tager = new Tag($title);

    // Get the contents and convert it to HTML
    $content = file_get_contents($tag);

    // The static page URL
    $tager->url = site_url() . 'tag/' . $title;

    $tager->tagb = ''; // Tag of related posts

    $tager->tagb = 'tagb to be done';

    // Get the contents and convert it to HTML
    $body = remove_html_comments($content);

    $tager->description = get_content_tag("d", $content, $body);

    return $tager;
}

/* Get tags from tag info files.
 * This function should provide an array of tag file names
 * the caller of this functionn will have to get files' content from those names
 */

function get_tags_files() {
    static $_desc = array();

    $dir = 'content/data/tag';
    if (is_dir($dir)) {
        $dh = opendir($dir);
        if ($dh) {
            while (($filename = readdir($dh)) !== false) {
                if ((strcmp($filename, '.') == 0) || (strcmp($filename, '..') == 0)) {
                    continue;
                }
                $_desc[] = $dir . '/' . $filename;
            }
            closedir($dh);
            return $_desc;
        }
    }
}

// Return tag list

function tag_list(bool $custom = null) {

    $dir = "content/widget";
    $filename = "content/widget/tag.list.cache";
    $tmp = array();
    $cat = array();
//    $list = array();

    if (is_dir($dir) === false) {
        mkdir($dir, 0775, true);
    }

    if (file_exists($filename)) {
        $cat = unserialize(file_get_contents($filename));
    } else {
        $arr = get_tags_info();
        foreach ($arr as $a) {
            $cat[] = array($a->md, $a->title);
        }
        array_push($cat, array('uncategorized', 'Uncategorized'));
        asort($cat);
        $tmp = serialize($cat);
        file_put_contents($filename, print_r($tmp, true));
    }

    if (!empty($custom)) {
        return $cat;
    }

    echo '<ul>';

    foreach ($cat as $k => $v) {
        if (get_tagcount($v[0]) !== 0) {
            echo '<li><a href="' . site_url() . 'tags/' . $v[0] . '">' . $v[1] . '</a></li>';
        }
    }

    echo '</ul>';
}

// Return tag count. Matching provided $var
function get_tagcount(Tag $var) {
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

        if (stripos($cat, $var->value) !== false) {
            $tmp[] = $v;
        }
    }

    return count($tmp);
}

// Return tag cloud.
function tag_cloud(bool $custom = null) {

    $dir = "content/widget";
    $filename = "content/widget/tags.cache";
    $tg = array();

    if (is_dir($dir) === false) {
        mkdir($dir, 0775, true);
    }

    $posts = get_post_unsorted();
    $tags = array();

    if (!empty($posts)) {

        if (!file_exists($filename)) {
            foreach ($posts as $index => $v) {
                $arr = explode('_', $v);
                $data = rtrim($arr[1], ',');
                $mtag = explode(',', $data);
                foreach ($mtag as $etag) {
                    $tags[] = strtolower($etag);
                }
            }
            $tag_collection = array_count_values($tags);
            ksort($tag_collection);
            $tg = serialize($tag_collection);
            file_put_contents($filename, print_r($tg, true));
        } else {
            $tag_collection = unserialize(file_get_contents($filename));
        }

        if (empty($custom)) {
            echo '<ul class="taglist">';
            foreach ($tag_collection as $tag => $count) {
                echo '<li class="item"><a href="' . site_url() . 'tag/' . $tag . '">' . tag_i18n($tag) . '</a> <span class="count">(' . $count . ')</span></li>';
            }
            echo '</ul>';
        } else {
            return $tag_collection;
        }
    } else {
        if (empty($custom)) {
            return;
        }
        return $tags;
    }
}

// save tag to file
function save_tag_i18n(Tag $tag, string $tagDisplay) {

    $dir = 'content/data/';
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $filename = "content/data/tags.lang";
//    $tags = array();
    $tmp = array();
    $views = array();

    $tt = explode(',', rtrim($tag->value, ','));
    $tl = explode(',', rtrim($tagDisplay, ','));
    $tags = array_combine($tt, $tl);

    if (file_exists($filename)) {
        $views = unserialize(file_get_contents($filename));
        foreach ($tags as $key => $val) {
            if (isset($views[$key])) {
                $views[$key] = $val;
            } else {
                $views[$key] = $val;
            }
        }
    } else {
        $views = $tags;
    }

    $tmp = serialize($views);
    file_put_contents($filename, print_r($tmp, true));
}

// get tags from file
function tag_i18n(string $tag) {
    static $tags = array();

    if (empty($tags)) {
        $filename = "content/data/tags.lang";
        if (file_exists($filename)) {
            $tags = unserialize(file_get_contents($filename));
        }
    }
    if (isset($tags[$tag])) {
        return $tags[$tag];
    }
    return $tag;
}

/* Get tag inside the markdown files
 * 
  <!--t A plea for a gene therapy for ALS t-->
  <!--d ALS is a strange disease where there are way too much explanations. d-->
  <!--tag gene therapy,SMA,TDP 43,AVXS 101,ALS tag-->

  ... content ...
 * 
 * $tag parameter:
 * 't' is for title of the post
 * 'd' is about the short description targeted at search engines
 * ' tag' is to get the list of tags of a post
 * 'image' is for posts with images (obsolete)
 * 'ranked' is for posts ranked by some criteria
 * 
 * $content parameter: Where to search for tags
 * $alt parameter: Is for alternative text, if no tags can be found in $content
 */

function get_content_tag(string $tag, string $content, string $alt = ''): string {
    $reg = '/\<!--' . $tag . '(.+)' . $tag . '--\>/';
    $ary = array();
    if (preg_match($reg, $content, $ary)) {
        if (isset($ary[1])) {
            $result = trim($ary[1]);
            if (!empty($result)) {
                return $result;
            }
        }
    }
    return $alt;
}

// rename tag folder
function rename_tag_folder($string, $old_url) {

    $old = str_replace('.md', '/', $old_url);
    $url = substr($old, 0, strrpos($old, '/'));
    $ostr = explode('/', $url);
    $url = '/blog/' . $ostr[count($ostr) - 1];

    $dir = get_category_folder();

    $file = array();

    foreach ($dir as $index => $v) {
        if (stripos($v, $url) !== false) {
            $str = explode('/', $v);
            $n = $str[count($ostr) - 4] . '/' . $str[count($ostr) - 3] . '/' . $str[count($ostr) - 2] . '/' . $string . '/';
            $file[] = array($v, $n);
        }
    }

    foreach ($file as $f) {
        if (is_dir($f[0])) {
            rename($f[0], $f[1]);
        }
    }
}

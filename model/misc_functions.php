<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

/*
 * Find a variable by its name in an array
 * Example usage: $is_post = from($_REQUEST, 'is_post');
 */

function from(array $source, string $name) {
    return isset($source[$name]) ? $source[$name] : null;
}

// require_once 'controller/plugins/php-markdown/MarkdownExtra.inc.php';
require_once dirname(__FILE__) . '/../controller/plugins/php-markdown/MarkdownExtra.inc.php';

// Get backup file.
// function get_zip_files(): array|false // FIXME this is valid in php 8
function get_zip_files(): array {
    static $_zip = array();

    if (empty($_zip)) {

        // Get the names of all the
        // zip files.

        $_zip = glob('backup/*.zip');
    }

    return $_zip;
}

// Do not remove!
// function called through usort function. 
// Sort by filename.
function sortfile($a, $b) {
    return $a['basename'] == $b['basename'] ? 0 : (($a['basename'] < $b['basename']) ? 1 : -1);
}

// Do not remove!
// function called through usort function. 
// Sort by date.
function sortdate($a, $b) {
    return $a->date == $b->date ? 0 : (($a->date < $b->date) ? 1 : -1);
}

// Helper function to determine whether
// to show the previous buttons
function has_prev(Post|null $prev) {
    if (isset($prev->url)) {
        return duplicate_code_5($prev);
    }
}

// Helper function to determine whether
// to show the next buttons
function has_next(Post|null $next) {
    if (isset($next->url)) {
        return duplicate_code_5($next);
    }
}

function duplicate_code_5(Post $post) {
    return array(
        'url' => $post->url,
        'title' => $post->title->value,
        'date' => $post->date,
        'body' => $post->body,
        'description' => $post->description,
        'tag' => $post->tag->value,
        'category' => $post->catg->title,
        'author' => $post->author->name,
        'related' => $post->tag->related,
        'views' => $post->views,
        'type' => $post->type->value,
        'file' => $post->file,
    );
}

// Helper function to determine whether
// to show the pagination buttons
function has_pagination(int $total, int $perpage, int $page = 1): array {
    if (!$total) {
        $total = count(get_post_unsorted());
    }
    $totalPage = ceil($total / $perpage);
    $number = 'Page ' . $page . ' of ' . $totalPage;
    $pager = get_pagination($page, $total, $perpage);
    return array(
        'prev' => $page > 1,
        'next' => $total > $page * $perpage,
        'pagenum' => $number,
        'html' => $pager,
        'items' => $total,
        'perpage' => $perpage
    );
}

//function to return the pagination string
function get_pagination(int $page, int $totalitems, int $perpage = 10): string {
    $counter = 0;
    //defaults
    $adjacents = 1;
    $pagestring = '?page=';

    //other vars
    $prev = $page - 1;                                    //previous page is page - 1
    $next = $page + 1;                                    //next page is page + 1
    $lastpage = ceil($totalitems / $perpage);             //lastpage is = total items / items per page, rounded up.
    $lpm1 = $lastpage - 1;                                //last page minus 1

    /*
      Now we apply our rules and draw the pagination object.
      We're actually saving the code to a variable in case we want to draw it more than once.
     */
    $pagination = '';
    if ($lastpage > 1) {
        $pagination .= '<ul class="pagination">';

        //previous button
        if ($page > 1) {
            $pagination .= '<li><a href="' . $pagestring . $prev . '">« Prev</a></li>';
        } else {
            $pagination .= '<li class="disabled"><span>« Prev</span></li>';
        }

        //pages    
        if ($lastpage < 7 + ($adjacents * 2)) {    //not enough pages to bother breaking it up
            for ($counter = 1; $counter <= $lastpage; $counter++) {
                if ($counter == $page) {
                    $pagination .= '<li class="active"><span>' . $counter . '</span></li>';
                } else {
                    $pagination .= '<li><a href="' . $pagestring . $counter . '">' . $counter . '</a></li>';
                }
            }
        } elseif ($lastpage >= 7 + ($adjacents * 2)) {    //enough pages to hide some
            //close to beginning; only hide later pages
            if ($page < 1 + ($adjacents * 3)) {
                for ($counter = 1; $counter < 4 + ($adjacents * 2); $counter++) {
                    if ($counter == $page) {
                        $pagination .= '<li class="active"><span>' . $counter . '</span></li>';
                    } else {
                        $pagination .= '<li><a href="' . $pagestring . $counter . '">' . $counter . '</a></li>';
                    }
                }
                $pagination .= '<li class="disabled"><span>...</span></li>';
                $pagination .= '<li><a href="' . $pagestring . $lpm1 . '">' . $lpm1 . '</a></li>';
                $pagination .= '<li><a href="' . $pagestring . $lastpage . '">' . $lastpage . '</a></li>';
            }
            //in middle; hide some front and some back
            elseif ($lastpage - ($adjacents * 2) > $page && $page > ($adjacents * 2)) {
                $pagination .= '<li><a href="' . $pagestring . '1">1</a></li>';
                $pagination .= '<li><a href="' . $pagestring . '2">2</a></li>';
                $pagination .= '<li class="disabled"><span>...</span></li>';
                for ($counter = $page - $adjacents; $counter <= $page + $adjacents; $counter++) {
                    if ($counter == $page) {
                        $pagination .= '<li class="active"><span>' . $counter . '</span></li>';
                    } else {
                        $pagination .= '<li><a href="' . $pagestring . $counter . '">' . $counter . '</a></li>';
                    }
                }
                $pagination .= '<li class="disabled"><span>...</span></li>';
                $pagination .= '<li><a href="' . $pagestring . $lpm1 . '">' . $lpm1 . '</a></li>';
                $pagination .= '<li><a href="' . $pagestring . $lastpage . '">' . $lastpage . '</a></li>';
            }
            //close to end; only hide early pages
            else {
                $pagination .= '<li><a href="' . $pagestring . '1">1</a></li>';
                $pagination .= '<li><a href="' . $pagestring . '2">2</a></li>';
                $pagination .= '<li class="disabled"><span>...</span></li>';
                for ($counter = $lastpage - (1 + ($adjacents * 3)); $counter <= $lastpage; $counter++) {
                    if ($counter == $page) {
                        $pagination .= '<li class="active"><span>' . $counter . '</span></li>';
                    } else {
                        $pagination .= '<li><a href="' . $pagestring . $counter . '">' . $counter . '</a></li>';
                    }
                }
            }
        }

        //next button
        if ($page < $counter - 1) {
            $pagination .= '<li><a href="' . $pagestring . $next . '">Next »</a></li>';
        } else {
            $pagination .= '<li class="disabled"><span>Next »</span></li>';
        }
        $pagination .= '</ul>';
    }

    return $pagination;
}

// Return edit tab on post
function tab(Post $p) {
    $user = $_SESSION[config("site.url")]['user'];
    $role = user('role', $user);
    if (isset($p->author)) {
        if ($user === $p->author->pseudo || $role === 'admin' || $role === 'superadmin') {
            echo '<div class="tab"><ul class="nav nav-tabs"><li role="presentation" class="active"><a href="' . $p->url . '">View</a></li><li><a href="' . $p->url . '/edit?destination=post">Edit</a></li></ul></div>';
        }
    } else {
        echo '<div class="tab"><ul class="nav nav-tabs"><li role="presentation" class="active"><a href="' . $p->url . '">View</a></li><li><a href="' . $p->url . '/edit?destination=post">Edit</a></li></ul></div>';
    }
}

// Get the title from file
function get_title_from_file(string $v): string {
    // Get the contents from file and convert it to HTML
    $content = MarkdownExtra::defaultTransform(file_get_contents($v));

    $replaced = substr($v, 0, strrpos($v, '/')) . '/';
    $base = str_replace($replaced, '', $v);

    // Extract the title and body
    return get_content_tag('t', $content, str_replace('-', ' ', str_replace('.md', '', $base)));
}

// Turn an array of posts into a JSON
function generate_json(array $posts) {
    return json_encode($posts);
}

// Create Zip files
function Zip(string $source, string $destination): bool {
    if (!extension_loaded('zip') || !file_exists($source)) {
        return false;
    }

    if (file_exists($destination)) {
        unlink($destination);
    }

    $zip = new ZipArchive();

    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
        return false;
    }

    if (is_dir($source) === true) {

        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($files as $file) {
            $file = str_replace('\\', '/', $file);

            // Ignore "." and ".." folders
            if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..'))) {
                continue;
            }

            if (is_dir($file) === true) {
                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
            } elseif (is_file($file) === true) {
                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
            }
        }
    } elseif (is_file($source) === true) {
        $zip->addFromString(basename($source), file_get_contents($source));
    }

    return $zip->close();
}

// TRUE if the current page is an index page like frontpage, tag index, archive index and search index.
function is_index(): bool {
    $req = $_SERVER['REQUEST_URI'];
    if (stripos($req, '/category/') !== false || stripos($req, '/archive/') !== false || stripos($req, '/tag/') !== false || stripos($req, '/search/') !== false || stripos($req, '/type/') !== false || stripos($req, '/blog') !== false || $req == site_path() . '/' || stripos($req, site_path() . '/?page') !== false) {
        return true;
    } else {
        return false;
    }
}

function replace_href(string $string, Tag $tag, string $class, string $url) {

    // Disable libxml errors and allow to fetch error information as needed
    libxml_use_internal_errors(true);

    // Load the HTML in DOM
    $doc = new DOMDocument();
    $doc->loadHTML($string);
    // Then select all anchor tags
//    $all_anchor_tags = $doc->getElementsByTagName($tag->value);
//    foreach ($all_anchor_tags as $_tag) {
//        if ($_tag->getAttribute('class') == $class) {
//            // If match class get the href value
//            $old = $_tag->getAttribute('href');
//            $uno = mb_convert_encoding($old, 'ISO-8859-1', 'UTF-8');
//            $new = $_tag->setAttribute('href', $url . $uno); //FIXME $new unused?
//        }
//    }
    $duo = mb_convert_encoding($doc->saveHTML($doc->documentElement), 'ISO-8859-1', 'UTF-8');
    return preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $duo);
}

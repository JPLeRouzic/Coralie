<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);


// Add static page
function add_page(Title $title, string $url, string $content, Desc $description1 = null) {
    $post_title = safe_html($title->value);
    $post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), $url));
    $description = safe_html($description1->value);
    if ($description1 !== null) {
        $post_description = "\n<!--d " . $description . " d-->";
    } else {
        $post_description = "";
    }
    $post_content = '<!--t ' . $post_title . ' t-->' . $post_description . "\n\n" . $content;
    if (!empty($post_title) && !empty($post_url)) {
        $post_content = stripslashes($post_content);

        $filename = $post_url . '.md';
        $dir = 'content/static/';
        if (is_dir($dir)) {
            file_put_contents($dir .
                    $filename, print_r($post_content, true));
        } else {
            mkdir($dir, 0775, true);
            file_put_contents($dir .
                    $filename, print_r($post_content, true));
        }
        $redirect = site_url() . 'admin';
        header("Location: $redirect");
    }
}

// Add static sub page
function add_sub_page(Title $title, string $url, string $content, string $static, Desc $description1 = null) {
    $post_title = safe_html($title->value);
    $post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), $url));
    $description = safe_html($description1->value);
    if ($description1 !== null) {
        $post_description = "\n<!--d " . $description . " d-->";
    } else {
        $post_description = "";
    }
    $post_content = '<!--t ' . $post_title . ' t-->' . $post_description . "\n\n" . $content;
    if (!empty($post_title) && !empty($post_url)) {
        $post_content = stripslashes($post_content);

        $filename = $post_url . '.md';
        $dir = 'content/static/' . $static . '/';
        if (is_dir($dir)) {
            file_put_contents($dir .
                    $filename, print_r($post_content, true));
        } else {
            mkdir($dir, 0775, true);
            file_put_contents($dir .
                    $filename, print_r($post_content, true));
        }
        $redirect = site_url() . 'admin';
        header("Location: $redirect");
    }
}

// Edit static page and sub page
function edit_page(Title $title, string $url, string $content, string $oldfile, 
        string $destination = null, Desc $description1 = null, string $static = null) {
    $dir = substr($oldfile, 0, strrpos($oldfile, '/'));
    $post_title = safe_html($title->value);
    $post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), $url));
    $description = safe_html($description1->value);
    if ($description1 !== null) {
        $post_description = "\n<!--d " . $description . " d-->";
    } else {
        $post_description = '';
    }
    $post_content = '<!--t ' . $post_title . ' t-->' . $post_description . "\n\n" . $content;
    if (!empty($post_title) && !empty($post_url)) {
        $post_content = stripslashes($post_content);

        $newfile = $dir . '/' . $post_url . '.md';
        if ($oldfile === $newfile) {
            file_put_contents($oldfile, print_r($post_content, true));
        } else {
            rename($oldfile, $newfile);
            file_put_contents($newfile, print_r($post_content, true));
            if (empty($static)) {
                $path = pathinfo($oldfile);
                $old = substr($path['filename'], strrpos($path['filename'], '/'));
                if (is_dir($dir . '/' . $old)) {
                    rename($dir .
                            '/' .
                            $old, $dir .
                            '/' .
                            $post_url);
                }
            }
        }
        if (!empty($static)) {
            $posturl = site_url() . $static . '/' . $post_url;
        } else {
            $posturl = site_url() . $post_url;
        }
        if ($destination == 'post') {
            header("Location: $posturl");
        } else {
            $redirect = site_url() . $destination;
            header("Location: $redirect");
        }
    }
}

// Edit user profile
function edit_profile(Title $title, string $content, string $user) {
    $user_title = safe_html($title->value);
    $user_content = '<!--t ' . $user_title . ' t-->' . "\n\n" . $content;
    if (!empty($user_title)) {
        $user_content = stripslashes($user_content);

        $dir = 'content/users/' . $user . '/';
        $filename = 'content/users/' . $user . '/author.md';
        if (is_dir($dir)) {
            file_put_contents($filename, print_r($user_content, true));
        } else {
            mkdir($dir, 0775, true);
            file_put_contents($filename, print_r($user_content, true));
        }
        $redirect = site_url() . 'author/' . $user;
        // ex: $redirect = 'https://csrf.4lima.de/News/author/admin' ;
        header("Location: $redirect");
    }
}

// Edit homepage
function edit_frontpage(Title $title, string $content) {
    $front_title = safe_html($title->value);
    $front_content = '<!--t ' . $front_title . ' t-->' . "\n\n" . $content;
    if (!empty($front_title)) {
        $front_content = stripslashes($front_content);

        $dir = 'content/data/frontpage';
        $filename = 'content/data/frontpage/frontpage.md';
        if (is_dir($dir)) {
            file_put_contents($filename, print_r($front_content, true));
        } else {
            mkdir($dir, 0775, true);
            file_put_contents($filename, print_r($front_content, true));
        }
        $redirect = site_url();
        header("Location: $redirect");
    }
}

// Delete blog post FIXME $str, $dt
function delete_post(string $file, string $destination) {
    if (!is_logged()) {
        return null;
    }
    $deleted_content = $file;
    // Get cache file
    $arr = explode('_', $file);
    $replaced = substr($arr[0], 0, strrpos($arr[0], '/')) . '/';
    $str = explode('/', $replaced);
    $dt = str_replace($replaced, '', $arr[0]);

    if (!empty($deleted_content)) {
        unlink($deleted_content);
        if ($destination == 'post') {
            $redirect = site_url();
            header("Location: $redirect");
        } else {
            $redirect = site_url() . $destination;
            header("Location: $redirect");
        }
    }
}

// Delete static page FIXME $menu $url
function delete_page(string $file, string $destination) {
    if (!is_logged()) {
        return null;
    }
    $deleted_content = $file;

    if (!empty($deleted_content)) {
        unlink($deleted_content);
        if ($destination == 'post') {
            $redirect = site_url();
            header("Location: $redirect");
        } else {
            $redirect = site_url() . $destination;
            header("Location: $redirect");
        }
    }
}

// Get recent posts by user
function get_user_posts() {
    if (!isset($_SESSION)) {
                header("location: ");
    }

    if (isset($_SESSION[config("site.url")]['user'])) {
        $posts = recent_posts(8);
        if (!empty($posts)) {
            echo '<table class="post-list">';
            echo '<tr class="head"><th>Title</th><th>Published</th>';
            if (config("views.counter") == "true") {
                echo '<th>Views</th>';
            }
            echo '<th>Tag</th><th>Operations</th></tr>';
            $i = 0;
            $len = count($posts);

            foreach ($posts as $p) {
                if ($i == 0) {
                    $class = 'item first';
                } elseif ($i == $len - 1) {
                    $class = 'item last';
                } else {
                    $class = 'item';
                }
                $i++;
                echo '<tr class="' . $class . '">';
                echo '<td><a target="_blank" href="' . $p->url . '">' . $p->title->value . '</a></td>';
                echo '<td>' . date('d F Y', $p->date) . '</td>';
                if (config("views.counter") == "true") {
                    echo '<td>' . $p->views . '</td>';
                }
                echo '<td>' . $p->tag->value . '</td>';
                echo '<td><a href="' . $p->url . '/edit?destination=admin">Edit</a> <a href="' . $p->url . '/delete?destination=admin">Delete</a></td>';
                echo '</tr>';
            }
            echo '</table>';
        }
    }
}

// Get all static pages
function get_user_pages() {
    if (isset($_SESSION[config("site.url")]['user'])) {
        $posts = get_static_post('');
        if (!empty($posts)) {
            krsort($posts);
            echo '<table class="post-list">';
            echo '<tr class="head"><th>Title</th>';
            if (config("views.counter") == "true") {
                echo '<th>Views</th>';
            }
            echo '<th>Operations</th></tr>';
            $i = 0;
            $len = count($posts);

            foreach ($posts as $p) {
                if ($i == 0) {
                    $class = 'item first';
                } elseif ($i == $len - 1) {
                    $class = 'item last';
                } else {
                    $class = 'item';
                }
                $i++;
                echo '<tr class="' . $class . '">';
                echo '<td><a target="_blank" href="' . $p->url . '">' . $p->title->value . '</a></td>';
                if (config("views.counter") == "true") {
                    echo '<td>' . $p->views . '</td>';
                }
                echo '<td><a href="' . $p->url . '/add?destination=admin">Add Sub</a> <a href="' . $p->url . '/edit?destination=admin">Edit</a> <a href="' . $p->url . '/delete?destination=admin">Delete</a></td>';
                echo '</tr>';
                $shortUrl = substr($p->url, strrpos($p->url, "/") + 1);
                $subPages = get_static_sub_post($shortUrl, null);

                foreach ($subPages as $sp) {
                    echo '<tr class="' . $class . '">';
                    echo '<td> &raquo;<a target="_blank" href="' . $sp->url . '">' . $sp->title->value . '</a></td>';
                    if (config("views.counter") == "true") {
                        echo '<td>' . $sp->views . '</td>';
                    }
                    echo '<td><a href="' . $sp->url . '/edit?destination=admin">Edit</a> <a href="' . $sp->url . '/delete?destination=admin">Delete</a></td>';
                    echo '</tr>';
                }
            }
            echo '</table>';
        }
    }
}

// Get all available zip files
function get_backup_files() {
    if (isset($_SESSION[config("site.url")]['user'])) {
        $files = get_zip_files();
        if (!empty($files)) {
            krsort($files);
            echo '<table class="backup-list">';
            echo '<tr class="head"><th>Filename</th><th>Date</th><th>Operations</th></tr>';
            $i = 0;
            $len = count($files);

            foreach ($files as $file) {
                if ($i == 0) {
                    $class = 'item first';
                } elseif ($i == $len - 1) {
                    $class = 'item last';
                } else {
                    $class = 'item';
                }
                $i++;
                // Extract the date
                $arr = explode('_', $file);
                // Replaced string
                $replaced = substr($arr[0], 0, strrpos($arr[0], '/')) . '/';
                $name = str_replace($replaced, '', $file);
                $date = str_replace('.zip', '', $arr[1]);
                $t = str_replace('-', '', $date);
                $time = new DateTime($t);
                $timestamp = $time->format("D, d F Y, H:i:s");
                $url = site_url() . $file;
                echo '<tr class="' . $class . '">';
                echo '<td>' . $name . '</td>';
                echo '<td>' . $timestamp . '</td>';
                echo '<td><a target="_blank" href="' . $url . '">Download</a> <form method="GET"><input type="hidden" name="file" value="' . $file . '"/><input type="submit" name="submit" value="Delete"/></form></td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo 'No available backup!';
        }
    }
}

<?php

/*
 * This files is about adding or editing content (post, draft, etc) in the context of admin user
 */

// Add content on file system
function add_content(Title $title, Tag $tag, string $url, string $content,
        string $user, string $description1,
        string|null $draft, string $category, Type $type1, string $media = '') {

    // Find who is this author
    $author = get_author($user);
    // If it does not exist, 
    if (!isset($author)) {
        render('no-author', array(
            'title' => blog_title() ,
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'bodyclass' => 'no-author',
            'type' => 'is_frontpage',
            'is_front' => false,
                )); 

        die;
    }

    $type = $type1->value;
    $post_date = date('Y-m-d-H-i-s');
    $post_title = safe_html($title->value);
    $post_media = preg_replace('/\s\s+/', ' ', strip_tags($media));
    $pt = safe_tag($tag->value);
    $post_tag1 = strtolower(preg_replace(array('/[^a-zA-Z0-9,. \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), $pt));
    $post_tagmd1 = preg_replace(array('/[^a-zA-Z0-9,. \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', ' ', ''), $pt);
    $post_tag = rtrim($post_tag1, ',');
    $post_tagmd = rtrim($post_tagmd1, ',');
    $post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), $url));
    $description = safe_html($description1);
    if ($description1 !== null) {
        $post_description = "\n<!--d " . $description . " d-->";
    } else {
        $post_description = "";
    }
        $valuemd = "\n<!--tag " . $post_tagmd . " tag-->";

        $post_media = "\n<!--" . $type . " " . $post_media . " " . $type . "-->";

        $post_content = "<!--t " . $post_title . " t-->" . $post_description . $valuemd . $post_media . "\n\n" . $content;
    if (!empty($post_title) && !empty($post_tag) && !empty($post_url)) {
        $post_content = stripslashes($post_content);

        /*
         * Create file name, it is based on:
         *     - the current date
         *     - the tags, ex 2019-02-01-06-38-41_gene-therapy,sma,tdp-43,avxs-101,a-plea-for-als.md
         *     - the title or url if provided by the author
         */
        $filename = $post_date . '_' . $post_tag . '_' . $post_url . '.md';
        /*
         * Now store the new post on file system.
         * Their storage place depends on:
         *     - the user pseudonym
         *     - the post category
         *     - the post type
         */
        if (empty($draft)) {
            $dir = 'content/users/' . $user . '/blog/' . $category . '/' . $type . '/';
        } else {
            $dir = 'content/users/' . $user . '/blog/' . $category . '/draft/';
        }
        if (is_dir($dir)) {
            file_put_contents($dir .
                    $filename, print_r($post_content, true));
        } else {
            mkdir($dir, 0775, true);
            file_put_contents($dir .
                    $filename, print_r($post_content, true));
        }
        save_tag_i18n(new Tag($post_tag), $post_tagmd);

        /*
         * Now update the recent.cache file.
         * This file contains all recent posts, it is convenient for statistics on recent files.
         * It is managed as a FIFO with 40 elements.
         */
        $posts_recent = array();
        $filerecent = "content/widget/recent.cache";
        if (file_exists($filerecent)) {
            $uno = file_get_contents($filerecent);
            $posts_recent = unserialize($uno);
            $newpost = new Post();
            $newpost->tag = $tag;
            $newpost->url = $url;
            $newpost->body = $content;
            $newpost->author = $author;
            $newpost->description = new Desc($description1);
            $newpost->type = $type1;
            $newpost->catg = get_category_info($category);

            $posts_recent[] = $posts_recent;
            while (count($posts_recent) > 40) {
                unset($posts_recent[0]);
            }
            $tmp = serialize($posts_recent);
            file_put_contents($filename, print_r($tmp, true));
        }

        if (empty($draft)) {
            $redirect = site_url() . 'admin/mine';
        } else {
            $redirect = site_url() . 'admin/draft';
        }
        header("Location: $redirect");
    }
}

// Edit content on file system
function edit_content(Title $title, Tag $tag, string $url, string $content,
        string $oldfile, string $destination,
        Desc $description1, $date, $media, $revertPost, $publishDraft, $category, $type) {
    if (!$destination) {
        $destination = '';
    }
    if (!$media) {
        $media = '';
    }
    $oldurl = explode('_', $oldfile);
    $dir = explode('/', $oldurl[0]);
    $olddate = date('Y-m-d-H-i-s', strtotime($date));
    if ($date !== null) {
        $oldurl[0] = substr($oldurl[0], 0, strrpos($oldurl[0], '/')) . '/' . $olddate;
    }
    $post_title = safe_html($title->value);
    $post_media = preg_replace('/\s\s+/', ' ', strip_tags($media));
    $pt = safe_tag($tag->value);
    $post_tag1 = strtolower(preg_replace(array('/[^a-zA-Z0-9,. \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), $pt));
    $post_tagmd1 = preg_replace(array('/[^a-zA-Z0-9,. \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', ' ', ''), $pt);
    $post_tag = rtrim($post_tag1, ',');
    $post_tagmd = rtrim($post_tagmd1, ',');
    $post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), $url));
    $description = safe_html($description1->value);

    if ($description1 !== null) {
        $post_description = "\n<!--d " . $description . " d-->";
    } else {
        $post_description = "";
    }

    $valuemd = "\n<!--tag " . $post_tagmd . " tag-->";
        $post_media = "\n<!--" . $type . " " . $post_media . " " . $type . "-->";

    $post_content = "<!--t " . $post_title . " t-->" . $post_description . $valuemd . $post_media . "\n\n" . $content;
    if (!empty($post_title) && !empty($post_tag) && !empty($post_url)) {
        $newfile = '';
        if (!empty($revertPost) || !empty($publishDraft)) {
            // It's either a post that must be either reverted as a draft, or updated
            // or it's a draft, that must be either published or reverted as a draft
            update_posts_files($revertPost, $publishDraft, $dir, $category, $type, $olddate,
                    $post_tag, $post_url, $post_content,
                    $oldfile, $newfile);
        } else {
            if ($dir[3] === $category) {
                // $dir[3] === $category
                $newfile = $oldurl[0] . '_' . $post_tag . '_' . $post_url . '.md';
                if ($oldfile === $newfile) {
                    file_put_contents($oldfile, print_r($post_content, true));
                } else {
                    rename($oldfile, $newfile);
                    file_put_contents($newfile, print_r($post_content, true));
                }
            } else {
                // $dir[3] != $category
                // AND
                // NOT a post nor a draft
                update_posts_files($revertPost, $publishDraft, $dir, $category, $type, $olddate,
                        $post_tag, $post_url, $post_content,
                        $oldfile, $newfile);
            }
        }
        if (!empty($publishDraft)) {
            $dt = $olddate;
            $t = str_replace('-', '', $dt);
            $time = new DateTime($t);
            $timestamp = $time->format("Y-m-d");
        } else {
            $replaced = substr($oldurl[0], 0, strrpos($oldurl[0], '/')) . '/';
            $dt = str_replace($replaced, '', $oldurl[0]);
            $t = str_replace('-', '', $dt);
            $time = new DateTime($t);
            $timestamp = $time->format("Y-m-d");
        }
        // The post date
        $postdate = strtotime($timestamp);
        // The post URL
        if (config('permalink.type') == 'post') {
            $posturl = site_url() . 'post/' . $post_url;
        } else {
            $posturl = site_url() . date('Y/m', $postdate) . '/' . $post_url;
        }
        save_tag_i18n(new Tag($post_tag), $post_tagmd);

        if ($destination == 'post') {
            if (!empty($revertPost)) {
                $drafturl = site_url() . 'admin/draft';
                header("Location: $drafturl");
            } else {
                header("Location: $posturl");
            }
        } else {
            if (!empty($publishDraft)) {
                header("Location: $posturl");
            } elseif (!empty($revertPost)) {
                $drafturl = site_url() . 'admin/draft';
                header("Location: $drafturl");
            } else {
                $redirect = site_url() . $destination;
                header("Location: $redirect");
            }
        }
    }
}

/*
 *  Write the new post and remove the draft
 * 
 * array(7) { 
 * [0]=> string(7) "content" 
 * [1]=> string(5) "users" 
 * [2]=> string(5) "admin" 
 * [3]=> string(4) "blog" 
 * [4]=> string(13) "Uncategorized" 
 * [5]=> string(4) "post" 
 * [6]=> string(19) "2022-08-19-23-32-49" 
 * }
 */

function update_posts_files(&$revertPost, &$publishDraft, array &$dir, string &$category, string &$type, string &$olddate,
        string &$post_tag, string &$post_url, string &$post_content,
        string &$oldfile, string &$newfile) {
    $dirBlog = $dir[0] . '/' . $dir[1] . '/' . $dir[2] . '/' . $dir[3] . '/' . $category . '/' . $type . '/';
    $dirDraft = $dir[0] . '/' . $dir[1] . '/' . $dir[2] . '/' . $dir[3] . '/' . $category . '/draft/';
    if ($dir[4] == 'draft') {
        $filename = $dirDraft . $olddate . '_' . $post_tag . '_' . $post_url . '.md';
    } else {
        $filename = $dirBlog . $olddate . '_' . $post_tag . '_' . $post_url . '.md';
    }
    if (!is_dir($dirBlog)) {
        mkdir($dirBlog, 0775, true);
    }
    if (!is_dir($dirDraft)) {
        mkdir($dirDraft, 0775, true);
    }

    if (!empty($revertPost)) {
        /*
         *  post => draft
         * We must remove the post file and create the draft file
         */
        unlink($oldfile);
        $filename = $dirDraft . $olddate . '_' . $post_tag . '_' . $post_url . '.md';
        file_put_contents($filename, print_r($post_content, true));
    } else if (!empty($publishDraft)) {
        /*
         * draft => post
         * post => post (update)
         * We must remove the draft file and create the post file
         */
        unlink($oldfile);
        $filename = $dirBlog . $olddate . '_' . $post_tag . '_' . $post_url . '.md';
        file_put_contents($filename, print_r($post_content, true));
    } else {
        echo '<br>Error update_posts_files 287' ;
        die() ;
    }

    $newfile = $olddate . '_' . $post_tag . '_' . $post_url . '.md';
}

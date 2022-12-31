<?php

// Add tags
function add_tag(string $title, string $url, string $content, string $description = null) {
    $post_title = safe_html($title);
    $post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), $url));
    $description = safe_html($description);
    if ($description !== '') {
        $post_description = "\n<!--d " . $description . " d-->";
    } else {
        $post_description = "";
    }
    $post_content = '<!--t ' . $post_title . ' t-->' . $post_description . "\n\n" . $content;

    if (!empty($post_title) && !empty($post_url)) {

        $post_content = stripslashes($post_content);

        $filename = $post_url . '.md';
        $dir = 'content/data/tag/';
        if (is_dir($dir)) {

            file_put_contents($dir .
                    $filename, print_r($post_content, true));
        } else {

            mkdir($dir, 0775, true);
            file_put_contents($dir .
                    $filename, print_r($post_content, true));
        }
        $redirect = site_url() . 'admin/tags';
        header("Location: $redirect");
    }
}

// Edit tag
function edit_tag(Title $title, string $url, string $content, string $oldfile, 
        string $destination = null, Desc $description = null) {
    $dir = substr($oldfile, 0, strrpos($oldfile, '/'));
    $post_title = safe_html($title->value);
    $post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), $url));
    $description->value = safe_html($description->value);
    if ($description->value !== null) {
        $post_description = "\n<!--d " . $description->value . " d-->";
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
        }
        rename_tag_folder($post_url, $oldfile); 
        if ($destination == 'post') {
//            header("Location: $posturl"); // FIXME ($posturl)
            $redirect = site_url() . 'admin/categories';
            header("Location: $redirect");
        } else {
            $redirect = site_url() . $destination;
            header("Location: $redirect");
        }
    }
}

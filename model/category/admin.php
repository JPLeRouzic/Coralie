<?php 

// Add a category
function add_category(string $title, string $url, string $content, string $description1 = null) {
    $post_title = safe_html($title);
    $post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), $url));
    $description = safe_html($description1);
    if($description !== '') {
        $post_description = "\n<!--d " . $description . " d-->";
    } else {
        $post_description = "";
    }
    $post_content = '<!--t ' . $post_title . ' t-->' . $post_description . "\n\n" . $content;
    if(!empty($post_title)&& !empty($post_url)) {
             $post_content = stripslashes($post_content);

        $filename = $post_url . '.md';
        $dir = 'content/data/category/';
        if(is_dir($dir)) {
            file_put_contents($dir . 
                              $filename, print_r($post_content, true));
        } else {
            mkdir($dir, 0775, true);
            file_put_contents($dir . 
                              $filename, print_r($post_content, true));
        }
        $redirect = site_url(). 'admin/categories';
        header("Location: $redirect");
    }
}

// Edit a category
function edit_category(Title $title, string $url, string $content, string $oldfile, string $destination = null, Desc $description1 = null) {
    $dir = substr($oldfile, 0, strrpos($oldfile, '/'));
    $post_title = safe_html($title->value);
    $post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), $url));
    $description = safe_html($description1->value);
    if($description !== '') {
        $post_description = "\n<!--d " . $description . " d-->";
    } else {
        $post_description = '';
    }
    $post_content = '<!--t ' . $post_title . ' t-->' . $post_description . "\n\n" . $content;
    if(!empty($post_title)&& !empty($post_url)) {
            $post_content = stripslashes($post_content);

        $newfile = $dir . '/' . $post_url . '.md';
        if($oldfile === $newfile) {
            file_put_contents($oldfile, print_r($post_content, true));
        } else {
            rename($oldfile, $newfile);
            file_put_contents($newfile, print_r($post_content, true));
        }
        
//         echo '<br>description1->value: ' . $description1->value . ', description: ' . $description . ', post_description: ' . $post_description ;
//        rename_category_folder($post_url, $oldfile);
        if($destination == 'post') {
//            header("Location: $posturl"); // FIXME ($posturl)
                   $redirect = site_url() . 'admin/categories';
        header("Location: $redirect");
        } else {
            $redirect = site_url(). $destination;
            header("Location: $redirect");
        }
    }
}

// rename category folder FIXME to be removed?
function rename_category_folder(string $string, string $old_url) {

    $old = str_replace('.md', '/', $old_url);
    $url1 = substr($old, 0, strrpos($old, '/'));
    $ostr = explode('/', $url1);
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
var_dump($file) ;

    foreach ($file as $f) {
        if (is_dir($f[0])) {
            rename($f[0], $f[1]);
        }
    }
}


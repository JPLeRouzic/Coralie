<?php

/*
 * This enables to delete a message belonging to a thread of messages
 */

require_once("db.php");

global $thdata, $log_file;

$thread = $_GET['post'];
$itemnb = $_GET['item'];
$return = $_GET['return']; // Return to admin interface

if (file_exists("$thdata/$thread.dat")) {
    $post = file_get_contents("$thdata/$thread.dat");
    $posts = explode('{', $post);
    unset($posts[$itemnb]);
    $post = implode('{', $posts);
    $post = substr($post, 0, -1) ;
    if(strlen($post) > 0) {
        $post = $post . ']';
        file_put_contents("$thdata/$thread.dat", $post);
    } else {
        unlink("$thdata/$thread.dat") ;
    }
}

if($return) {   
    header("Location: https://padiracinnovation.org/News/admin/stub");
} else {
    header("Location: ./view.php?post=" . $thread);
    }
die();

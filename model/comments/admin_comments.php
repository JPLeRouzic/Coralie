<?php

// Show news search engine page
ini_set('display_errors', 'On');
error_reporting(E_ALL);

include_once("db.php");
include_once("config.php");
include_once("Parsedown.php");
include_once("ParsedownExtra.php");
include_once("functions.php");

// Show comments page
route('GET', '/admin/commenti', function () {
    admin_comments();
}
);

function admin_comments() {
    if (is_logged()) {
        echo '<html><body>';
        admin_page_comments();
        echo '</body></html>';
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
}

function admin_page_comments() {
    $config = array(
        "thread_data" => __DIR__ . '/../../content/comments/forum_data/threads', //Folder to store thread data in, make sure to give proper permissions (0744) and the owner of the folder must be apache's user (Or nginx's user)
        "perPageThread" => 10 //Default reply amount to show in each thread (View mode)
    );
    if (is_logged()) {
        $files1 = scan_dir($config['thread_data']);
        if ($files1 == false) {
            echo '<br>There is no comments';
            die();
        }
        $threads = array();

        // Get only the .dat files
        foreach ($files1 as $file) {
            if( (strcmp($file, '.dat') != 0) && (strcmp($file, '.dat.dat') != 0) ) {
                if (strpos($file, '.dat') >= 0 &&
                        strpos($file, '.dat') < strlen($file)) {
                    // found
                    $threads[] = $file;
                } else {
                    // not found
                }
            }
        }
        
//        echo '<br>threads:<br>' ; var_dump($threads) ;

        // Now $threads is an array containing a .dat file in each cell
        foreach ($threads as $thread_id) {
            admin_user_comments($config, $thread_id);
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die();
}

function is_dir_empty($dir) {
    if (!is_readable($dir)) {
        return false;
    }
    $nbcomments = count(scandir($dir));
    if ($nbcomments == 2) {
        // . and ..
        return false;
    } else {
        return $nbcomments;
    }
}

function admin_user_comments($config, $post_id) {
    $discfilenm = __DIR__ . '/../../content/comments/forum_data/threads/' . $post_id;
    $post_content = file_get_contents($discfilenm);
    if(strcmp($post_content, "") == 0) {
        return ;
        }
    $postmini = explode('{"post":', $post_content) ;
    $postnb = count($postmini) ;
    echo '<br>Thread: ' . ($postnb - 1) ;
    for($postidx = 1; $postidx < ($postnb); $postidx++) {
        echo '<br>Post: ' . $postidx . '<br>';
        echo $postmini[$postidx] . '<br>';
        // var_dump($post_content) ;

        /*
         * Delete button
         $post_id = part of the URL of the post such as:
         certains-cas-de-maladie-d'alzheimer-sont-ils-déclenchés-par-une-forme-de-diabète-dans-le-cerveau-?.dat
         */
         
        // remove the '.dat' at the end of $post_id
        $post_id = substr($post_id, 0, -4);
//        echo '<br><form action="https://padiracinnovation.org/News/content/comments/delete.php?type=delete&post=' . $post_id . '&item=' . $postidx . '&return=true' . '" method="POST">' ;
        echo '<br><form action="content/comments/delete.php?type=delete&post=' . $post_id . '&item=' . $postidx . '&return=true' . '" method="POST">' ;
       echo '<input type="submit" value="Delete">
              </form>' ;        
 
    }
}

?>

<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

/*
 * This enables to read a thread of messages
 */
include_once("config.php");
include_once("Parsedown.php");
include_once("ParsedownExtra.php");
 
session_start();

// include_once("header.php");

$usdata = $config['user_data'];
$thdata = $config['thread_data'];

if (isset($_GET['post'])) {
    $post_id = $_GET['post'];
} else {
    header("Location: ./index.php");
}

comnt_view($thdata, $config, $post_id);

function comnt_view($thdata, $config, $post_id) {

    $p = array();

    if (file_exists("$thdata/$post_id.lock") || file_exists("$thdata/$post_id.lockadmin")) {
        echo '<div class="alert alert-warning">' . "thread.locked" . '</div>';
    }

    /* get cookie to recognize eventual user
     * as we do not authenticate users, when an unauthenticated user clicks on "reply"
     * we set a cookie with a random name
     * at every future visit, the cookie is read and interpreted as if the user was authenticated.
     * if the user delete this cookie, they can't edit their own replies
     * after 30 days the cookie becomes invalid
     */
    if (isset($_COOKIE['username'])) {
        $_SESSION['username'] = $_COOKIE['username'];
    } else {
        // Create a user name with date and IP address
        $_SESSION['username'] = 'anonymous_' . date("Y/m/d") . '_' . $_SERVER["REMOTE_ADDR"];
        $cookie_name = "username";
        $cookie_value = $_SESSION['username'];
        setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
    }

    $files1 = scan_dir($config['thread_data']);
//    var_dump($files1) ;
    $totalPages = 0;
    $data = $config['thread_data'];

    // set title
    if (isset($post_id)) {
        if (is_file("$data/$post_id")) {
            $filetime = filemtime("$data/$post_id");
        } else {
            $filetime = date(time());
        }
            echo '<h3 style="background-color:DodgerBlue;">Discussion:</h3>';
    }

    // prepare database
    if (isset($post_id)) {
        /* */
        $discfilenm = __DIR__ . '/../../content/comments/forum_data/threads/' . $post_id . ".dat";
        if (is_file($discfilenm) == false) {

            echo '<h4>Write the first comment!</h4>';
            /*
             * id="type" => case "reply"
             * id="post-id" => contains $post_id, which is the URL of the commented post
             * id="text" => contains the text of the reply
             */
//            echo '<form action="https://padiracinnovation.org/News/content/comments/submit.php" method="POST">
            echo '<form action="https://padiracinnovation.org/News/content/comments/submit.php" method="POST">
                <input style="display: none;" name="type" id="type" value="reply"></input>
                <input style="display: none;" name="post-id" id="post-id" value="';
            echo $post_id;
            echo '"></input>
                <textarea name="text" id="text" rows="10" cols="50%"  cols="50" class="form-control"></textarea><br />
                <button type="submit" class="btn btn-primary pull-right">';
            echo 'Submit'; // FIXME!
            echo '</button>
        </form>';
        } else {
        $comments = array() ;
        foreach($files1 as $filei) {
            $comments[] = file_get_contents(__DIR__ . '/../../content/comments/forum_data/threads/' . $filei) ;
            }
            if (isset($comments)) {
//            foreach($comments as $commentarray) {
                    if (isset($_SESSION['username'])) {
                        if (!file_exists("$thdata/$post_id.lock") && !file_exists("$thdata/$post_id.lockadmin")) {

                            /*
                             * Lock button
                             */
                            if (isAdmin($_SESSION['username'])) {
    //                            echo '&nbsp;&nbsp;<a href="https://padiracinnovation.org/News/content/comments/submit.php?type=lock&post=' . $post_id
                                echo '&nbsp;&nbsp;<a href="https://padiracinnovation.org/News/content/comments/submit.php?type=lock&post=' . $post_id
                                . '" class="btn btn-primary btn-sm">' . "lock.thread"
                                . '</a><br />';
                            }
                        } elseif (!file_exists("$thdata/$post_id.lockadmin")) {

                            /*
                             * Unlock button
                             */
                            if (isAdmin($_SESSION['username'])) {
    //                            echo '&nbsp;&nbsp;<a href="https://padiracinnovation.org/News/content/comments/submit.php?type=unlock&post=' . $post_id
                                echo '&nbsp;&nbsp;<a href="https://padiracinnovation.org/News/content/comments/submit.php?type=unlock&post=' . $post_id
                                . '" class="btn btn-primary btn-sm">' . "unlock.thread"
                                . '</a><br />';
                            }
                        }
                    } else {
                        echo '<br />';
                    }
                    echo '<br>';
                    $page = !empty($_GET['page']) ? (int) $_GET['page'] : 1;

                    if (( isset($_GET['page']) ) && ($_GET['page'] == 'first')) {
                        $page = 1;
                    } elseif (( isset($_GET['page']) ) && ($_GET['page'] == 'last')) {
                        $page = count($comments);
                    }

                    $total = count($comments); //total items in array
                    $limit = $config['perPageThread']; //per page
                    $totalPages = ceil($total / $limit); //calculate total pages
                    $page = max($page, 1); //get 1 page when $_GET['page'] <= 0
                    $page = min($page, $totalPages); //get last page when $_GET['page'] > $totalPages
                    $offset = ($page - 1) * $limit;
                    if ($offset < 0) {
                        $offset = 0;
                    }
                    $commentslice = array_slice($comments, $offset, $limit, true);
                    foreach ($commentslice as $onecommentfull) {
                    
                    $pprime = explode('","', $onecommentfull) ;
                    $onecomment['post'] = $pprime[0] ;
                    $onecomment['time'] = $pprime[1] ;
                    $onecomment['user'] = $pprime[2] ;
                    
                        $item = array_search($onecomment, $commentslice);
                        $item++;
                        $pmd = Parsedown::instance()
                                ->setMarkupEscaped(true) # escapes markup (HTML)
                                ->text($onecomment['post']);
                        echo '<div class="panel panel-default">
      				<div class="panel-heading"><b>' . $onecomment['user'] . '</b> @ '
                        . $onecomment['time'] . '&nbsp;&nbsp;' /* . $edit */ . '<span class="pull-right">#' . $item . '</span></div>
      				<div class="panel-body" style="overflow:auto;word-wrap: break-word;">'
                        . $pmd;

                        /*
                         * Delete button
                         */
                        if (isset($_SESSION['username']) && ((isUser($onecomment['user'], $_SESSION['username'])) || (isAdmin($_SESSION['username'])))) {
    //                        echo '<a href="https://padiracinnovation.org/News/content/comments/delete.php?type=delete&post=' . $post_id
                            echo '<a href="https://padiracinnovation.org/News/content/comments/delete.php?type=delete&post=' . $post_id
                            . '&item=' . $item .
                            '" class="btn btn-danger btn-sm">' . "delete.post" . '</a>';
                        }

                        echo '<!-- <br /></div>
			    </div>';

                        echo '<br /> -->';
                    }
                    /*
                     * Edit button
                     * but it's possible to reply only at end of the list of replies
                     */
                    if (isset($onecomment)) {
                        if (isset($_SESSION['username']) && ( isUser($onecomment['user'], $_SESSION['username']) || isAdmin($_SESSION['username']))) {
                            if (!file_exists("$thdata/$post_id.lock") && !file_exists("$thdata/$post_id.lockadmin")) {

                                echo '&nbsp;&nbsp;';
    //                            echo '<a href="https://padiracinnovation.org/News/content/comments/edit.php?post=';
                                echo '<a href="https://padiracinnovation.org/News/content/comments/edit.php?post=';
                                echo $post_id;
                                echo '&reply_num=' . $item . '"';
                                echo 'class="btn btn-primary btn-sm">';
                                echo "edit.reply";
                                echo '</a>';
                            }
                        }
                    }
                    /*
                     * We do not use login to control who write answers
                     * but it's possible to reply only at end of the list of replies
                     */
                    if (!file_exists("$thdata/$post_id.lock") && !file_exists("$thdata/$post_id.lockadmin")) {
                        echo '&nbsp;&nbsp;';
    //                    echo '<a href="https://padiracinnovation.org/News/content/comments/reply.php?post=';
                        echo '<a href="https://padiracinnovation.org/News/content/comments/reply.php?post=';
                        echo $post_id;
                        echo '" class="btn btn-primary btn-sm">';
                        echo "reply.to.post";
                        echo '</a>';
                    }
//                } // foreach($comments as $commentarray
            } // if(isset($comments
        } // There are previous comments
    }
}

/*          ***********************       */
//Misc functions
function scan_dir($dir) {
    $ignored = array('.', '..', '.svn', '.htaccess');

    $files = array();
    foreach (scandir($dir) as $file) {
        if (in_array($file, $ignored)) {
            continue;
        }
        if (strpos($file, ".name")) {
            continue;
        }
        if (strpos($file, ".lock")) {
            continue;
        }
        if(strpos($file, ".lockadmin")) {
            continue;
        }
        $files[$file] = filemtime($dir . '/' . $file);
    }

    arsort($files);
    $files = array_keys($files);

    return ($files) ? $files : false;
}

function isAdmin($user) {
    if (isset($_SESSION["/News/"]["user"])) {
        $user1 = $_SESSION["/News/"]["user"];

        if ($user1 === 'admin') {
            return true;
        }
    }
    return false;
}

function isUser($owner, $user) {
    if (strcmp($owner, $user) === 0) {
        return true;
    }
    return false;
}



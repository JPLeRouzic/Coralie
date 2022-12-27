<?php

/*
 * This enables to edit a message belonging to a thread of messages
 */
session_start();
require("db.php");
require("config.php");
include("Parsedown.php");
include("ParsedownExtra.php");
require("functions.php");
// include("header.php");

$usdata = $config['user_data'];
$thdata = $config['thread_data'];
echo '<div class="container">';

/* get cookie to recognize eventual user
 * as we do not authenticate users, when an unauthenticated user clicks on "reply"
 * we set a cookie with a random name
 * at every future visit, the cookie is read and interpreted as if the user was authenticated.
 * if the user delee this cookie, they can't edit their own replies
 * after 30 days the cookie becomes invalid
 */

if (isset($_GET['post'])) {
    $post_id = $_GET['post'];
} 

if (isset($_GET['reply_num'])) {
    $reply_num = $_GET['reply_num'];
} else {
    $reply_num = 1;
}

edit($thdata, $config, $post_id, $reply_num);

function edit($thdata, $config, $post_id, $reply_num) {
    echo '<div class="page-header">
        <h1>';
    $to = $post_id;
//    echo sprintf(L("edit.reply.to"), file_get_contents("$thdata/$to"));
    echo '<br /><small>';
    sprintf(L("post.number"), $reply_num);
    echo '</small></h1>
    </div>';

    if (!file_exists("$thdata/" . $post_id . '.dat')) {
        echo L("post.not.exist");
    } else {
        echo '<p>';
        echo L("use.md");
        echo '</p>
        <form action="submit.php" method="POST">
            <input style="display: none;" name="type" id="type" value="edit"></input>
            <input style="display: none;" name="reply_num" id="reply_num" value="';
        echo $reply_num;
        echo '"></input>
            <input style="display: none;" name="post-id" id="post-id" value="';
        echo $post_id;
        echo '"></input>';

        $posts = new Fllat($post_id, $thdata);
        $p = $posts->select();
        $temp_time = "";
        $temp_text = "";
//        if ((isUser($_SESSION['username'])) || (isAdmin($_SESSION['username']))) { FIXME!
        if ((isset($_SESSION['username'])) || (isAdmin($_SESSION['username']))) {
            foreach ($p as $pp) {
                $k = array_search($pp, $p);
                $k++;
//                if ($k == $reply_num) {
		if(isUser($pp['user'], $_SESSION['username']) && $k == $reply_num){
                    $temp_text = $pp['post'];
                    $temp_time = $pp['time'];
                    break;
                }
            }
            echo '<textarea name="text" id="text" rows="10" cols="50%" class="form-control">' . $temp_text . '</textarea><br />
				<input style="display: none;" name="time" id="time" value="' . $temp_time . '"></input>
				<button type="submit" class="btn btn-primary pull-right">' . L("submit") . '</button>
				';
        } else {
            echo L("not.perms.edit.reply");
        }

        echo '</form>
        <script type="text/javascript">
            $(document).ready(function () {';

        echo 'document.title = \'  \n';
        echo $config['title'];
        $to = $post_id;
//        echo " | " . sprintf(L("reply.to.2"), file_get_contents("$thdata/$to"));
        echo ';
            });
        </script>';
    }
}

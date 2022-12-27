<?php

/*
 * This enables to reply to a thread of messages
 */
session_start();
require("db.php");
require("config.php");
include_once("Parsedown.php");
include_once("ParsedownExtra.php");
require("functions.php");
// include_once("header.php");

$usdata = $config['user_data'];
$thdata = $config['thread_data'];
echo '<div class="container">';

if (isset($_GET['post'])) {
    $post_id = $_GET['post'];
} else {
    $post_id = 'is-weight-loss-inevitable-in-als';
}

reply($thdata, $config, $post_id);

function reply($thdata, $config, $post_id) {

    if (!file_exists("$thdata/" . $post_id . '.dat')) {
        file_put_contents("$thdata/" . $post_id . '.dat', '');
    }

    echo '<p>' . L("use.md");
    echo '</p>
        <form action="submit.php" method="POST">
            <input style="display: none;" name="type" id="type" value="reply"></input>
            <input style="display: none;" name="post-id" id="post-id" value="';

    echo $post_id;
    echo '"></input>
            <textarea name="text" id="text" rows="10" cols="50%" class="form-control"></textarea><br />
            <button type="submit" class="btn btn-primary btn-sm pull-right">';
    echo L("submit");
    echo '</button>
        </form>';
}

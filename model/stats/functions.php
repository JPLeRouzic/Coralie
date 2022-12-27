<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

/*
 * Get the page views count 
 * Extract of views.json file format:
 * {
 * "content/users/admin/blog/english/post/2020-07-21-21-23-33_alzheimer_roles-of-gluta....md":348,
 */

function get_views($filepath) {
    static $_views = array();

    if (empty($_views)) {
        $filename = "content/data/views.json";
        if (file_exists($filename)) {
            $_views = json_decode(file_get_contents($filename), true);
        }
    }
    if (isset($_views[$filepath])) {
        return $_views[$filepath];
    } else {
//        echo '<br>File path of ' . $filepath . 'has probably been modified';
    }
    return -1;
}

// Add page views count
function add_view(string $page) {
    $time = new DateTime();
    $timestamp = $time->format("Y-m-d H:i:s");

    /*     * ** stats per page *** */
    $dir = 'content/data/';
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $filename = "content/data/views.json";
    $views = array();
    if (file_exists($filename)) {
        $file_content = file_get_contents($filename);
        $views = json_decode($file_content, true); // When true, JSON objects will be returned as associative arrays
        // Returns the value encoded in JSON in appropriate PHP type. 
        // If the JSON object cannot be decoded it returns NULL
        if ($views === NULL) {
            $date = strtotime($timestamp);
            $filename = "content/data/logs_" . date('Y-m-d', $date);
            file_put_contents($filename, "empty json: " . $page);

            return;
        }
    }
    if (isset($views[$page])) {
        $views[$page]++;
    } else {
        $views[$page] = 1;
    }
    file_put_contents($filename, json_encode($views, JSON_UNESCAPED_UNICODE));

    /*     * ** stats per day *** */
    $date = strtotime($timestamp);

    // Read the file containing the number of access for today
    $filename = "content/stats/" . date('Y-m-d', $date);

    if (file_exists($filename)) {
        // Increment the number of access to the site this day
        $nbAccess = file_get_contents($filename);
        $nbAccess++;

        // save the file
        file_put_contents($filename, $nbAccess);
    } else {
        // The file does not exist, so we are "tomorrow"
        $yesterday = date('Y-m-d', strtotime('-1 days'));

        // Read the file containing the number of access of yesterday
        $yesterfile = "content/stats/" . $yesterday;

        // Get the total for yesterday
        if (is_file($yesterfile)) {
            $nbAccess = file_get_contents($yesterfile);
        } else {
            $nbAccess = 0;
        }

        // Store the new content at the end of the log file
        // FILE_APPEND flag helps to append the content to the end of the file instead of overriding the content.
        file_put_contents('content/stats/logs.txt', "\n; " . $yesterday . ', ' . $nbAccess, FILE_APPEND);

        // Remove the yesterday file
        $path = realpath("content/stats/" . $yesterday);
        if (is_writable($path)) {
            unlink($path);
        }

        // Write the file for today
//	    	$filename = "content/stats/" . date('Y-m-d', $date) ;
        if (!file_exists($filename)) {
            file_put_contents($filename, '1');
        }
    }
}

<!-- backup-start.html -->
<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

$title = config('blog.title');
$name = preg_replace('/[^A-Za-z0-9 ,.-]/u', '', strtolower($title));
$name = str_replace(' ', '-', $name);
$name = str_replace('--', '-', $name);
$name = str_replace('--', '-', $name);
$name = rtrim(ltrim($name, ' \,\.\-'), ' \,\.\-');

$timestamp = date('Y-m-d-H-i-s');
$dir = 'backup';

if (is_dir($dir)) {
    Zip('content/', 'backup/' . $name . '_' . $timestamp . '.zip');
} else {
    mkdir($dir, 0777, true);
    Zip('content/', 'backup/' . $name . '_' . $timestamp . '.zip');
}

$redirect = site_url() . 'admin/backup';
header("Location: $redirect");

?>
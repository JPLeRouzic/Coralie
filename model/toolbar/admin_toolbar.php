<?php

/*
  - Return toolbar
 *
 * See in htmly.php and the model folder, what functions are called from the URLs that are thrown here
 * There are four roles: 
 *     superadmin => administrate all Web sites
 *     admin => administrate one Web site
 *     author => Search and write content
 *     reader => Can read the site and modify their profile
 */

function toolbar() {
    $user = $_SESSION[config("site.url")]['user'];
    // Obtain user's role from config file
    $role = user('role', $user);
    $base = site_url();
    echo<<<EOF
    <link href="{$base}assets/resources/css/toolbar.css" rel="stylesheet" />
EOF
    ;
    echo '<div id="toolbar"><ul>';
    if ($role === 'superadmin') {
        echo '<li><a href="' . $base . 'admin/phpinfo">PHP info</a></li>';
        echo '<li><a href="' . $base . 'admin/stub">Stub function</a></li>';
    }
    if (($role === 'superadmin') || ($role === 'admin')) {
        echo '<li><a href="' . $base . 'admin/popular">Popular</a></li>';
        echo '<li><a href="' . $base . 'admin/trending">Trending</a></li>';
        echo '<li><a href="' . $base . 'admin/unpopular">UnPopular</a></li>';
        echo '<li><a href="' . $base . 'admin/mine">Mine</a></li>';
        echo '<li><a href="' . $base . 'admin/categories">Categories</a></li>';
        echo '<li><a href="' . $base . 'admin/menu">Menu</a></li>';
        echo '<li><a href="' . $base . 'admin/backup">Backup</a></li>';
        echo '<li><a href="' . $base . 'admin/config">Config</a></li>';
        echo '<li><a href="' . $base . 'admin/stats">Stats</a></li>';
    }
    if (($role === 'superadmin') || ($role === 'admin') || ($role === 'author')) {
        echo '<li><a href="' . $base . 'admin/draft">Drafts</a></li>';
        echo '<li><a href="' . $base . 'admin/content">Add content</a></li>';
        echo '<li><a href="' . $base . 'admin/import">Import RSS</a></li>';
    }
    // Ordinary user (reader)
    echo '<li><a href="' . $base . 'edit/profile">Edit profile</a></li>';
    echo '<li><a href="' . $base . 'logout">Logout</a></li>';
    echo '</ul></div>';
}

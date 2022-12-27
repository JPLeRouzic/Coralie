<?php

/*
 *  Get category info files.
 * 
 * a:3:{i:0;s:32:"content/data/category/english.md";i:1;s:31:"content/data/category/中文.md";i:2;s:33:"content/data/category/francais.md";}
 * array() {
 *      integer, // just an index
 *      string // 'content/data/category/english.md'
 * } etc...
 */

function get_category_files() {
    static $_desc = array();
    /*
    if (empty($_desc)) {
        $url = 'content/index/index-category.txt';
        $_desc = unserialize(file_get_contents($url));
    }
    return $_desc;
*/
    $dir = 'content/data/category' ;
    if (is_dir($dir)) {
        if ($dh = opendir($dir)) {
            while (($filename = readdir($dh)) !== false) {
                if( (strcmp($filename, '.') == 0) || (strcmp($filename, '..') == 0) ) {
                    continue ;
                }
                $_desc[] = $dir . '/' . $filename ;
            }
            closedir($dh);
            return $_desc;
        }
    }
}

// Get category folder.
function get_category_folder() {
    static $_dfolder = array();
    
    // as $_dfolder is a static variable, it is not necessarily empty
    if (empty($_dfolder)) {
        $tmp = glob('content/users/*/blog/*/', GLOB_ONLYDIR);
        if (is_array($tmp)) {
            foreach ($tmp as $dir) {
                $_dfolder[] = $dir;
            }
        }
    }
    return $_dfolder;
}

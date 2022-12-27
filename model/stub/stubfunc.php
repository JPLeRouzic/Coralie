<?php
// Show news search engine page
ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Show stats page
route('GET', '/admin/stub', function() {
    admin_stub();
}
);

function admin_stub() {
    if(is_logged()) {
        echo '<html><body>';
         echo '</body></html>';
    } else {
        $login = site_url(). 'login';
        header("location: $login");
    }
    die;
}



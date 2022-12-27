<?php
// Show news search engine page

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Show stats page
route('GET', '/admin/phpinfo', function () {
    admin_phpinfo(); 
});

function admin_phpinfo() {
    if(is_logged()) {
        echo '<html><body>';
phpinfo() ;
        echo '</body></html>';
    } else {
        $login = site_url(). 'login';
        header("location: $login");
    }
    die;
}

?>

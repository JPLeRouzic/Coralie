<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Load the configuration file
config('source');

// Set the timezone
if (config('timezone')) {
    date_default_timezone_set(config('timezone'));
} else {
    date_default_timezone_set('Asia/Jakarta');
}

// Show Config page
route('GET', '/admin/config', function () {
    get_admin_config();
}
);

// Submitted Config page data
route('POST', '/admin/config', function () {
    post_admin_config();
}
);

// Show Config page
function get_admin_config() {
    if (!isset($_SESSION)) {
        session_start();
    }

    $user = $_SESSION[config("site.url")]['user'];
    $role = user('role', $user);

    if (is_logged()) {
        config('views.root', 'views/admin/views');
        if ( ($role === 'admin') || ($role === 'superadmin') ) {
            render('config', array(
                'title' => 'Config - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'admin-config',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Config'
            ));
        } else {
            render('denied', array(
                'title' => 'Config page - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'type' => 'is_admin-config',
                'is_admin' => true,
                'bodyclass' => 'denied',
                'breadcrumb' => '',
            ));
        }
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
}

// Submitted Config page data
function post_admin_config() {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));
    if (is_logged() && $proper) {
        $newKey = from($_REQUEST, 'newKey');
        $newValue = from($_REQUEST, 'newValue');

        $new_config = array();
        $new_Keys = array();
        if (!empty($newKey)) {
            $new_Keys[$newKey] = $newValue;
        }
        foreach ($_POST as $name => $value) {
            if (substr($name, 0, 8) == "-config-") {
                $name = str_replace("_", ".", substr($name, 8));
                $new_config[$name] = $value;
            }
        }
        save_config($new_config, $new_Keys);
        $login = site_url() . 'admin/config';
        header("location: $login");
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
}

/*
 * Returns the value for some given key in config.ini
 * For example: $view_root = config('views.root');
 * $_config[$key => $value] contains all configurations 
 */
function config($key, $value = null) {
    static $_config = array();

    if (($key === 'source') && (isset($value)) && file_exists($value)) {
        // Réinitialization of $_config[$key => $value]
        // The 'source' config is in a file
        $_config = parse_ini_file($value, true);
    }
    // This asks just for the value of the key passed in parameter
    // The 'source' config is already in memory
    elseif ($value == null) {
        // If it is already available, just read the value of key in memory
        if (isset($_config[$key])) {
            return $_config[$key];
        } else {
            // If it is not available, read the value of key from file
            $value = 'config/config.ini';
            $_config = parse_ini_file($value, true);
        }
    } elseif (isset($key) && ($value != null)) {
        // Change the value of the key (only) in memory
        $_config[$key] = $value;
    }
    else {
        echo 'error config.php 127' ;
        die() ;
    }
}

function save_config($data = array(), $new = array()) {
    global $config_file;

    $string2 = '';
    $string3 = file_get_contents($config_file) . "\n";

    foreach ($data as $word => $value) {
        $value = str_replace('"', '\"', $value);
        $string2 = preg_replace("/^" . $word . " = .+$/m", $word . ' = "' . $value . '"', $string3);
    }
    $string1 = rtrim($string2);
    foreach ($new as $word => $value) {
        $value = str_replace('"', '\"', $value);
        $string1 .= "\n" . $word . ' = "' . $value . '"' . "\n";
    }
    $string = rtrim($string1);
    return file_put_contents($config_file, $string);
}

function site_url() {
    
    if (config('site.url') == null) {
        error(500, 'err_16: [site.url] is not set');
    }

    // Forcing the forward slash
    $uno = config('site.url') ;
    // This function returns a string with '/' stripped from the end of string.
    $duo = rtrim($uno, '/') ;
    return $duo . '/';
}

function site_path() {
    if (config('site.url') == null) {
        error(500, 'err_17: err_1: [site.url] is not set');
    }

    $uno = config('site.url') ;
    $duo = parse_url($uno, PHP_URL_PATH) ;
    $_path = rtrim($duo, '/');

    return $_path;
}

function error(string $code, string $message) {
    file_put_contents('content/stats/errlogs.txt', "\nHTTP code: " . $code . ', message: ' . $message, FILE_APPEND);
    header("HTTP/1.0 {$code} {$message}", true, $code);
    die($message);
}


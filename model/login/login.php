<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

/*
 * This is about managing the posted user's credentials
 * The login page template is in views/admin/views/login.html.php
 * This verifies the login and password, the csrf and the user role
 * If everything is OK a session is set with user's name
 * This session is destroyed only by explicit logoff or closing the browser
 */

// Get submitted login data
route('POST', '/login', function () {

    $properCSRF = (is_csrf_proper(from($_REQUEST, 'csrf_token')));
    $user = from($_REQUEST, 'user');
    $password = from($_REQUEST, 'password');

    // verify the password of this user
    $credential_test = authentication($user, $password);

    $is_role = role($user);

    login_imp($user, $password, $properCSRF/* , $captcha */, $credential_test, $is_role);
}
);

function login_imp(string $user, string $password, bool $is_properCSRF/* , bool $is_captcha */, bool $credential_test, bool $is_role) {

    if ($is_properCSRF && $credential_test && $is_role) {
        // Let's start a session for this user

        config('views.root', 'views/admin/views');

        $_SESSION[config("site.url")]['user'] = $user;
        header('location: admin');
    } else {
        // Something was wrong in authentication, start again

        $message['error'] = '';
        if (empty($user)) {
            $message['error'] .= '<li>User field is required.</li>';
        }
        if (empty($password)) {
            $message['error'] .= '<li>Password field is required.</li>';
        }
        if (!($credential_test)) {
            $message['error'] .= '<li>Password is incorrect.</li>';
        }
        if (!$is_properCSRF) {
            $message['error'] .= '<li>Too much time elapsed since last activity.</li>';
        }
        if (!$is_role) {
            $message['error'] .= '<li>You are not authorized to access this area</li>';
        }
        /*        if (!$is_captcha) {
          $message['error'] .= '<li>reCaptcha not correct.</li>';
          }
         */
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        config('views.root', 'views/admin/views');
        render('login', array(
            'title' => 'Login - ' . blog_title(),
            'description' => 'Login page from ' . blog_title() . '.',
            'canonical' => site_url() . '/login',
            'error' => '<ul>' . $message['error'] . '</ul>',
            'bodyclass' => 'in-login',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Login'
        ));
        die;
    }
}

// Return username.ini value from config file
// It has the following attributes:
// password =  ; a hash of the password
// encryption = password_hash ; the method to make the hash
// role = admin  ; the role in the forum

function user(string $key, string $user = null) {
    $user = 'config/users/' . $user . '.ini';
    static $_config = array();
    if (file_exists($user)) {
        $_config = parse_ini_file($user, true);
        if (!empty($_config[$key])) {
            return $_config[$key];
        }
    }
}

function update_user(string $userName, string $password, string $role) {
    $file = 'config/users/' . $userName . '.ini';
    if (file_exists($file)) {
        file_put_contents($file, "password = " .
                password_hash($password, PASSWORD_DEFAULT) .
                "\n" .
                "encryption = password_hash\n" .
                "role = " .
                $role .
                "\n");
        return true;
    }
    return false;
}

function create_user(string $userName, string $password, string $role = "user") {
    $file = 'config/users/' . $userName . '.ini';
    if (file_exists($file)) {
        return false;
    } else {
        file_put_contents($file, "password = " .
                password_hash($password, PASSWORD_DEFAULT) .
                "\n" .
                "encryption = password_hash\n" .
                "role = " .
                $role .
                "\n");
        return true;
    }
}

// Validate password of this user
function authentication(string $user, string $pass): bool {
    $value_file = 'config/users/' . $user . '.ini';
    if (!file_exists($value_file)) {
        return false;
    }
    $value_enc = user('encryption', $user);
    $value_pass = user('password', $user);

    if ($value_enc === "password_hash") {
        if (password_verify($pass, $value_pass)) {
            if (!isset($_SESSION)) {
                session_start();
            }

            $_SESSION[config("site.url")]['user'] = $user;
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function role(string $user): bool {
    $value_role = user('role', $user);
    if ((strcmp($value_role, 'admin') != 0) && (strcmp($value_role, 'superadmin') != 0)) {
        return false;
    } else {
        return true;
    }
}

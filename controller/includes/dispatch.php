<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

/*
 * This is the main routing function on this framework.
 * It receives URLs and determines which action should be done.
 * For example to access the "admin" menu from a web site root at:
 * https://padiracinnovation.org/News/
 * The URL is:
 * https://padiracinnovation.org/News/admin/import
 * 
  #2 News/model/draft/draft.php(9): get_admin_draft() <---------------
  #3 News/controller/includes/dispatch.php(250): {closure}()         ^<--------------
  #4 News/controller/includes/dispatch.php(309): route('GET', '/admin/draft') ----->^
  #5 News/controller/htmly.php(220): dispatch()
  #6 News/index.php(4): require('/home/webpages/...')
 *
 */

function dispatch() {
    // Store requested URL in $path
    $path = $_SERVER['REQUEST_URI'];

    if (config('site.url') !== null) {
        $preg_quote1 = preg_quote(site_path());
        $preg_quote2 = '@^' . $preg_quote1 . '@';

        // Searches $path for matches to $preg_quote2 and replaces them with ''.
        $path = preg_replace($preg_quote2, '', $path);
    }

    // Split string by 
    $parts = preg_split('/\?/', $path, -1, PREG_SPLIT_NO_EMPTY);

    $uri1 = trim($parts[0], '/');
    $uri = strlen($uri1) ? $uri1 : 'index';

    //  * This will determine if the HTTP verb is a GET or a POST
    $method = $_SERVER['REQUEST_METHOD'];

    // Now route the URL query to the correct function
    // ex: route('POST', '/add/content') 
    route($method, "/{$uri}");
}

function route(string $http_verb, string $pattern, $callback = null) {
    // callback a map to route requests depending on their request type
    // FIXME $route_map will become very large with many posts, it should be cached
    static $route_map = array(
        'GET' => array(), // Array of routes for GET requests
        'POST' => array() // Array of routes for POST requests
    );

    $method = strtoupper($http_verb);

    // If not GET or POST requests, then print error
    if (!in_array($method, array('GET', 'POST'))) {
        if (in_array($method, array('HEAD'))) {
            header('Content-type: text/html; charset=utf-8');
            die();
        } else {
            error(500, 'err_15: Only HEAD, GET and POST are supported');
        }
    }
    // $method is GET or POST 
    $vals = array();

    // a callback was passed, so we create a route definition including
    // - a route
    // - the callback
    if ($callback !== null) {
        // create a route entry for this pattern
        $route_map[$method][$pattern] = array(
            'xp' => route_to_regex($pattern),
            'cb' => $callback
        );
    } else {
        // There is no callback specified
        $vals = null;

        // callback is null, so this is a route invokation. look up the callback.
        foreach ($route_map[$method] as $pat => $obj) {

            // if the requested uri ($pat) has a matching route, let's invoke the cb
            if (!preg_match($obj['xp'], $pattern, $vals)) {
                continue;
            }

            // call middleware
//            middleware($pattern);

            // construct the params for the callback
            $keys1 = array();
            array_shift($vals); // FIXME type incompatible with declaration
            preg_match_all('@:([\w]+)@', $pat, $keys1, PREG_PATTERN_ORDER);
            $keys = array_shift($keys1);
            $argv = array();

            foreach ($keys as $index => $id) {
                $id = substr($id, 1);
                if (isset($vals[$id])) {
                    array_push($argv, trim(urldecode($vals[$id])));
                }
            }

            /* call filters if we have symbols
            if (count($keys)) {
                filter(array_values($keys), $vals);
            } */

            // if cb found, invoke it
            if (is_callable($obj['cb'])) {
                call_user_func_array($obj['cb'], $argv);
            }

            // leave after first match
            break;
        }
    }
}

/*
 * route_to_regex() is only called by route(), an internal function to the dispatcher
 * The callback function has one parameter containing an array of matches. 
 * The first element in the array contains the match for the whole expression while the remaining elements 
 * have matches for each of the groups in the expression.
 */

function route_to_regex(string $route1) {
    // Perform a regular expression search and replace using a callback
    $route = preg_replace_callback(
            '@:[\w]+@i',
            function ($matches) {
                $token = str_replace(':', '', $matches[0]);
                return '(?P<' . $token . '>[a-z0-9_\0-\.]+)';
            },
            $route1);
    return '@^' . rtrim($route, '/') . '$@i';
}



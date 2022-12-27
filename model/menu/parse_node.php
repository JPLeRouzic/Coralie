<?php

function parseNodes($nodes, $child = null, $class = null) {
    if (empty($child)) {
        $ul = '<ul class="nav navbar-nav '.$class.'">';
        foreach ($nodes as $node) {
            if (isset($node->children)) {
                $ul .= parseNode($node, true);
            } else {
                $ul .= parseNode($node);
            }
        }
        $ul .= '</ul>';
        return $ul;
    } else {
        $ul = '<ul class="subnav dropdown-menu" role="menu">';
        foreach ($nodes as $node) {
            if (isset($node->children)) {
                $ul .= parseNode($node, true);
            } else {
                $ul .= parseNode($node);
            }
        }
        $ul .= '</ul>';
        return $ul;
    }
}

function parseNode($node, $child = null) {
    $req = strtok($_SERVER["REQUEST_URI"],'?');
    $url = parse_url(slashUrl($node->slug));
    $su = parse_url(site_url());
    if (empty($child)) {

        if (isset($url['host']) && isset($su['host'])) {
            if ($url['host'] ==  $su['host']) {
                if (slashUrl($url['path']) == slashUrl($req)) {
                    $li = '<li class="item nav-item active '.$node->class.'">';
                } else  {
                    $li = '<li class="item nav-item '.$node->class.'">';
                }
            } else {
                $li = '<li class="item nav-item '.$node->class.'">'; // Link out
            }
        } else {
            if (slashUrl($node->slug) == slashUrl($req)) {
                $li = '<li class="item nav-item active '.$node->class.'">';
            } else {
                $li = '<li class="item nav-item '.$node->class.'">';
            }
        }

        $li .= '<a class="nav-link" href="'.htmlspecialchars(slashUrl($node->slug), FILTER_SANITIZE_URL).'">'.$node->name.'</a>';
        if (isset($node->children)) {
            $li .= parseNodes($node->children, true, null);
        }
        $li .= '</li>';
        return $li;
    } else {

        if (isset($url['host']) && isset($su['host'])) {
            if ($url['host'] ==  $su['host']) {
                if (slashUrl($url['path']) == slashUrl($req)) {
                    $li = '<li class="item nav-item dropdown active '.$node->class.'">';
                } else  {
                    $li = '<li class="item nav-item dropdown '.$node->class.'">';
                }
            } else {
                $li = '<li class="item nav-item dropdown '.$node->class.'">'; // Link out
            }
        } else {
            if (slashUrl($node->slug) == slashUrl($req)) {
                $li = '<li class="item nav-item dropdown active '.$node->class.'">';
            } else {
                $li = '<li class="item nav-item dropdown '.$node->class.'">';
            }
        }

        $li .= '<a class="nav-link dropdown-toggle" data-toggle="dropdown" href="'.htmlspecialchars(slashUrl($node->slug), FILTER_SANITIZE_URL).'">'.$node->name.'<b class="caret"></b></a>';
        if (isset($node->children)) {
            $li .= parseNodes($node->children, true, null);
        }
        $li .= '</li>';
        return $li;
    }
}

function slashUrl($url) {
    return rtrim($url, '/') . '/';
}


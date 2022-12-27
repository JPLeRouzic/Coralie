<?php

// Auto generate menu from static page
function get_menu(string $custom) {
    $posts = get_static_pages();
    $req = $_SERVER['REQUEST_URI'];

    if (!empty($posts)) {

        asort($posts);

        echo '<ul class="nav ' . $custom . '">';
        if ($req == site_path() . '/' || stripos($req, site_path() . '/?page') !== false) {
            echo '<li class="item first active"><a href="' . site_url() . '">' . config('breadcrumb.home') . '</a></li>';
        } else {
            echo '<li class="item first"><a href="' . site_url() . '">' . config('breadcrumb.home') . '</a></li>';
        }

        if ($req == site_path() . '/blog' || stripos($req, site_path() . '/blog?page') !== false) {
            echo '<li class="item active"><a href="' . site_url() . 'blog">' . 'Blog' . '</a></li>';
        } else {
            echo '<li class="item"><a href="' . site_url() . 'blog">' . 'Blog' . '</a></li>';
        }

        $i = 0;
        $len = count($posts);

        foreach ($posts as $indexp => $v) {

            if ($i == $len - 1) {
                $class = 'item last';
            } else {
                $class = 'item';
            }
            $i++;

            // Replaced string
            $replaced = substr($v, 0, strrpos($v, '/')) . '/';
            $base = str_replace($replaced, '', $v);
            $url = site_url() . str_replace('.md', '', $base);

            $title = get_title_from_file($v);

            if ($req == site_path() . "/" . str_replace('.md', '', $base) || stripos($req, site_path() . "/" . str_replace('.md', '', $base)) !== false) {
                $active = ' active';
//                $reqBase = '';
            } else {
                $active = '';
            }

            $subPages = get_static_sub_pages(str_replace('.md', '', $base));
            if (!empty($subPages)) {
                asort($subPages);
                echo '<li class="' . $class . $active . ' dropdown">';
                echo '<a class="dropdown-toggle" data-toggle="dropdown" href="' . $url . '">' . ucwords($title) . '<b class="caret"></b></a>';
                echo '<ul class="subnav dropdown-menu" role="menu">';
                $iSub = 0;
                $countSub = count($subPages);
                foreach ($subPages as $indexsp => $sp) {
                    $classSub = "item";
                    if ($iSub == 0) {
                        $classSub .= " first";
                    }
                    if ($iSub == $countSub - 1) {
                        $classSub .= " last";
                    }
                    $replacedSub = substr($sp, 0, strrpos($sp, '/')) . '/';
                    $baseSub = str_replace($replacedSub, '', $sp);

                    if ($req == site_path() . "/" . str_replace('.md', '', $base) . "/" . str_replace('.md', '', $baseSub)) {
                        $classSub .= ' active';
                    }
                    $urlSub = $url . "/" . str_replace('.md', '', $baseSub);
                    echo '<li class="' . $classSub . '"><a href="' . $urlSub . '">' . get_title_from_file($sp) . '</a></li>';
                    $iSub++;
                }
                echo '</ul>';
            } else {
                echo '<li class="' . $class . $active . '">';
                echo '<a href="' . $url . '">' . ucwords($title) . '</a>';
            }
            echo '</li>';
        }
        echo '</ul>';
    } else {

        echo '<ul class="nav ' . $custom . '">';
        if ($req == site_path() . '/') {
            echo '<li class="item first active"><a href="' . site_url() . '">' . config('breadcrumb.home') . '</a></li>';
        } else {
            echo '<li class="item first"><a href="' . site_url() . '">' . config('breadcrumb.home') . '</a></li>';
        }
        echo '</ul>';
    }
}

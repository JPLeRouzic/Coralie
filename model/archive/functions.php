<?php

// Return archive page.
function get_archive(string $req, int $page, int $perpage): array {
    $posts = get_posts_sorted();

    $tmp = array();

    foreach ($posts as $index => $v) {
        $str = explode('_', $v['basename']);
        if (strpos($str[0], "$req") !== false) {
            $tmp[] = $v;
        }
    }

    if (empty($tmp)) {
        not_found('get_archive 17');
    }

    return get_posts($tmp, $page, $perpage);
}

// Return an archive list, categorized by year and month.
function archive_list() {

    $dir = "content/widget";
    $filename = "content/widget/archive.cache";
    $ar = array();

    if (is_dir($dir) === false) {
        mkdir($dir, 0775, true);
    }

    $posts = get_post_unsorted();
    $by_year = array();
    $col = array();

    if (!empty($posts)) {

        if (!file_exists($filename)) {
            foreach ($posts as $index => $v) {
                /*
                 * array(2) { 
                 *     ["basename"]=> string(66) "2022-01-14-08-49-47_als_muscle-wasting.md" 
                 *     ["dirname"]=> string(37)
                 */

                $arr = explode('_', $v['basename']);

                // Replaced string
                $str = $arr[0];
                $replaced = substr($str, 0, strrpos($str, '/')) . '/';

                $date = str_replace($replaced, '', $arr[0]);
                $data = explode('-', $date);
                $col[] = $data;
            }

            foreach ($col as $row) {

                $y = $row['0'];
                $m = $row['1'];
                $by_year[$y][] = $m;
            }

            $ar = serialize($by_year);
            file_put_contents($filename, print_r($ar, true));
        } else {
            $by_year = unserialize(file_get_contents($filename));
        }

        # Most recent year first
        krsort($by_year);

        # Iterate for display
        $i = 0;

        foreach ($by_year as $year => $months) {
            if ($i == 0) {
                $class = 'expanded';
                $arrow = '&#9660;';
            } else {
                $class = 'collapsed';
                $arrow = '&#9658;';
            }
            $i++;

            $by_month = array_count_values($months);
            # Sort the months
            krsort($by_month);

            $script = <<<EOF
                    if (this.parentNode.className.indexOf('expanded') > -1){this.parentNode.className = 'collapsed';this.innerHTML = '&#9658;';} else {this.parentNode.className = 'expanded';this.innerHTML = '&#9660;';}
EOF;
            echo '<ul class="archivegroup">';
            echo '<li class="' . $class . '">';
            echo '<a href="javascript:void(0)" class="toggle" onclick="' . $script . '">' . $arrow . '</a> ';
            echo '<a href="' . site_url() . 'archive/' . $year . '">' . $year . '</a> ';
            echo '<span class="count">(' . count($months) . ')</span>';
            echo '<ul class="month">';

            foreach ($by_month as $month => $count) {
                $name = date('F', mktime(0, 0, 0, $month, 1, 2010));
                echo '<li class="item"><a href="' . site_url() . 'archive/' . $year . '-' . $month . '">' . $name . '</a>';
                echo ' <span class="count">(' . $count . ')</span></li>';
            }

            echo '</ul>';
            echo '</li>';
            echo '</ul>';
        }
    }
}

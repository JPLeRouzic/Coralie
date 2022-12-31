<?php

// Copyright
function copyright() {
    $blogcp = blog_copyright();
    $credit = '<a href="/News/2019/05/notre-histoire" target="_blank">Notre histoire</a>';

    if (!empty($blogcp)) {
        return $copyright = '<p>' . $blogcp . '</p><p>' . $credit . '</p>';
    } else {
        return $credit = '<p>' . $credit . '</p>';
    }
}



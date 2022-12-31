<?php

function get_breadcrumb(Post $current) {
    return '<!-- Start breadcrumb -->
        <span itemscope="itemscope" itemtype="https://schema.org/BreadcrumbList">
                <span itemprop="itemListElement" itemscope="itemscope" itemtype="https://schema.org/ListItem">
                    <a href="' . site_url() . '" itemprop="url">Home</a>
                    <span content="1" itemprop="position"></span>
                </span>
        </span> 
        <a property="v:title" rel="v:url" href="' . $current->tag->tagb . '">&#187; ' . $current->tag->value . '</a>&#187; '
            . $current->title->value . '<!-- Breadcrumb"s end-->';
}

function get_breadcrumb_cat(Category $catgry) {
    return '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; ' . $catgry->title ;
}

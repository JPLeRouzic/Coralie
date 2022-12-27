<?php

function get_breadcrumb(Post $current) {
/*    
    if (config('blog.enable') === 'true') {
        $blog = '<span itemscope="itemscope" itemtype="https://schema.org/BreadcrumbList">
   <span itemprop="itemListElement" itemscope="itemscope" itemtype="https://schema.org/ListItem">
       <a expr:href="' . site_url() . '" itemprop="url">
         <span itemprop="name">Home</span>
      </a>
      <span content="1" itemprop="position">
   </span>
</span>';
    } else {
        $blog = '';
    }
*/
    
    return '<!-- Start breadcrumb -->
        <span itemscope="itemscope" itemtype="https://schema.org/BreadcrumbList">
                <span itemprop="itemListElement" itemscope="itemscope" itemtype="https://schema.org/ListItem">
                    <a href="' . site_url() . '" itemprop="url">Home</a>
                    <span content="1" itemprop="position"></span>
                </span>
        </span> 
        <a property="v:title" rel="v:url" href="' . $current->catg->url . '">&#187; ' . $current->catg->title . '</a>&#187; ' 
        . $current->title->value . '<!-- Breadcrumb"s end-->';
}

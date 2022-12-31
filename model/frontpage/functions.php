<?php 

// Return frontpage content
function get_frontpage()
{
    $front = new Front();
    
    $filename = 'content/data/frontpage/frontpage.md';

    if (file_exists($filename)) {
        $content = file_get_contents($filename);
        $front->title = get_content_tag('t', $content, 'Welcome') ;
        $front->url = site_url() . 'front';
        // Get the contents and convert it to HTML
        $front->body = MarkdownExtra::defaultTransform(remove_html_comments($content));
    } else {
        $front->title = 'Welcome';
        $front->url = site_url() . 'front';
        $front->body = 'Welcome to our website.';
    }
    
    return $front;
}


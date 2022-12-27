<?php

// Return blog title
function blog_title(): string {
    return config('blog.title');
}

// Return blog tagline
function blog_tagline(): string {
    return config('blog.tagline');
}

// Return blog description
function blog_description(): string {
    return config('blog.description');
}

// Return blog copyright
function blog_copyright(): string {
    return config('blog.copyright');
}

// Return author info. Deprecated
function authorinfo(string $name = null, string $about = null): string {
    if (config('author.info') == 'true') {
        return '<div class="author-info"><h4>by <strong>' . $name . '</strong></h4>' . $about . '</div>';
    }
}


// Strip html comment 
function remove_html_comments(string $content): string  {
    $patterns = array('/(\s|)<!--t(.*)t-->(\s|)/', '/(\s|)<!--d(.*)d-->(\s|)/', '/(\s|)<!--tag(.*)tag-->(\s|)/', '/(\s|)<!--image(.*)image-->(\s|)/', '/(\s|)<!--video(.*)video-->(\s|)/', '/(\s|)<!--audio(.*)audio-->(\s|)/', '/(\s|)<!--link(.*)link-->(\s|)/', '/(\s|)<!--quote(.*)quote-->(\s|)/');
    return preg_replace($patterns, '', $content);
}

// Shorten the string
function shorten(string $string = null, $char = null): string|null {
    if (empty($char) || empty($string)) {
        return $string ;
    }

    if (strlen(strip_tags($string)) < $char) {
        // preg_replace: You need to use the unicode modifier, u, when using unicode in the regex.
        $string = preg_replace('/\s\s+/', ' ', strip_tags($string));
        $string = ltrim(rtrim($string));
        return $string;
    } else {
        $string = preg_replace('/\s\s+/', ' ', strip_tags($string));
        $string = ltrim(rtrim($string));
        $string = substr($string, 0, $char);
        $string = substr($string, 0, strrpos($string, ' '));
        return $string;
    }
}

// return html safe string
// Certain characters have special significance in HTML, and should be represented by HTML entities 
// if they are to preserve their meanings. 
// If you require all input substrings that have associated named entities to be translated, 
// use <code>htmlentities()</code> instead.</p><p>
// If the input can represent characters that are not coded in the final document character set 
// and you wish to retain those characters (as numeric or named entities), both this function and 
// <code>htmlentities()</code> (which only encodes substrings that have named entity equivalents) 
// may be insufficient. You may have to use <code>mb_encode_numericentity()</code> instead.</p><p></p>
function safe_html(string $string): string {
    $string = htmlspecialchars($string, ENT_QUOTES);
    $string = preg_replace('/\r\n|\r|\n/', ' ', $string);
    $string = preg_replace('/\s\s+/', ' ', $string);
    $string = ltrim(rtrim($string));
    return $string;
}

// return tag safe string
function safe_tag(string $string): string {
    $tags = array();
    $string = preg_replace('/[\s-]+/', ' ', $string);
    $string = explode(',', $string);
    $string = array_map('trim', $string);
    foreach ($string as $str) {
        $tags[] = $str;
    }
    $string = implode(',', $tags);
    $string = preg_replace('/[\s_]/', '-', $string);
    return $string;
}



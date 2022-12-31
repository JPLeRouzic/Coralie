<?php  

// Get the teaser
function get_teaser(string $string4, string $url = null, int $char = null): string
{

    $teaserType = config('teaser.type');
    $more = config('read.more');
    
    if(empty($more)) {
        $more = 'Read more';
    }
    
    if(empty($char)) {
        $char = config('teaser.char');
        if(empty($char)) {
            $char = 200;
        }        
    }

    if ($teaserType === 'full') {
//        $readMore = explode('<!--more-->', $string4);
//        if (isset($readMore[1])) {
//            $patterns = array('<a id="more"></a><br>', '<p><a id="more"></a><br></p>');
//            $string7 = str_replace($patterns, '', $readMore[0]);
//            $string8 = replace_href($string7, 'a', 'footnote-ref', $url); // FIXME type incompatible with declaration
//            return $string8 . '<p class="jump-link"><a class="read-more btn btn-cta-secondary" href="'. $url .'#more">' . $more . '</a></p>';
//        } else {
            return $string4;
//        }
    } elseif (strlen(strip_tags($string4)) < $char) {
        $string5 = preg_replace('/\s\s+/', ' ', strip_tags($string4));
        $string6 = ltrim(rtrim($string5));
        return $string6;
    } else {
        $string3 = preg_replace('/\s\s+/', ' ', strip_tags($string4));
        $string2 = ltrim(rtrim($string3));
        $string1 = substr($string2, 0, $char);
        $string = substr($string1, 0, strrpos($string1, ' '));
        return $string;
    }
}


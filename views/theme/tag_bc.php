<?php

function tag_bc(array $a) {
    return '<span itemscope="itemscope" itemtype="https://schema.org/BreadcrumbList">
 <span itemprop="itemListElement" itemscope="itemscope" itemtype="https://schema.org/ListItem">
 <a expr:href="' . $a[1] . '" itemprop="url">
 <span itemprop="name">Home</span>
  </a>
  <span content="1" itemprop="position">
 </span>
</span>';
}

// Output head contents
function head_contents(): string {
    $output = '';

    $favicon = '<link rel="icon" type="image/x-icon" href="' . site_url() . 'favicon.ico" >';
    $charset = '<meta charset="utf-8">';
    $referrer = '<meta name="referrer" content="origin">';
    $viewport = '<meta name="viewport" content="width=device-width, initial-scale=1">';
    $feed = '<link rel="alternate" type="application/rss+xml" title="' . blog_title() . ' Feed" href="' . site_url() . 'feed/rss" >';
    $webmasterTools = '';

    $output .= $charset . "\n" . "\n" . $viewport . "\n" . $referrer . "\n" . $favicon . "\n" . "\n" . $feed . "\n" . $webmasterTools . "\n";

    return $output;
}

/*
 * Echo one important word from a given text
 * It is used for automatic subtitles
 * It could be called from formParagraphs() in MarkDownExtra.php
 */

function autosubtitle(string $body) {
    // Output hidden keywords for feeding search engines
    $keyword_array = array();
    // Tokenize the title + description (including apostrophes, etc)
    $parts = preg_split("/\s+|\b(?=[!\?\.])(?!\.\s+)/", strip_tags($body));

    // extract long words (supposedly being key words) or 
    // words beginning with an uppercase
    // constrains the list to 5 keywords
    foreach ($parts as $keyword) {
        $majuscule = false;

        // All words where first letter is capitalized
        if ((strlen($keyword) > 3) && (strcmp(mb_strtoupper($keyword[0]), $keyword[0]) == 0)) {
            $keyword_array[] = $keyword . ', ';
            continue;
        }

        /*
          This gives true if all characters are alphanumeric.
          ^ marks the start of the string, and ^$^ marks the end.
          It also gives true if the string is empty.
         */
        if (preg_match("/^[A-Za-z0-9]*$/", $keyword)) {
            if (strcmp(mb_strtoupper($keyword), $keyword) == 0) {
                $majuscule = true;
        }
        }

        // All words which are in majuscule
        if ($majuscule) {
//                                $keyW = preg_replace("/[^A-Za-z0-9 ]/", ' ', $keyword);
//                                $keyW = html_entity_decode(strip_tags($keyword));
            $keyword_array[] = $keyword . ', ';
            continue;
    }

        // All words which are in minuscule and length is > 8
        if (($majuscule == false) && (strlen($keyword) > 8)) {
            // Remove unwanted characters like '&#039;s' in Alzheimer&#039;s
            $unwantedpos = strpos($keyword, '&#');
            if (($unwantedpos != false) && strlen($keyword) > 6) {
                $keyW = substr($keyword, 0, $unwantedpos);
                $keyword_array[] = $keyW . ', ';
            } else {
                $keyword_array[] = $keyword . ', ';
}
            continue;
        }
        // No lengthy keywords string
        if (count($keyword_array) > 8) {
            break;
        }
    }
    $keyword_string = implode('', array_unique($keyword_array));
    $KW_string = substr($keyword_string, 0, -2);

    return $KW_string;
}

<?php

// Show import RSS page
// https://padiracinnovation.org/News/admin/import
route('GET', '/admin/import', function () {
    if (is_logged()) {
        config('views.root', 'views/admin/views');
        render('import', array(
            'title' => 'Import feed - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => 'is_admin-import',
            'is_admin' => true,
            'bodyclass' => 'admin-import',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Import feed'
        ));
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
});

// Submitted import page data
route('POST', '/admin/import', function () {

    $proper = is_csrf_proper(from($_REQUEST, 'csrf_token'));

    $url = from($_REQUEST, 'url');
    $credit = from($_REQUEST, 'credit');
    if (is_logged() && !empty($url) && $proper) {
        
        if(!isset($credit)) {
            // Add source link box was not ticked
            $credit = '' ;
        }

        $log = get_feed($url, $credit);

        if (!empty($log)) {

            config('views.root', 'views/admin/views');

            render('import', array(
                'title' => 'Import feed - ' . blog_title(),
                'description' => strip_tags(blog_description()),
                'canonical' => site_url(),
                'error' => '<ul>' . $log . '</ul>',
                'type' => 'is_admin-import',
                'is_admin' => true,
                'bodyclass' => 'admin-import',
                'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Import feed'
            ));
        }
    } else {
        $message['error'] = '';
        if (empty($url)) {
            $message['error'] .= '<li class="alert alert-danger">You need to specify the feed url.</li>';
        }
        if (!$proper) {
            $message['error'] .= '<li class="alert alert-danger">CSRF Token not correct.</li>';
        }

        config('views.root', 'views/admin/views');

        render('import', array(
            'title' => 'Import feed - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'error' => '<ul>' . $message['error'] . '</ul>',
            'url' => $url,
            'type' => 'is_admin-import',
            'is_admin' => true,
            'bodyclass' => 'admin-import',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Login'
        ));
    }
});

// Fetch RSS feed
function get_feed(string $feed_url, string $credit = '') {
    $source = file_get_contents($feed_url);
    $feed = new SimpleXmlElement($source);
    if (!empty($feed->channel->item)) {
        /* https://news.ycombinator.com/rss
         * 
         * object(SimpleXMLElement)#67 (5) { 
         *     ["title"]=> string(5) "Sound" 
         *     ["link"]=> string(28) "https://ciechanow.ski/sound/" 
         *     ["pubDate"]=> string(31) "Tue, 18 Oct 2022 15:48:16 +0000" 
         *     ["comments"]=> string(45) "https://news.ycombinator.com/item?id=33249215" 
         *     ["description"]=> object(SimpleXMLElement)#66 (0) { } 
         * }
         * 
         * https://wordpress.com/blog/feed/
         * 
         * object(SimpleXMLElement)#66 (7) { 
         *     ["title"]=> string(64) "Free New Course Coming Soon: Create Your Site With WordPress.com" 
         *     ["link"]=> string(102) "https://wordpress.com/blog/2022/10/17/free-new-course-coming-soon-create-your-site-with-wordpress-com/" 
         *     ["comments"]=> string(111) "https://wordpress.com/blog/2022/10/17/free-new-course-coming-soon-create-your-site-with-wordpress-com/#comments" 
         *     ["pubDate"]=> string(31) "Mon, 17 Oct 2022 16:42:38 +0000" 
         *     ["category"]=> object(SimpleXMLElement)#68 (0) { } 
         *     ["guid"]=> string(37) "http://en.blog.wordpress.com/?p=49506" 
         *     ["description"]=> object(SimpleXMLElement)#69 (0) { } 
         * }
         */
        foreach ($feed->channel->item as $entry) {            
            $descriptionA = $entry->children('content', true);
            $descriptionB = $entry->description;
            if (!empty($descriptionA)) {
                $content = $descriptionA;
            } elseif (!empty($descriptionB)) {
                $content = preg_replace('#<br\s*/?>#i', "\n", $descriptionB);
            } else {
                return $str = '<li>Can not read the feed content.</li>';
            }
            $time = new DateTime($entry->pubDate);
            $timestamp = $time->format("Y-m-d H:i:s");
            $time = strtotime($timestamp);
            $tags = $entry->category;
            $title = rtrim($entry->title, ' \,\.\-');
            $title = ltrim($title, ' \,\.\-');
            $user = $_SESSION[config("site.url")]['user'];
            $url = strtolower(
                    preg_replace(
                            array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), 
                            array('', '-', ''), $entry->link));
            if ($credit == 'yes') {
                $source = $entry->link;
            } else {
                $source = '';
            }
            importRSS(new Title($title), $time, new Tag($tags), $content, $url, $user, $source);
        }
    } else {
        return $str = '<li>Unsupported feed.</li>';
    }
}

// Import RSS feed
function importRSS(Title $title, string $time, Tag $tags, string $content, string $url, string $user, string $source) {
    $post_date = date('Y-m-d-H-i-s', $time);
    $post_title = safe_html($title->value);
    $pt = safe_tag($tags->value);
    
    $post_tag = strtolower(preg_replace(array('/[^a-zA-Z0-9,. \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), $pt));
    $post_tag = rtrim($post_tag, ',');
    
    $post_tagmd = preg_replace(array('/[^a-zA-Z0-9,. \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', ' ', ''), $pt);
    $post_tagmd = rtrim($post_tagmd, ',');
    
    $post_url = strtolower(preg_replace(array('/[^a-zA-Z0-9 \-\p{L}]/u', '/[ -]+/', '/^-|-$/'), array('', '-', ''), $url));
    if (!empty($source)) {
        $post_content = '<!--t ' . $post_title . ' t-->' . "\n" . '<!--tag' . $post_tagmd . 'tag-->' . "\n\n" . $content . "\n\n" . 'Source: <a target="_blank" href="' . $source . '">' . $title->value . '</a>';
    } else {
        $post_content = '<!--t ' . $post_title . ' t-->' . "\n" . '<!--tag' . $post_tagmd . 'tag-->' . "\n\n" . $content;
    }
    if (!empty($post_title) && ($post_tag != '') && !empty($post_url)) {
        $post_content = stripslashes($post_content);

        $filename = $post_date . '_' . $post_tag . '_' . $post_url . '.md';
        $dir = 'content/users/' . $user . '/blog/uncategorized/draft/';
        if (is_dir($dir)) {
            file_put_contents($dir .
                    $filename, print_r($post_content, true));
        } else {
            mkdir($dir, 0775, true);
            file_put_contents($dir .
                    $filename, print_r($post_content, true));
        }
        save_tag_i18n(new Tag($post_tag), $post_tagmd);
        $redirect = site_url() . 'admin/mine';
        header("Location: $redirect");
    } else {
        // FIXME
        /*
        echo '<br>post_title: ' . $post_title ;
        echo '<br>post_tag: ' . $post_tag ;
        echo '<br>post_url: ' . $post_url ;
        echo '<br>post_content: ' . $post_content;
        */
    }
}

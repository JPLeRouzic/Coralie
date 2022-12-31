<?php

/* Format of recent.cache
 * 
 * ;s:7:"related";s:9:"alzheimer";}i:1;O:4:"Post":17:
 *      {
 *      s:8:"category";
 *      s:37:"<a href="/News/category/english"></a>";
 *      s:9:"categoryb";
 *      s:68:"<a property="v:title" rel="v:url" href="/News/category/english"></a>";
 *      s:2:"ct";
 *      s:7:"english";
 *      s:3:"url";
 *      s:22:"/News/2022/08/35983834";
 *      s:5:"title";
 *      s:122:"Association between vascular risk factors and cognitive impairment in ...";
 *      s:6:"author";
 *      s:5:"admin";
 *      s:18:"/News/author/admin";
 *      s:4:"type";
 *      s:4:"post";
 *      s:4:"body";
 *      s:2208:"<p>Greater physical activity and cardiorespiratory fitness are ... </p>"
 */

// Return trending posts lists
function trending_posts(int $count = 20) {
    $_views = array();
    $tmp = array();
    $trendingviews = array() ;

    if (config('views.counter') == 'true') {

        /* Get views file content */
        $filename = 'content/data/views.json';
        if (file_exists($filename)) {
            $_views = json_decode(file_get_contents($filename), true);
        }

        /* Get recent posts */
        $filerecent = "content/widget/recent.cache";
        $tmp = array();
        $posts_recent = array();
        if (file_exists($filerecent)) {
            $uno = file_get_contents($filerecent);
            $posts_recent = unserialize($uno);

            /* Check if for each post in recent.cache, there is a corresponding post in $_views */
            foreach ($posts_recent as $post) {
                foreach ($_views as $viewurl => $viewnb) {
                    // $viewurl: string(128) "content/users/admin/blog/english/post/2020-03-27-19-12-45_alzheimer.md"

                    if ((isset($post->file)) && (strcmp($post->file, $viewurl) == 0)) {
//                        echo '<br>post: ' . $post->file;
                        $trendingviews[] = [$viewurl => $viewnb];
                        break;
                    }
                }
            }

            // reverse sort, so most popular posts are the first
            arsort($trendingviews);

            $tmp = tmp_from_key($trendingviews, $count);

            $posts = get_posts($tmp, 1, $count);

            return $posts;
        } else {
        $posts = get_posts(null, 1, $count);
        $tmp = serialize($posts);
        file_put_contents($filerecent, print_r($tmp, true));
        }
    } else {
        echo '<ul><li>views.counter in config file is false</li></ul>';
    }
}

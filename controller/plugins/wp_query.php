<?php

/*
 * This is the beginning of a compatibility layer with WordPress plugin infrastructure.
 * 
 * WP_Query is a PHP class for constructing queries to the WordPress database and returning posts, pages, or other custom objects to render on the page. 
 * It allows developers to build complex searches while removing the need to write separate SQL queries.
 *
 * WP_Query provides shortcuts and built-in functions for customizing The Loop, which is the process for displaying content on a given page. 
 * With this focus on The Loop, WP_Query is the preferred choice for WordPress theme developers seeking to customize the content and appearance of pages.
 * 
 * The true power of WP_Query comes from the variety of parameters available to refine your database searches. 
 * The values specified for these parameters are called arguments (often shortened to args).
 *
 * WP_Query has 18 different parameter groups to pull from.
 *
 * You can review the parameter groups in greater detail in the WordPress Codex.
 */

class WP_Query {
    /* Specific to Coralie */

    // global parameters
    public $glbparameters;
    public int $nextPost;

    /* Properties specific to WP */
    // Holds the query string that was passed to the $wp_query object by WP class.
    // ['terms', 'field', 'orderby'
    public array $query;
    // An associative array containing the dissected $query: an array of the query variables and their respective values.
    public array $query_vars;
    // Applicable if the request is a category, author, permalink or Page. Holds information on the requested category, author, post or Page.
    public string $queried_object;
    // If the request is a category, author, permalink or post / page, holds the corresponding ID.
    public int $queried_object_id;
    // Gets filled with the requested posts from the database.
    public array $posts;
    // The number of posts being displayed.
    public int $post_count;
    // The total number of posts found matching the current query parameters
    public int $found_posts;
    // The total number of pages. Is the result of $found_posts / $posts_per_page
    public int $max_num_pages;
    // (available during The Loop) Index of the post currently being displayed.
    public string $current_post;
    // (available during The Loop) The post currently being displayed.
    public string $post;
    // Booleans dictating what type of request this is. For example, the first three represent ‘is it a permalink?’, ‘is it a Page?’, ‘is it any type of archive page?’
    public bool $is_single;
    public bool $is_page;
    public bool $is_archive;
    public bool $is_preview;
    public bool $is_date;
    public bool $is_year;
    public bool $is_month;
    public bool $is_time;
    public bool $is_author;
    public bool $is_category;
    public bool $is_tag;
    public bool $is_tax;
    public bool $is_search;
    public bool $is_feed;
    public bool $is_comment_feed;
    public bool $is_trackback;
    public bool $is_home;
    public bool $is_404;
    public bool $is_comments_popup;
    public bool $is_admin;
    public bool $is_attachment;
    public bool $is_singular;
    public bool $is_robots;
    public bool $is_posts_page;
    public bool $is_paged;

    public function __construct(array $params) {
        $this->glbparameters = $params;
        $this->nextPost = 0;

        // Filter posts with some criteria
        $this->posts = $this->getPostsCriteria($params);
    }

    /*
     * Get posts from one author
     * 
     * author (int) – use author id.
     * author_name (string) – use ‘user_nicename‘ – NOT name.
     * author__in (array) – use author id.
     * author__not_in (array) – use author id
     * 
     * returns an array pof posts
     */

    private function getPostsCriteria(array $params): array {
        /*
         * Show posts associated with certain authors.
         */
        if (isset($params['author'])) {
            wpq_manage_authors($params);
        } else
        /*
         * Show posts associated with certain categories.
         */
        if (isset($params['cat'])) {
            wpq_manage_cats($params);
        } else
        /*
         * Show posts associated with certain tags.
         */
        if (isset($params['tag'])) {
            wpq_manage_tags($params);
        } else
        /*
         * Show posts associated with certain taxonomies.
         */
        if (isset($params['tax'])) {
            wpq_manage_taxes($params);
        } else
        /*
         * Show posts associated with certain types.
         */
        if (isset($params['post_type'])) {
            wpq_manage_types($params);
        } else
        /*
         * Show posts associated with certain status.
         */
        if (isset($params['post_status'])) {
            wpq_manage_status($params);
        } else
        /*
         * Show posts associated with certain date.
         */
        if (isset($params['date_query'])) {
            wpq_manage_dates($params);
        } else
        /*
         * Show posts associated with certain custom field..
         */
        if (isset($params['meta_key'])) {
            wpq_manage_cusfields($params);
        }
    }

    public function have_posts(): bool {
        if (count($this->posts) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * Retrieves the next post, and put it the 'in the loop'
     */

    public function the_post(): Post {
        if ($this->nextPost < count($this->posts)) {
            return $this->posts[$this->nextPost];
        }
    }

    public function query(array $query_args) {
        
    }

    public function get(string $one, string $two) {
        
    }

    public function set(string $one, string $two) {
        
    }

}

function wpq_manage_authors($author): array {
    /*
     * Show posts associated with certain authors.
     */
    if (is_int($author['author']) && ($author['author'] >= 0)) {
        // Get author by ID
        $name = get_name_author_ID($author['author']);
        return get_posts_author_name($name);
    } else
    if (is_int($author['author']) && ($author['author'] < 0)) {
        // Exclude author by ID
        $tmp = array();
        $name = get_name_author_ID($author['author']);

        // Get all posts
        $vposts = get_post_unsorted();

        // Collect all posts except the unwanted author 
        foreach ($vposts as $index => $v) {
            $ret_gp = get_post($v);
            if (isset($ret_gp)) {
                if ($ret_gp->author->name !== $name) {
                    $tmp[] = $ret_gp;
                }
            }
        }
        return $tmp;
    } else
    if (is_string($author['author'])) {
        // Display posts from several specific authors 
        $tmp = array();
        $namelist = trim(explode(',', $author['author_name']));
        foreach ($namelist as $author) {
            $tmp1[] = get_posts_author_name($author);
            foreach ($tmp1 as $post) {
                $tmp[] = $post;
            }
        }
        return $tmp;
    } else
    if (is_string($author['author__not_in'])) {
        // Display posts from all except several specific authors 
        $tmp = array();

        // First we collect all posts
        $vposts = get_post_unsorted();

        // We build a list of excluded authors
        $namelist = trim(explode(',', $author['author__not_in']));

        // Collect all posts except those from unwanted authors 
        foreach ($vposts as $index => $v) {
            $ret_gp = get_post($v);
            if (isset($ret_gp)) {
                $tesrez = is_name_in_array($ret_gp->author->name, $namelist);
                if (!$tesrez) {
                    $tmp[] = $ret_gp;
                }
            }
        }
        return $tmp;
    } else
    if (is_string($author['author_name'])) {
        return get_posts_author_name($author['author_name']);
    }
}

function is_name_in_array($name_to_test, $namelist) {
    foreach ($namelist as $name) {
        if (in_array($name_to_test, $name)) {
            return true;
        }
    }
    return false;
}

function get_posts_author_name(string $author): array {
    $sorted1 = array();

    // Retrieve file pathnames of posts from that author
    $sorted1 = glob('content/users/' . $author . '/blog/*/post/*.md', GLOB_NOSORT);

    // Format them as array of "date/folder/name" (the v format)
    $_sorted = prepare_v($sorted1);

    // Find Posts from the 'v array'
    foreach ($_sorted as $index => $v) {
        $ret_gp = get_post($v);
        if (isset($ret_gp)) {
            $tmp[] = $ret_gp;
        }
    }
    return $tmp;
}


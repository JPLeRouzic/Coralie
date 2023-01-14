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
    
    // global parameters
    public $glbparameters ;
    
    public array $posts ;
    
    public int $nextPost ;
    
    public function __construct(array $params) {
        $this->glbparameters = $params ;
        $nextPost = 0 ;
    }

    
    function have_posts():bool {
        $this->posts = get_posts_sorted() ;
        
        if(count($this->posts) > 0) {
            return true ;
        } else {
            return false ;
        }
    }
    
    /*
     * Retrieves the next post, and put it the 'in the loop'
     */
    function the_post():Post {
        return $posts[$nextPost] ;
    }
}

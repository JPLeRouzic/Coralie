<?php

ini_set('display_errors', 'On');
error_reporting(E_ALL);

/* * 
 * Blog posts 
 * 
 * A permalink or permanent link is a URL that is intended to remain unchanged for many years into the future, 
 * Clean URLs also named user-friendly URLs, pretty URLs or search engine-friendly URLs, are URLs intended
 *  to improve the usability and accessibility of a website or web service by being immediately and intuitively 
 * meaningful to non-expert users. 
 * Such URL schemes tend to reflect the conceptual structure of a collection of information and 
 * decouple the user interface from a server's internal representation of information. 
 * Other reasons for using clean URLs include search engine optimization (SEO)
 */

// Show blog post without year-month
route('GET', '/post/:name', function($name) {
    get_post_name($name);
}
);

// Form to edit the post content
route('GET', '/post/:name/edit', function($name) {
    get_post_name_edit($name);
}
);

// Post resulting from form to edit content
route('POST', '/post/:name/edit', function() {
    post_post_name_edit();
}
);

// Form to delete blog post
route('GET', '/post/:name/delete', function($name) {
    get_post_name_delete($name);
}
);

// Get deleted data from form
route('POST', '/post/:name/delete', function() {
    post_post_name_delete();
}
);


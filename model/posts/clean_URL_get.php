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

// Show blog post with year-month
route('GET', '/:year/:month/:name', function ($year, $month, $name) {
    get_year_month_name($year, $month, $name);
}
);

// Edit blog post with year-month
route('GET', '/:year/:month/:name/edit', function ($year, $month, $name) {
        get_year_month_name_edit($year, $month, $name);
}
);

// Get edited data from blog post
route('POST', '/:year/:month/:name/edit', function () {
    post_year_month_name_edit();
}
);

// Delete blog post
route('GET', '/:year/:month/:name/delete', function ($year, $month, $name) {
    get_year_month_name_delete($year, $month, $name);
}
);

// Get deleted data from blog post
route('POST', '/:year/:month/:name/delete', function () {
    post_year_month_name_delete();
}
);


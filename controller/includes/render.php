<?php

/*
 * This function write the content on the browser by using the template at 
 * the layout.html.php
 * It imports variables from $locals array into the current symbol table 
 * and makes them accessible to the used template file
 * Do not use extract() on untrusted data, like user input (e.g. $_GET, $_FILES).</p>
 */

function render(string $view, array $locals = null, $layout_flag = true) {
    $uno = str_replace('?', '~', $_SERVER['REQUEST_URI']);
    str_replace('/', '#', $uno);

    //  Import variables from $locals array into the current symbol table 
    if (is_array($locals) && count($locals)) {
        extract($locals, EXTR_SKIP);
//      var_dump($locals) ;
    }
    /*
      For a blog article:
     * ------------------
     * 
     * array(10) { 
     *    ["title"]=> string(35) "English - Padirac Innovations' blog" 
     *    ["description"]=> string(14) "my description" 
     *    ["canonical"]=> string(22) "/News/category/english" 
     *    ["page"]=> int(1) 
     *    ["posts"]=> array(10) { 
     *        [0]=> object(Post)#64 (13) { 
     *            ["catg"]=> object(Category)#66 (5) { 
     *                 ["category"]=> string(37) "" 
     *                 ["categoryb"]=> string(68) "" 
     *                 ["url"]=> string(22) "/News/category/english" 
     *                 ["md"]=> uninitialized(string) 
     *                 ["file"]=> uninitialized(string) 
     *                 ["title"]=> string(0) "" 
     *                 ["body"]=> uninitialized(string) 
     *                 ["description"]=> uninitialized(string) 
     *                 ["slug"]=> string(7) "english" 
     *                 } 
     *            ["url"]=> string(86) "/News/2022/08/augmenting-neurogenesis-rescues-memory" 
     *            ["file"]=> string(137) "content/users/admin/blog/english/post/2022-08-27-09-41-11.md" 
     *            ["md"]=> uninitialized(string) 
     *            ["title"]=> object(Title)#71 (1) { 
     *                 ["value"]=> string(75) "Augmenting neurogenesis rescues memory" 
     *                 } 
     *            ["author"]=> object(Author)#67 (4) { 
     *                 ["name"]=> string(5) "admin" 
     *                 ["about"]=> string(25) "author"s about to be done" 
     *                 ["description"]=> string(31) "author"s description to be done" 
     *                 } 
     *            ["type"]=> object(Type)#68 (1) { 
     *                 ["value"]=> string(4) "post" 
     *                 } 
     *            ["date"]=> int(1661586071) 
     *            ["views"]=> int(56) 
     *            ["description"]=> object(Desc)#76 (1) { 
     *                 ["value"]=> string(27) "post description to be done" 
     *                 }
     *            ["body"]=> string(3281) " ... "
     *            ["archive"]=> object(Archive)#70 (1) { 
     *                 ["title"]=> string(21) "/News/archive/2022-08" 
     *                 } 
     *            ["tag"]=> object(Tag)#72 (3) { 
     *                 ["value"]=> string(53) "Alzheimer" 
     *                 ["tagb"]=> string(348) " Home " 
     *                 ["related"]=> object(Tag)#73 (1) { 
     *                      ["value"]=> string(9) "alzheimer" 
     *                      ["tagb"]=> uninitialized(string) 
     *                      ["related"]=> uninitialized(Tag) 
     *                      } 
     *                 } 
     *            ["ct"]=> string(7) "english" } 
     *   [1]=> object(Post)#65 (14) { 
     *    etc...
     * 
      For a static page:
     * ------------------
     * array(8) { 
     *  ["title"]=> string(46) "Important articles - Padirac Innovations' blog" 
     *  ["description"]=> string(149) "Si vous êtes malade de la SLA, ..." 
     *  ["canonical"]=> string(25) "/News/articles-importants" 
     *  ["bodyclass"]=> string(27) "in-page articles-importants" 
     *  ["breadcrumb"]=> string(51) "Home » Important articles" 
     *  ["p"]=> object(Post)#76 (22) { 
     *    ["category"]=> NULL 
     *    ["categoryb"]=> NULL 
     *    ["ct"]=> NULL 
     *    ["url"]=> string(25) "/News/articles-importants" 
     *    ["title"]=> string(18) "Important articles" 
     *    ["author"]=> NULL 
     *    ["type"]=> NULL 
     *    ["body"]=> string(1562) "..."
     *    ["date"]=> NULL 
     *    ["views"]=> int(654) 
     *    ["description"]=> string(149) "Si vous êtes malade de la SLA, ..." 
     *    ["archive"]=> NULL ["file"]=> string(37) "content/static/articles-importants.md" 
     *    ["image"]=> NULL 
     *    ["video"]=> NULL 
     *    ["link"]=> NULL 
     *    ["quote"]=> NULL 
     *    ["audio"]=> NULL 
     *    ["tag"]=> NULL 
     *    ["tagb"]=> NULL 
     *    ["related"]=> NULL 
     *    } 
     *  ["type"]=> string(10) "staticPage" 
     *  ["is_page"]=> bool(true) 
     * }
     * 
     */

    /*
     * ; Set the theme here
     * views.root = "views/theme/jplr"
     */
    $view_root = config('views.root');
    if ($view_root == null) {
        error(500, "err_11: [views.root] is not set");
    }

    /*
     * We use as template the file in $view, it is often "layout.html.php"
    #0 News/model/draft/draft.php(68): render('user-draft', Array)----------->
    #1 News/controller/includes/render.php(130): include()            <-------
    #2 News/views/admin/views/user-draft.html.php:27  <----------------
     */
    ob_start();
    include "{$view_root}/{$view}.html.php";

    /*
     * Get current buffer contents and delete current output buffer
     * Previous content between ob_start() and ob_get_clean() was buffered at PHP level.
     * ob_get_clean (the PHP buffer) will send the content of the buffer filled with the previous echo()
     * to dispatch's buffer
     */
    add_content_stash(trim(ob_get_clean()));

    /*
     * We always use layout.html.php
     */
    $layout2 = config('views.layout'); // which is usually 'layout"
    if ($layout2 == null) {
        $layout2 = 'layout';
    }

    $layout = "{$view_root}/{$layout2}.html.php";
    header('Content-type: text/html; charset=utf-8');
    if (config('generation.time') == 'true') {
        // Print how much time it took to generate the page (optional)
        ob_start();
        $start = microtime();

        // include template
        require $layout;

        $finish = microtime();
        $total_time = round(($finish - $start), 4);
        echo "\n" . '<!-- Dynamic page generated in ' . $total_time . ' seconds. -->';
    } else {
        /*
         * $layout_flag == false
         * We DO NOT use layout.html.php
         */
        ob_start();
        // include template
        require $layout;
    }
   /*
     * Get current buffer contents and delete current output buffer
     * This will output to the browser, the content of the buffer filled with the previous echo()
     */
    echo trim(ob_get_clean());
}

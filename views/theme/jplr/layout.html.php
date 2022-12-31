<!-- layout.html.php
This is the main layout for your theme.
This template displays the <head>...</head> and it includes another template to display either:
    - one post
    - multiple posts.
    - a static page
It is used for example in /News/
Variables are passed through the render function, for example:
        render('add-content', array(
            'title' => 'Add content - ' . blog_title(),
            'description' => strip_tags(blog_description()),
            'canonical' => site_url(),
            'type' => $type,
            'is_admin' => true,
            'bodyclass' => 'add-content',
            'breadcrumb' => '<a href="' . site_url() . '">' . config('breadcrumb.home') . '</a> &#187; Add post'
        ));
-->
<!DOCTYPE html>
<html lang="en">
    <head>
        <?php echo head_contents(); ?>
        <title><?php echo $title; ?></title>

        <meta name="image" property='og:image' content='../../../assets/PI_logo_square.avif'>
        <meta name="description" property='og:description' content='
        <?php
        if (is_a($description, 'Desc')) {
            $descript1 = $description->value;
        } else {
            $descript1 = $description;
        }
        $strlndesc = strlen($descript1);
        echo substr($descript1, 0, min($strlndesc, 150));
        ?>
              '>
        <meta name="author" property='og:author' content="<?php
        if (isset($author)) {
            echo $author;
        } else {
            echo 'multiple authors';
        }
        ?>">
        <meta name="title" property="og:title" content="<?php echo $title; ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=yes"/>

        <!--        <meta http-equiv="Content-Security-Policy" content="default-src https:"> -->
        <link rel="canonical" href="<?php echo site_url(); ?>" >
        <link href="<?php echo site_url(); ?>assets/Lato.css" rel="stylesheet" type="text/css">
        <link href="<?php echo site_url(); ?>assets/Montserrat.css" rel="stylesheet" type="text/css">
        <link href="<?php echo site_url(); ?>assets/Crimson.css" rel="stylesheet" type="text/css">     
        <!-- Global CSS -->
        <link rel="stylesheet" href="<?php
        echo site_url();
        echo config('views.root');
        ?>/css/bootstrap.min.css">   
        <!-- Funny glyphs -->
        <link rel="stylesheet" href="<?php
        echo site_url();
        echo config('views.root');
        ?>/css/font-awesome.min.css">
        <!-- Theme CSS -->  
        <link id="theme-style" rel="stylesheet" href="<?php
        echo site_url();
        echo config('views.root');
        ?>/css/styles.css">
        <!-- JS libraries -->
        <script src="<?php
              echo site_url();
              echo config('views.root');
        ?>/js/jquery-latest.min.js"></script>
        <script src="<?php
              echo site_url();
              echo config('views.root');
        ?>/js/bootstrap.min.js"></script>
        <script src="<?php
              echo site_url();
              echo config('views.root');
        ?>/js/includefiles.js"></script>
        <!-- Voice interface -->
        <script src="<?php echo site_url(); ?>assets/voice/js/responsivevoice.js?key=gBak4SYC"></script>
        <script src="<?php echo site_url(); ?>assets/voice/js/responsivevoice.js"></script>
        <script src="<?php echo site_url(); ?>assets/voice/js/mousetrap.min.js"></script>
        <script src="<?php echo site_url(); ?>assets/voice/js/app.js"></script>
        <!-- Clarity tracking code for https://www.padiracinnovation.org/ -->
        <script>
            (function (c, l, a, r, i, t, y) {
                c[a] = c[a] || function () {
                    (c[a].q = c[a].q || []).push(arguments)
                };
                t = l.createElement(r);
                t.async = 1;
                t.src = "https://www.clarity.ms/tag/" + i + "?ref=bwt";
                y = l.getElementsByTagName(r)[0];
                y.parentNode.insertBefore(t, y);
            })(window, document, "clarity", "script", "somekey");
        </script>
    </head> 
    <?php
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
        $url = site_url() . 'search/' . $search;
        header("Location: $url");
    }
    ?>
    <body class="<?php echo $bodyclass; ?>" itemscope="itemscope" itemtype="https://schema.org/Blog">


        <?php
        if (is_logged()) {
            toolbar();
        }
        ?>
        <!-- ******HEADER****** --> 
<?php ?>

        <div class="container"> 
            <!-- header.html -->
            <nav id="menu" class="navbar">
                <div class="navbar-header"><span id="category" class="visible-xs"></span>
                    <button type="button" class="btn btn-navbar navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">Menu&nbsp;<i class="fa fa-bars"></i></button>
                </div>
                <div class="collapse navbar-collapse navbar-ex1-collapse">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="<?php echo site_url(); ?>">
                                <img alt="Logo Padirac Innovation" src="<?php echo site_url(); ?>assets/PI_logo_4.avif" height="80" width="480" />
                            </a>
                        </li>

                        <li>
                            <a href="./">News from research</a>
                        </li>

                        <li>
                            <a href="<?php echo site_url(); ?>articles-importants">Useful Posts</a>
                        </li>

                        <li>
                            <a href="<?php echo site_url(); ?>feed/rss">RSS feed</a>
                        </li>

                        <li> 
                            <form id="search" class="navbar-form search" role="search">
                                <div class="input-group">
                                    <input type="search" name="search" class="form-control" placeholder="Type to search">
                                    <span class="input-group-btn"><button type="submit" class="btn btn-default btn-submit"><i class="fa fa-angle-right"></i></button></span>
                                </div>
                            </form>

                        </li>
                        <li>
                            <a href="mailto:jeanpierre.lerouzic@padiracinnovation.org">Contact the author</a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!--      <div class="container sections-wrapper"> -->
            <div class="row">
                <div><!-- secondary--> 
                    <div class="primary col-md-8 col-sm-12 col-xs-12"><!-- separator -->
                        <div>
                            <?php
                            // See post.html.php for HTML rendering of the content of the post
                            echo get_content_stash();
                            ?>   
                        </div>
                    </div><!-- primary-->
                    <div class="secondary col-md-4 col-sm-12 col-xs-12">
                        <aside class="recent-posts aside section">
                            <div class="section-inner">
                                <!-- Tab nav -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#recent-posts" aria-controls="recent-posts" role="tab" data-toggle="tab">Recent Posts</a></li>
<?php if (config('views.counter') === 'true') : ?>
                                        <li role="presentation"><a href="#popular-posts" aria-controls="popular-posts" role="tab" data-toggle="tab">Popular Posts</a></li>
<?php endif; ?>
                                </ul>
                                <!-- Tab content -->
                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane active" id="recent-posts">
                                        <h2 class="hide">Recent Posts</h2>
                                        <?php $lists = recent_posts(); ?>
                                        <?php $char = 60; ?>
                                        <?php foreach ($lists as $l): ?>
                                            <?php
                                            if (strlen(strip_tags($l->title->value)) > $char) {
                                                $recentTitle = shorten($l->title->value, $char) . '...';
                                            } else {
                                                $recentTitle = $l->title->value;
                                            }
                                            ?>
                                            <div class="item">
                                                <h3 class="title"><a href="<?php echo $l->url; ?>"><?php echo $recentTitle; ?></a></h3>
                                                <div class="content">
                                                    <p><?php echo shorten($l->description->value, 75); ?>...</p>
                                                    <a class="more-link" href="<?php echo $l->url; ?>"><i class="fa fa-link"></i> Read more</a>
                                                    <span class="share pull-right">
                                                        <a target="_blank" class="first" href="https://www.facebook.com/sharer.php?u=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>&t=<?php echo urlencode($l->title->value) ?>"><i class="fa fa-facebook"></i></a>

                                                        <a target="_blank" href="https://twitter.com/share?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>&text=<?php echo urlencode($l->title->value) ?>"><i class="fa fa-twitter"></i></a>

                                                        <a target="_blank" href="https://news.ycombinator.com/submitlink?u=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>&t=<?php echo urlencode($l->title->value) ?>"><i class="fa fa-hacker-news"></i></a>

                                                        <a target="_blank" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>"><i class="fa fa-linkedin"></i></a>

                                                        <a target="_blank" href="https://reddit.com/submit?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>&title=<?php echo urlencode($l->title->value) ?>"><i class="fa fa-reddit"></i></a>

                                                        <a target="_blank" href="https://www.tumblr.com/widgets/share/tool?canonicalUrl=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>&title=<?php echo urlencode($l->title->value) ?>"><i class="fa fa-tumblr-square"></i></a>

                                                        <a target="_blank" href="https://service.weibo.com/share/share.php?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>&appkey=&title=<?php echo urlencode($l->title->value) ?>"><i class="fa fa-weibo"></i></a>

                                                    </span>
                                                </div><!-- content-->
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                    <div role="tabpanel" class="tab-pane" id="popular-posts">
                                        <h2 class="hide">Popular Posts</h2>
                                        <?php $lists = popular_posts(); ?>
                                        <?php $char = 60; ?>
                                        <?php foreach ($lists as $l): ?>
                                            <?php
                                            if (strlen($l->title->value) > $char) {
                                                $recentTitle = shorten($l->title->value, $char) . '...';
                                            } else {
                                                $recentTitle = $l->title->value;
                                            }
                                            ?>
                                            <div class="item">
                                                <h3 class="title"><a href="<?php echo $l->url; ?>"><?php echo $recentTitle; ?></a></h3>
                                                <div class="content">
                                                    <p><?php echo shorten($l->description->value, 75); ?>...</p>
                                                    <a class="more-link" href="<?php echo $l->url; ?>"><i class="fa fa-link"></i> Read more</a>
                                                    <span class="share pull-right">
                                                        <a target="_blank" class="first" href="https://www.facebook.com/sharer.php?u=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>&t=<?php echo urlencode($l->title->value) ?>"><i class="fa fa-facebook"></i></a>

                                                        <a target="_blank" href="https://twitter.com/share?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>&text=<?php echo urlencode($l->title->value) ?>"><i class="fa fa-twitter"></i></a>

                                                        <a target="_blank" href="https://news.ycombinator.com/submitlink?u=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>&t=<?php echo urlencode($l->title->value) ?>"><i class="fa fa-hacker-news"></i></a>

                                                        <a target="_blank" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>"><i class="fa fa-linkedin"></i></a>

                                                        <a target="_blank" href="https://reddit.com/submit?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>&title=<?php echo urlencode($l->title->value) ?>"><i class="fa fa-reddit"></i></a>

                                                        <a target="_blank" href="https://www.tumblr.com/widgets/share/tool?canonicalUrl=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>&title=<?php echo urlencode($l->title->value) ?>"><i class="fa fa-tumblr-square"></i></a>

                                                        <a target="_blank" href="https://service.weibo.com/share/share.php?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . urlencode($l->url) ?>&appkey=&title=<?php echo urlencode($l->title->value) ?>"><i class="fa fa-weibo"></i></a>

                                                    </span>
                                                </div><!-- content-->
                                            </div>
<?php endforeach; ?>
                                    </div>
                                </div>
                            </div><!-- section-inner-->
                        </aside><!-- section-->

                        <aside class="archive aside section">
                            <div class="section-inner">
                                <h2 class="heading">Archive</h2>
                                <div class="content">
<?php /* echo */ archive_list(); ?>
                                </div><!-- content-->
                            </div><!-- section-inner-->
                        </aside><!-- section-->

                        <aside class="archive aside section">
                            <div class="section-inner">
                                <h2 class="heading">Search</h2>
                                <form id="search" class="navbar-form search" role="search">
                                    <div class="input-group">
                                        <input type="search" name="search" class="form-control" placeholder="Type to search">
                                        <span class="input-group-btn"><button type="submit" class="btn btn-default btn-submit"><i class="fa fa-angle-right"></i></button></span>
                                    </div>
                                </form>
                            </div><!-- section-inner-->
                        </aside><!-- section-->

                        <aside class="category-list aside section">
                            <div class="section-inner">
                                <h2 class="heading">Languages</h2>
                                <div class="content">
                                    <?php
                                    $catlist = category_list();
                                    echo '<ul>';

                                    foreach ($catlist as $k => $catvalue) {
                                        echo '<li><a href="' . site_url() . 'category/' . $catvalue[1] . '">' . $catvalue[1] . '</a></li>';
                                    }

                                    echo '</ul>';
                                    ?>
                                </div><!-- content-->
                            </div><!-- section-inner-->
                        </aside><!-- section-->

                        <!-- Forum  section 
                        <?php
                        date_default_timezone_set('UTC');
                        /* The comment section */
                        if ($_SERVER['REQUEST_URI'] !== '/News/Home') { // No comment in front page
//				include('model/comments/index.php'); 
                            // FIXME
                        }
                        ?>
                        <!-- section-->

                        <!-- Now the Paypal button -->
                        <br>Please, help us continue to provide valuable information:
                        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
                            <input type="hidden" name="cmd" value="_s-xclick" />
                            <input type="hidden" name="hosted_button_id" value="V6BQ5CYG47MHS" />
                            <input type="image" src="https://www.paypalobjects.com/en_US/FR/i/btn/btn_donateCC_LG.gif" border="0" name="submit" title="PayPal - The safer, easier way to pay online!" alt="Donate with PayPal button" />
                            <img alt="" border="0" src="https://www.paypal.com/en_FR/i/scr/pixel.gif" width="1" height="1" />
                        </form>
                        <br>

                        <!-- Tags -->
                        <aside class="tags aside section"><!-- section tags -->
                            <div class="section-inner">
                                <h2 class="heading">Tags</h2>
                                <div class="tag-cloud" >
                                    <?php
// $tags = tag_cloud(true); 
                                    $tags = ['Alzheimer', 'ALS', 'Parkinson', 'Cancer', 'Aging', 'Misc'];
                                    foreach ($tags as $tag):
                                        ?>
                                        <a class="more-link" href="<?php echo site_url(); ?>tag/<?php echo $tag; ?>"><?php echo tag_i18n($tag); ?></a> 
<?php endforeach; ?>
                                </div><!-- tag-cloud-->
                            </div><!-- section-inner-->
                        </aside><!-- section tags -->
                    </div><!-- secondary-->    
                </div><!-- row-->
            </div><!-- end of container-->


            <!-- ******FOOTER layout.html.php ****** --> 
            <footer class="footer">
                <div class="container text-center">
                    <div data-include-footer="../<?php
echo site_url();
echo config('views.root');
?>/footer.html"></div> 
                    <script>
                        includeHTML("data-include-footer");
                    </script>
                </div>
            </footer><!-- footer-->
    </body>
</html> 

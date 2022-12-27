<!-- main.html
This template displays a number of posts in main container of a page.
The main content index as a posts list. Eg. in dynamic frontpage, category, tag, archive, or search result.
It is used for example in /News
-->
<?php if (!empty($breadcrumb)): ?>
    <div class="breadcrumb"><?php echo $breadcrumb ?></div>
<?php endif; ?>

<?php
$i = 0;
$len = count($posts);
?>
<?php foreach ($posts as $p): ?>
    <?php
    if ($i == 0) {
        $class = 'post first';
    } elseif ($i == $len - 1) {
        $class = 'post last';
    } else {
        $class = 'post';
    }
    $i++;
    ?>
    <section class="post section <?php echo $class ?>" itemprop="blogPost" itemscope="itemscope" itemtype="https://schema.org/BlogPosting">
        <div class="section-inner">
            <div class="content">
                <div class="item">
                    <?php if (!empty($p->image)) { ?>
                        <div class="featured featured-image">
                            <a href="<?php echo $p->url ?>"><img  itemprop="image" src="<?php echo $p->image; ?>" alt="<?php echo $p->title->value ?>"/></a>
                        </div>
                    <?php } ?>

                    <div class="info text-left">
                        <h2 class="title" itemprop="headline">
                            <a href="<?php
                            echo $p->url;
                            ?>"><?php echo $p->title->value; ?></a></h2>
                        <p class="meta">
                            <span class="date" itemprop="datePublished"><?php echo date('d F Y', $p->date) ?></span> - Posted by 
                            <span itemprop="name"><?php echo $p->author->pseudo; ?></span>  
                            <span itemprop="articleSection">in <?php echo $p->catg->title; ?></span> 
                            <span class="share pull-right">
                                <a target="_blank" class="first" href="https://www.facebook.com/sharer.php?u=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>&t=<?php echo $p->title->value ?>"><i class="fa fa-facebook"></i></a>

                                <a target="_blank" href="https://twitter.com/share?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>&text=<?php echo $p->title->value ?>"><i class="fa fa-twitter"></i></a>

                                <a target="_blank" href="https://news.ycombinator.com/submitlink?u=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>&t=<?php echo $p->title->value ?>"><i class="fa fa-hacker-news"></i></a>

                                <a target="_blank" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>"><i class="fa fa-linkedin"></i></a>

                                <a target="_blank" href="https://reddit.com/submit?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>&title=<?php echo $p->title->value ?>"><i class="fa fa-reddit"></i></a>

                                <a target="_blank" href="https://www.tumblr.com/widgets/share/tool?canonicalUrl=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>&title=<?php echo $p->title->value ?>"><i class="fa fa-tumblr-square"></i></a>

                                <a target="_blank" href="http://service.weibo.com/share/share.php?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>&appkey=&title=<?php echo $p->title->value ?>"><i class="fa fa-weibo"></i></a>
                            </span>
                        </p>
                    </div>
                    <div class="desc text-left" itemprop="articleBody">                                    
                        <?php echo get_teaser($p->body, $p->url) ?>
                    </div><!--//desc-->
                    <div style="position:relative;">
                        <?php if (config('teaser.type') === 'trimmed'): ?>
                            <span class="more"><a class="btn btn-cta-secondary" href="<?php echo $p->url; ?>">Read more</a></span>
                        <?php endif; ?>
                        <span class="share pull-right">
                            <a target="_blank" class="first" href="https://www.facebook.com/sharer.php?u=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>&t=<?php echo $p->title->value ?>"><i class="fa fa-facebook"></i></a>

                            <a target="_blank" href="https://twitter.com/share?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>&text=<?php echo $p->title->value ?>"><i class="fa fa-twitter"></i></a>

                            <a target="_blank" href="https://news.ycombinator.com/submitlink?u=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>&t=<?php echo $p->title->value ?>"><i class="fa fa-hacker-news"></i></a>

                            <a target="_blank" href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>"><i class="fa fa-linkedin"></i></a>

                            <a target="_blank" href="https://reddit.com/submit?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>&title=<?php echo $p->title->value ?>"><i class="fa fa-reddit"></i></a>

                            <a target="_blank" href="https://www.tumblr.com/widgets/share/tool?canonicalUrl=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>&title=<?php echo $p->title->value ?>"><i class="fa fa-tumblr-square"></i></a>

                            <a target="_blank" href="http://service.weibo.com/share/share.php?url=<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $p->url ?>&appkey=&title=<?php echo $p->title->value ?>"><i class="fa fa-weibo"></i></a>
                        </span>
                        <div style="clear:both;"></div>
                    </div>
                </div><!--//item-->                       
            </div><!--//content-->  
        </div><!--//section-inner-->                 
    </section><!--//section-->
<?php endforeach; ?>
<?php if (!empty($pagination['prev']) || !empty($pagination['next'])): ?>
    <div class="pagination"><?php echo $pagination['html']; ?></div>
<?php endif; ?>


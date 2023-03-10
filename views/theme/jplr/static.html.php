<!-- static.html
Template for for static page and sub-static page.
-->
<?php if (!empty($breadcrumb)): ?>
    <div class="breadcrumb"><?php echo $breadcrumb ?></div>
<?php endif; ?>
<section class="inpage post section" itemprop="blogPost" itemscope="itemscope" itemtype="https://schema.org/BlogPosting">
    <div class="section-inner">
        <div class="content">
            <?php if (is_logged()) { echo tab($p); } ?>
            <div class="item">
                <h1 class="title" itemprop="headline"><?php echo $p->title->value;?></h1>
                <div class="desc text-left" itemprop="articleBody">
                    <?php 
                    echo $p->body; 
                    ?>
                </div><!--//desc-->
            </div><!--//item-->
        </div><!--//content-->
    </div><!--//section-inner-->
</section><!--//section-->
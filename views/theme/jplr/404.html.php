<!-- 404.html -->
<?php if (!empty($breadcrumb)): ?>
    <div class="breadcrumb"><?php echo $breadcrumb ?></div>
<?php endif; ?>
<section class="inpage post section">
    <div class="section-inner">
        <div class="content">
            <div class="item">
                <h1 class="title">This page doesn't exist!</h1>
                <p>Error code: <?php echo $error_code ?></p>
                <p>Please search to find what you're looking for or visit our <a href="<?php echo site_url() ?>">homepage</a> instead.</p>
                <?php
//                $e = new \Exception;
//                var_dump($e->getTraceAsString());
                echo search()
                ?>
            </div>
        </div>
    </div>

    <aside class="archive aside section">
        <div class="section-inner">
            <h2 class="heading">Archive</h2>
            <div class="content">
                <?php echo archive_list(); ?>
            </div><!--//content-->
        </div><!--//section-inner-->
    </aside><!--//section-->
</section>

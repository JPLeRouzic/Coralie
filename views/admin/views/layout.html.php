<!-- layout.html -->
<!DOCTYPE html>
<html>
<head>
    <?php echo head_contents() ?>
    <title><?php echo $title;?></title>
    <meta name="description" content="<?php echo $description; ?>"/>
    <?php if($canonical): ?>
        <link rel="canonical" href="<?php echo $canonical; ?>" />
    <?php endif; ?>
    <link href="<?php echo site_url() ?>assets/resources/css/admin.css" rel="stylesheet"/>
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet" type="text/css">
    <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body class="admin <?php echo $bodyclass; ?>" itemscope="itemscope" itemtype="http://schema.org/Blog">
<div class="hide">
    <meta content="<?php echo blog_title() ?>" itemprop="name"/>
    <meta content="<?php echo blog_description() ?>" itemprop="description"/>
</div>
<?php if (is_logged()) {
    toolbar();
} 

?>
<div id="outer-wrapper">
    <div id="content-wrapper">
        <div class="container">
            <section id="content">
                <?php echo get_content_stash() ?>
            </section>
        </div>
    </div>
    <div id="footer-wrapper">
        <div class="container">
            <footer id="footer">
                <div class="copyright"><?php echo copyright() ?></div>
            </footer>
        </div>
    </div>
</div>
</body>
</html>

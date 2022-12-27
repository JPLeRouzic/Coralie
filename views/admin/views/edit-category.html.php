<!-- edit-category.html -->
<?php
if (isset($p->file)) {
    $url = $p->file;
} else {
    $url = $oldfile;
}

$oldmd2 = explode('/', $url);
$oldmd = $oldmd2[3];

$content = file_get_contents($url);
$oldtitle = get_content_tag('t', $content, 'Untitled');
$olddescription = get_content_tag('d', $content);
$oldcontent = remove_html_comments($content);

if (isset($_GET['destination'])) {
    $destination = $_GET['destination'];
} else {
    $destination = 'admin';
}
$dir = substr($url, 0, strrpos($url, '/'));
$oldurl = str_replace($dir . '/', '', $url);

if (isset($p->url)) {
    $delete = $p->url . '/delete?destination=' . $destination;
} else {
    if (empty($sub)) {
        $delete = site_url() . $oldmd . '/delete?destination=' . $destination;
    } else {
        $delete = site_url() . $static . '/' . $sub . '/delete?destination=' . $destination;
    }
}
?>
<link rel="stylesheet" type="text/css" href="<?php echo site_url() ?>views/admin/editor/css/editor.css"/>
<script src="<?php echo site_url() ?>assets/resources/js/jquery.min.js"></script> 
<script src="<?php echo site_url() ?>assets/resources/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>views/admin/editor/js/Markdown.Converter.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>views/admin/editor/js/Markdown.Sanitizer.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>views/admin/editor/js/Markdown.Editor.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>views/admin/editor/js/Markdown.Extra.js"></script>
<link rel="stylesheet" href="<?php echo site_url() ?>assets/resources/css/jquery-ui.css">
<script type="text/javascript" src="<?php echo site_url() ?>views/admin/editor/js/jquery.ajaxfileupload.js"></script>

<?php if (isset($error)) { ?>
    <div class="error-message"><?php echo $error ?></div>
<?php } ?>

<div class="wmd-panel">
    <form method="POST">
        Title <span class="required">*</span>
        <br>
        <input type="text" name="title" class="text <?php
        if (isset($postTitle)) {
            if (empty($postTitle)) {
                echo 'error';
            }
        }
        ?>" value="<?php echo $oldtitle ?>"/>
        <br><br>
        Url (optional)<br><input type="text" name="url" class="text" value=""/>
        <br>
        <span class="help">If the url is left empty we will use the page title.</span>
        <br><br>
        Meta Description (optional)<br><textarea name="description" rows="3" cols="20"><?php
            if (isset($p->description->value)) {
                echo $p->description->value;
            } else {
                echo $olddescription;
            }
            ?></textarea>
        <br><br>
        <input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
        <input type="hidden" name="oldfile" class="text" value="<?php echo $url ?>"/>
        <input type="submit" name="submit" class="submit" value="Save category"/>
    </form>
</div>

<style>
    #insertImageDialog {
        display:none;
        padding: 10px;
        font-size:12px;
    }
    .wmd-prompt-background {
        z-index:10!important;
    }
</style>

<!-- Declare the base path. Important -->
<script type="text/javascript">var base_path = '<?php echo site_url() ?>';</script>
<script type="text/javascript" src="<?php echo site_url() ?>views/admin/editor/js/editor.js"></script>

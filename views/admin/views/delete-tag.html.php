<!-- delete-tag.html -->
<?php
if (isset($_GET['destination'])) {
    $destination = $_GET['destination'];
}
$url = $p->file; // FIXME ($p)

$dir = substr($url, 0, strrpos($url, '/'));
$oldurl = str_replace($dir . '/', '', $url);
$oldmd = str_replace('.md', '', $oldurl);

$post = $p->url;

if (isset($destination)) {

    if ($destination == 'post') {
        $back = $post;
    } else {
        $back = site_url() . $destination;
    }
} else {
    $back = site_url();
}
?>
<p>Are you sure want to delete <strong><?php 
/*
 * object(Tag)#64 (7) { 
 *     ["value"]=> string(6) "中文" 
 *     ["tag"]=> string(14) "tag to be done" 
 *     ["tagb"]=> string(15) "tagb to be done" 
 *     ["count"]=> uninitialized(string) 
 *     ["related"]=> uninitialized(Tag) 
 *     ["url"]=> string(16) "/News/tag/中文" 
 *     ["description"]=> string(33) "中文文本。(Texts in Chinese)" 
 *     ["file"]=> string(26) "content/data/tag/中文.md" 
 *     ["body"]=> string(34) " 中文文本。(Texts in Chinese)" }
 */
echo $p->value; 
?></strong>?</p>
<form method="POST">
    <input type="hidden" name="file" value="<?php echo $p->file ?>"/><br>
    <input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
    <input type="submit" name="submit" value="Delete"/>
    <span><a href="<?php echo $back ?>">Cancel</a></span>
</form>
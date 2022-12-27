<!-- delete-post.html -->
<?php
if (isset($_GET['destination'])) {
    $destination = $_GET['destination'];
}

if (isset($p->file)) {
    $url = $p->file;
} else {
    $url = $oldfile;
}

$oldurl = explode('_', $url);
$oldtag = $oldurl[1];
$oldmd = str_replace('.md', '', $oldurl[2]);

$replaced = substr($oldurl[0], 0, strrpos($oldurl[0], '/')) . '/';
$dt = str_replace($replaced, '', $oldurl[0]);
$t = str_replace('-', '', $dt);

// string(45) "content/users/admin/blog/中文/post/20210816184920"
$oldtimearray = explode('/', $t);
if(is_array($oldtimearray)) {
$t = $oldtimearray[count($oldtimearray) - 1] ;
}

$time = new DateTime($t);
$timestamp = $time->format("Y-m-d");
// The post date
$postdate = strtotime($timestamp);
// The post URL
$post = site_url() . date('Y/m', $postdate) . '/' . $oldmd;

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
<?php echo '<p>Are you sure want to delete: "<strong>' . $p->title->value . '</strong>"?</p>'; ?>
<form method="POST">
    <input type="hidden" name="file" value="<?php echo $p->file ?>"/><br>
    <input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
    <input type="submit" name="submit" value="Delete"/>
    <span><a href="<?php echo $back ?>">Cancel</a></span>
</form>
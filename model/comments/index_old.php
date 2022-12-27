<?php
/*
index.php - The index, ah yes, most web servers use this as a default file of sorts. Used to create the MAIN forum page (Thread list) and link all the php files together as one.
Script Created by Mitchell Urgero
Date: Sometime in 2016 ;)
Website: https://urgero.org
E-Mail: info@urgero.org

Script is distributed with Open Source Licenses, do what you want with it. ;)
"I wrote this because I saw that there are not that many databaseless Forums for PHP. It needed to be done. I think it works great, looks good, and is VERY mobile friendly. I just hope at least one other person
finds this PHP script as useful as I do."

*/
session_start();
require("config.php");
require("functions.php");
require("db.php");
//Force SSL if config says so.
if($config['ssl'] == true){
	if($_SERVER["HTTPS"] != "on")
	{
    	header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    	die();
	}
}

//Begin page
include("header.php");
echo '<div class="container">';
/* Body content
if($config['announce'] !== ""){
	echo '<div class="alert alert-info">'.$config['announce'].'</div>';
}
 */
if(isset($_SESSION['username']) && $config['allowNewThreads'] !== false){
	echo '<a href="post.php?type=new" class="btn btn-primary">'.L("new.post").'</a><br />';
}
if(!$config['allowNewThreads']){
	echo '<div class="alert alert-warning">'.L("new.thread.locked").'</div>';
}
if(isset($_SESSION['username'])){
	if(isAdmin($_SESSION['username']) && $config['allowNewThreads'] === false){
		echo '<a href="post.php?type=new" class="btn btn-primary">'.L("new.post").'</a><br />';
	}	
}

?>
<!--
<div class="page-header">
  <h1> <?= L("latest.posts") ?></h1>
</div>
-->
<table class="table">
	<thead>
		<th><?= L("post") ?></th>
		<th><?= L("last.updated") ?></th>
	</thead>
	<tbody>
<?php
$files1 = scan_dir($config['thread_data']);
$totalPages = 0;
if($files1){
  $data = $config['thread_data'];
  $page = ! empty( $_GET['page'] ) ? (int) $_GET['page'] : 1;
  $total = count( $files1 ); //total items in array
  $limit = $config['perPage']; //per page
  $totalPages = ceil( $total/ $limit ); //calculate total pages
  $page = max($page, 1); //get 1 page when $_GET['page'] <= 0
  $page = min($page, $totalPages); //get last page when $_GET['page'] > $totalPages
  $offset = ($page - 1) * $limit;
  if( $offset < 0 ) $offset = 0;
  if ($files1) {
  $files1 = array_slice( $files1, $offset, $limit );
      foreach($files1 as $file){
          if($file != ".." && $file != "."){
              $file = str_replace(".dat", "", $file);
              $name = file_get_contents("$data/$file.name");
              echo '<tr><td><a href="post.php?page=1&type=view&post='.$file.'">'.$name.'</a> | <a style="font-size:9px;" href="post.php?page=last&type=view&post='.$file.'">'.L("jump.to.last").'</a></td><td>'.date("Y-m-d h:i:sA",filemtime("$data/$file.dat")).'</td></tr>';
          }
      }
  }
}
?>
</tbody>
</table>


<?php
echo '<ul class="pagination">';
echo '<li><a href="./?page=1">'.L("first").'</a></li>';
for($i = 1; $i <= $totalPages; $i++){
	if($i == $page){
		echo '<li class="active"><a href="./?page='.$i.'">'.$i.'</a></li>';
	} else {
		echo '<li><a href="./?page='.$i.'">'.$i.'</a></li>';
	}
}
echo '<li><a href="./?page='.$totalPages.'">'.L("last").'</a></li>';
echo "</ul>";
echo '</div>';
// include("footer.php");


?>

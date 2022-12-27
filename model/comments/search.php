<?php
/*
search.php - The search Script.
Script Created by Mitchell Urgero
Date: Sometime in 2016 ;)
Website: https://urgero.org
E-Mail: info@urgero.org

Script is distributed with Open Source Licenses, do what you want with it. ;)
"I wrote this because I saw that there are not that many databaseless Forums for PHP. It needed to be done. I think it works great, looks good, and is VERY mobile friendly. I just hope at least one other person
finds this PHP script as useful as I do."

*/
session_start();
require("db.php");
require("config.php");
require("functions.php");
if(!$_SESSION['username']){
	die(L("must.logged.search"));
}


//Begin page
include("header.php");
echo '<div class="container">';
?>
<div class="page-header">
  <h1><?= sprintf(L("search.title"),$_GET['search']) ?></h1>
</div>
<?php
$files1 = scan_dir($config['thread_data']);
$finalAr = array();
$search = $_GET['search'];
$find = false;
if(strpos($search, " ")){
	$search = explode(" ", $search);
	
	foreach($search as $sr){
		foreach($files1 as $flsr){
			if(strpos($flsr, $sr) !== false){
				if(!in_array($flsr, $finalAr)){
					array_push($finalAr, $flsr);
					$find = true;
				}	
			}
		}
	}
} else {
	foreach($files1 as $flsr){
			if(strpos($flsr, $search) !== false){
				array_push($finalAr, $flsr);
				$find = true;
			}
		}
}
if($find === false){
	echo L("no.results");
} else {
	?>
	<table class="table">
	<thead>
		<th><?= L("post") ?></th>
		<th><?= L("last.updated") ?></th>
	</thead>
	<tbody>
	
	<?php
	$data = $config['thread_data'];
	$page = ! empty( $_GET['page'] ) ? (int) $_GET['page'] : 1;
	$total = count( $finalAr ); //total items in array    
	$limit = 20; //per page    
	$totalPages = ceil( $total/ $limit ); //calculate total pages
	$page = max($page, 1); //get 1 page when $_GET['page'] <= 0
	$page = min($page, $totalPages); //get last page when $_GET['page'] > $totalPages
	$offset = ($page - 1) * $limit;
	if( $offset < 0 ) $offset = 0;
	$finalAr = array_slice( $finalAr, $offset, $limit );
	foreach($finalAr as $file){
		if($file != ".." && $file != "."){
			$file = str_replace(".dat", "", $file);
			$name = file_get_contents("$data/$file.name");
			echo '<tr><td><a href="post.php?page=1&type=view&post='.$file.'">'.$name.'</a> | <a style="font-size:9px;" href="post.php?page=last&type=view&post='.$file.'">'.L("jump.to.last").'</a></td><td>'.date("Y-m-d h:i:sA",filemtime("$data/$file.dat")).'</td></tr>';
		}
	}	
}

?>
</tbody>
</table>
<?php
if($find === true){
	echo '<ul class="pagination">';
	echo '<li><a href="search.php?page=1&search='.$_GET['search'].'">'.L("first").'</a></li>';
	for($i = 1; $i <= $totalPages; $i++){
		if($i == $page){
			echo '<li class="active"><a href="search.php?page='.$i.'&search='.$_GET['search'].'">'.$i.'</a></li>';
		} else {
			echo '<li><a href="search.php?page='.$i.'&search='.$_GET['search'].'">'.$i.'</a></li>';
		}
}
echo '<li><a href="search.php?page='.$totalPages.'&search='.$_GET['search'].'">'.L("last").'</a></li>';
echo "</ul>";
echo '</div>';
}

// include("footer.php");

?>

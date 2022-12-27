<?php
/*
login.php - Is there really anything that needs to be said about a login page? Just a simple form really.
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
require("simple-php-captcha.php");
require("functions.php");
//Begin page
include_once("header.php");

$_SESSION['captcha'] = simple_php_captcha( $config['captchaLogin']);
echo '<div class="container">';
//Body content
?>
<div class="page-header">
  <h1><?= L("login") ?></h1>
</div>
<?php if(isset($_GET['msg'])){ echo '<p style="color:black;">'.$_GET['msg'].'</p>'; } ?>
<div class="row">
	<div class="col-md-6 col-md-offset-3">
<form action="submit.php" method="POST">
	<input type="hidden" name="type" id="type" value="login" />
	<div class="form-group">
    <label for="user"><?= L("username") ?>:</label>
    <input type="username" class="form-control" id="user" name="user">
  </div>
  <div class="form-group">
    <label for="pass"><?= L("password") ?>:</label>
    <input type="password" class="form-control" id="pass" name="pass">
  </div>
  	<?php
  	if($config['captchaLoginForce'] === true){
  		echo '';
  		?>
  		  <div class="form-group">
  			<label for="cap"><?= L("captcha.request") ?>:</label><br />
  			<img src="<?php echo $_SESSION['captcha']['image_src']; ?>">&nbsp;&nbsp;<input type="text" name="cap" id="cap" rows="8">
  		</div>
  		<?php
  	}
  	?>
  <button type="submit" class="btn btn-primary pull-right"><?= L("submit") ?></button>
</form>
</div>
</div>
<?php
echo '</div>';
// include("footer.php");
?>

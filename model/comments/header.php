<?php
/*
header.php - The top of any web page needs a proper header, this generates the menu and puts css, js, and other header stuff onto the page.
Script Created by Mitchell Urgero
Date: Sometime in 2016 ;)
Website: https://urgero.org
E-Mail: info@urgero.org

Script is distributed with Open Source Licenses, do what you want with it. ;)
"I wrote this because I saw that there are not that many databaseless Forums for PHP. It needed to be done. I think it works great, looks good, and is VERY mobile friendly. I just hope at least one other person
finds this PHP script as useful as I do."

*/
?>
<!DOCTYPE html>
<html lang="<?= L("key") ?>">
    <head>
        <title><?php echo $config['title']; ?></title>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="<?php echo $config['desc']; ?>">
        <meta name="author" content="JPLeRouzic">
        <link href="css/<?php echo themeSelector(); ?>" rel="stylesheet">
        <link href="css/bootstrapvalidator.min.css" rel="stylesheet">
        <link href="css/simplemde.min.css" rel="stylesheet">
        <script src="js/jquery.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/bootstrapvalidator.min.js"></script>
    </head>

    <body>

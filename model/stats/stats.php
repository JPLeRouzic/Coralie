<?php

// Show news search engine page

ini_set('display_errors', 'On');
error_reporting(E_ALL);

// Show stats page
route('GET', '/admin/stats', function () {
    admin_stats();
});

function admin_stats() {
    if (is_logged()) {
        echo '<html>
<head>
    <meta charset="utf-8" />

<meta name="viewport" content="width=device-width, initial-scale=1" />
<meta name="generator" content="HTMLy v2.7.5" /><meta name="referrer" content="origin">
<link rel="icon" type="image/x-icon" href="/News/favicon.ico" />
<link rel="sitemap" href="/News/sitemap.xml" />
<link rel="alternate" type="application/rss+xml" title="Padirac Innovations blog Feed" href="/News/feed/rss" />

    <title>Statistics</title>
    <meta name="description" content="This blog is about the latest research in degenerative diseases."/>
            <link rel="canonical" href="/News/" />
        <link href="/News/assets/resources/css/admin.css" rel="stylesheet"/>
    <link href="//fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet" type="text/css">
        <!--[if lt IE 9]>
    <script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
</head>
<body class="admin admin-tags" itemscope="itemscope" itemtype="http://schema.org/Blog">
<div class="hide">
    <meta content="Padirac Innovations blog" itemprop="name"/>
    <meta content="This blog is about the latest research in degenerative diseases." itemprop="description"/>
</div>
    <link href="/News/assets/resources/css/toolbar.css" rel="stylesheet" />' ;
toolbar() ;
echo '<div id="outer-wrapper">';

        $time = new DateTime();
        $timestamp = $time->format("Y-m-d H:i:s");
        $date = strtotime($timestamp);

        // Read the file containing the number of access for today
        $filename = "content/stats/" . date('Y-m-d', $date);
        if (!file_exists($filename)) {
            file_put_contents($filename, '0');
        }

        $nbAccess = file_get_contents($filename);
        echo '<br>Number of hits today: ' . $nbAccess . '<br><br>';

        // How many records in log file?
        $records = file_get_contents('content/stats/logs.txt');
        $scan_result = explode(';', $records);
        $max = sizeof($scan_result);

        // List last 7 days
        if (($max - 8) < 0) {
            $floor = 0;
        } else {
            $floor = $max - 8;
        }

        for ($i = ($max - 1 ); $i > $floor; $i--) {
            $nb_access2 = $scan_result[$i];
            $nb_access = explode(',', $nb_access2);
            echo $nb_access[0] . " , number access: " . $nb_access[1] . "<br>";
        }

        // Find the means of a few last days
        echo '<br>Means of previous ' . (int) ($max - 1 - $floor) . ' days<br>';
        $tot = 0;
        for ($i = ($max - 1 ); $i >= $floor; $i--) {
            $nb_access2 = $scan_result[$i];
            $nb_access = explode(',', $nb_access2);
            if (!isset($nb_access[1])) {
                break;
            }
            $tot = $tot + (int) $nb_access[1];
        }
        echo (int) ($tot / ((int) ($max - $floor - 1)));

        // Find the means of last 30 days
        if (($max - 31) < 0) {
            $floor = 0;
        } else {
            $floor = $max - 31;
        }
        echo '<br><br>Means of previous ' . (int) ($max - 1 - $floor) . ' days<br>';
        $tot = 0;
        for ($i = ($max - 1 ); $i >= $floor; $i--) {
            $nb_access2 = $scan_result[$i];
            $nb_access = explode(',', $nb_access2);
            if (!isset($nb_access[1])) {
                break;
            }
            $tot = $tot + (int) $nb_access[1];
        }
        echo (int) ($tot / ((int) ($max - $floor - 1)));

        // Find the overall means
        echo '<br><br>Overall means<br>';
        $tot = 0;
        for ($i = ($max - 1 ); $i >= 0; $i--) {
            $nb_access2 = $scan_result[$i];
            $nb_access = explode(',', $nb_access2);
            if (!isset($nb_access[1])) {
                break;
            }
            $tot = $tot + (int) $nb_access[1];
        }
        echo (int) ($tot / ($max - 1));

        // echo log of internal errors
        $filename = "content/stats/errlogs.txt" ;
        if (file_exists($filename)) {
            echo '<br><br>Content of errlogs.txt:<br>';
            $out = file_get_contents($filename);
            echo $out . '<br>' ;
        }

        echo '<a href="' . site_url() . 'admin"><br><br>Click here to return to admin menu</a>';
        echo '</body></html>';
    } else {
        $login = site_url() . 'login';
        header("location: $login");
    }
    die;
}


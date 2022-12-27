<?php

/*

Script Created by Mitchell Urgero
Date: Sometime in 2016 ;)
Website: https://urgero.org
E-Mail: info@urgero.org

Script is distributed with Open Source Licenses, do what you want with it. ;)
"I wrote this because I saw that there are not that many databaseless Forums for PHP. 
 * It needed to be done. I think it works great, looks good, and is VERY mobile friendly. I just hope at least one other person
finds this PHP script as useful as I do."
*/
function themeSelector(){
	global $config;

	return 'bootstrap.min.'.$config['theme'].'.css';
}
//Misc functions
function scan_dir($dir) {
    $ignored = array('.', '..', '.svn', '.htaccess');

    $files = array();
    foreach (scandir($dir) as $file) {
        if (in_array($file, $ignored)) continue;
        if(strpos($file,".name")) continue;
        if(strpos($file,".lock")) continue;
        if(strpos($file,".lockadmin")) continue;
        $files[$file] = filemtime($dir . '/' . $file);
    }

    arsort($files);
    $files = array_keys($files);

    return ($files) ? $files : false;
}

function L_init(){
    global $config;
    $lang = $config["lang"];
    if( ($lang=="auto") && (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) ){
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        $acceptLang = [];
        $langfiles = scandir('lang');
        foreach($langfiles as $langfile) {
            if($langfile!="."&&$langfile!=".."){
                $acceptLang[] = json_decode(file_get_contents("lang/".$langfile),true)["key"];
            }
        }
        $lang = in_array($lang, $acceptLang) ? $lang : 'en';
    }

//    $file = "lang/".$lang.".json";
    $en = "lang/en.json";
//    if(file_exists($file)){
        $langArray = json_decode(file_get_contents($en),true);
//    }else{
//        throw new Exception('The '.$lang.' language does not exist yet! You can translate it on the GitHub ;)');
//    }
    $enArray = json_decode(file_get_contents($en),true);
    $returnLang = [];
    foreach($enArray as $keyLang => $enLang){
        if(array_key_exists($keyLang,$langArray)){
            $returnLang[$keyLang] = $langArray[$keyLang];
        }else{
            $returnLang[$keyLang] = $enArray[$keyLang];
        }
    }
    return $returnLang;
}
function L($key){
    defined('L') or define("L",L_init());
    return L[$key];
}

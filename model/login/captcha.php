<?php

// Google recaptcha
function isCaptcha(string $reCaptchaResponse) {
    if (config('google.reCaptcha') != 'true') {
        return true;
    }
    $url = "https://www.google.com/recaptcha/api/siteverify";
    $options = array(
        "secret" => config("google.reCaptcha.private"),
        "response" => $reCaptchaResponse,
        "remoteip" => $_SERVER['REMOTE_ADDR'],
    );
    $fileContent = file_get_contents($url . "?" . http_build_query($options));
    if ($fileContent === false) {
        return false;
    }
    $json = json_decode($fileContent, true);
    if ($json == false) {
        return false;
    }
    return ($json['success']);
}


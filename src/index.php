<?php
    require "../vendor/autoload.php";

    require "api/telegram.php";

    try {
        $tl = new Telegram("https://api.telegram.org/", "bot310341855:AAGF60Bu1mHjDjjEn31ekxwJmKw-OMTBlqg/");
        print_r($tl->getLocation());
        
    } catch(Exception $e) {
        echo "Exception: " . $e;
    }
?>
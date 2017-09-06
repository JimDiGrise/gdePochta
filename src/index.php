<?php
    require "../vendor/autoload.php";

    require "api/telegram.php";
    require "api/Commands.php";

    try {
        $tl = new Telegram("https://api.telegram.org/", "bot310341855:AAGF60Bu1mHjDjjEn31ekxwJmKw-OMTBlqg/");
        $location;
            while(1) {
                sleep(3);
                $command = new Commands($tl);
                $lastMessage = $tl->getLastMessage();
                if(!empty($lastMessage)) {
                    $tl->confirmMessage();
                    $command->handleCommand($lastMessage);
                }
                    
            }
        
    } catch(Exception $e) {
        echo "Exception: " . $e;
    }
?>
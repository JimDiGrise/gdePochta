<?php 
    require "../vendor/autoload.php";

    require "yandex.php";
    require "pochta.php";
    class Commands {
        private $bot;
        private $lastChatId;
        private $geoLocation;
      
        private $geoKeyboard = [
            'keyboard' => [
                [
                   [
                       'text' => 'Отправить местоположение',
                       'request_location'=>true
                  ]
                ] 
                
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];

        public function __construct($bot) {
            $this->bot = $bot;
            
        }
        public function handleCommand($command) {
            if( $command == "/start" ) {
                $this->handleStart();
            } else if(!empty($command->latitude)) {
                $this->handleSetLocation($command);
            } else {
                $this->handleWrong();
            }
        }
       
        private function handleStart() {
            $this->bot->sendMessage( "Почтовый бот\n ", $this->geoKeyboard);
        }
        private function handleWrong() {
            $this->bot->sendMessage( "Команда не найдена", $this->geoKeyboard);
        }
        public function setLastChatId($chatId) {
            $this->lastChatId = $chatId;
        }
        private function handleSetLocation($geo) {
            $ya = new Yandex();
            $address = $ya->getAdress($geo);
            $pochta = new Pochta();
            print_r($address);
            print_r($pochta->getIndex($address));
        }
    }
    ?>
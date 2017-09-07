<?php 
    require "../vendor/autoload.php";

    require "yandex.php";
    require "pochta.php";
    class Commands {
        private $bot;
        private $lastChatId;
        private $geoLocation;
        private $menuKeyboard = [
            'keyboard' => [
                ["Найти отделение по местоположению"],
                ["Найти отделение по индексу"],
                ["Найти ближайшие отделения почты"],
                ["Изменить местоположение"]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];
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
            } else if($command == "location was set") {
                $this->setLocation();
            } else if($command == "Найти отделение по местоположению") {
                $this->getPostOffice();
            } else {
                $this->handleWrong();
            }
        }
       
        private function handleStart() {
            $this->bot->sendMessage( "Почтовый бот\n ", $this->geoKeyboard);
            $this->bot->sendMessage( "Установить геолокацию\n ", $this->geoKeyboard);
        }
        private function handleWrong() {
            $this->bot->sendMessage( "Команда не найдена", $this->menuKeyboard);
        }
        public function setLastChatId($chatId) {
            $this->lastChatId = $chatId;
        }
        private function setLocation() {
            $this->bot->sendMessage( "Меню\n ", $this->menuKeyboard);
        } 
        private function getPostOffice() {
            $geo = $this->bot->getLocation();
            print_r($geo);
            $ya = new Yandex();
            $address = $ya->getAdress($geo);
            $pochta = new Pochta();
            $index = $pochta->getIndex($address);
            $office = $ya->getPostOfficeByIndex($index);
            $ya->getSaticMap($office["Geo"]);
            $hours = explode(";",$office["Часы"] );
            $this->bot->sendMessage($office["Имя"] . "\n" . 
                                    "Адрес: " . $office["Адрес"] . "\n" . 
                                    "Телефон: " . $office["Телефон"] . "\n" .
                                    "Часы работы: \n" . $hours[0] . "\n" . $hours[1], $this->geoKeyboard);
            sleep(1);
            $this->bot->sendPhoto("img.png");

        }
    }
    ?>
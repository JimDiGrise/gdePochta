<?php 
    require "../vendor/autoload.php";

    require "yandex.php";
    require "pochta.php";
    class Commands {
        private $bot;
        private $lastChatId;
        private $geoLocation;
        private $index;
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
            if( $command ==  "/start") {
                $this->handleStart();
            } else if($command == "Изменить местоположение") {
                $this->handleStart();
            } else if($command == "location was set") {
                $this->setLocation();
            } else if($command == "index was set") {
                $this->getPostOfficeByIndex();
            } else if($command == "Найти отделение по местоположению") {
                $this->getOfficeByGeo();
            } else if($command == "Найти отделение по индексу") {
                $this->bot->sendMessage( "Введите индекс\n ", $this->menuKeyboard);
            } else if($command == "Найти ближайшие отделения почты") {
                $this->getOffices();
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
        private function setIndex($index) {
            $this->index = $index;
            
        }
        public function setLastChatId($chatId) {
            $this->lastChatId = $chatId;
        }
        private function setLocation() {
            if(!file_exists("geolocation.cfg") ) {
                file_put_contents("geolocation.cfg", json_encode([]));    
            }
            $geo = json_decode(file_get_contents("geolocation.cfg"), true);
            
            $geo[$this->bot->getChatId()] = $this->bot->getLocation();
            file_put_contents("geolocation.cfg", json_encode($geo));
            $this->bot->sendMessage( "Меню\n ", $this->menuKeyboard);
        } 
        private function getOffices() {
            $geo = $this->getLocation();
            $ya = new Yandex();
             $officesList = $ya->getItemsByGeoLocation($geo); 
            foreach($officesList as $office) {
                sleep(3);
                $hours = explode(";", $office->properties->CompanyMetaData->Hours->text);
                $this->bot->sendMessage($office->properties->CompanyMetaData->name . "\n" . 
                                        "Адрес: " . $office->properties->CompanyMetaData->address . "\n" . 
                                        "Телефон: " . $office->properties->CompanyMetaData->Phones[0]->formatted . "\n" .
                                        "Часы работы: \n" . $hours[0] . "\n" . $hours[1], $this->menuKeyboard);
                $ya->getSaticMap($office->geometry->coordinates);
                sleep(1);
                $this->bot->sendPhoto("img.png");

            }
        }
        private function getOfficeByGeo() {
            $geo = $this->getLocation();
            $ya = new Yandex();
            $address = $ya->getAdress($geo);
            $this->bot->sendMessage("Ваш адрес: " . $address, $this->menuKeyboard);
            $pochta = new Pochta();
            $index = $pochta->getIndex($address);
            $this->bot->sendMessage("Ваш индекс: " . $index, $this->menuKeyboard);
            $office = $ya->getPostOfficeByIndex($index);
            $hours = explode(";",$office["Часы"] );
            $this->bot->sendMessage($office["Имя"] . "\n" . 
                                    "Адрес: " . $office["Адрес"] . "\n" . 
                                    "Телефон: " . $office["Телефон"] . "\n" .
                                    "Часы работы: \n" . $hours[0] . "\n" . $hours[1], $this->menuKeyboard);
            $ya->getSaticMap($office["Geo"]);
            $this->bot->sendPhoto("img.png");

        }
        private function getPostOfficeByIndex() {
            $index = $this->bot->getIndex();
            $this->bot->sendMessage("Ваш индекс: " . $index, $this->menuKeyboard);
            $ya = new Yandex();
            $office = $ya->getPostOfficeByIndex($index);
            $hours = explode(";",$office["Часы"] );
            $this->bot->sendMessage($office["Имя"] . "\n" . 
                                    "Адрес: " . $office["Адрес"] . "\n" . 
                                    "Телефон: " . $office["Телефон"] . "\n" .
                                    "Часы работы: \n" . $hours[0] . "\n" . $hours[1], $this->menuKeyboard);
            $ya->getSaticMap($office["Geo"]);
            $this->bot->sendPhoto("img.png");

        }
        private function getLocation() {
            $locations = json_decode(file_get_contents("geolocation.cfg"), true);
            return $locations[$this->bot->getChatId()];
        }
    }
    ?>
<?php 
    require "../vendor/autoload.php";
    
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
            } else if($command == "location was set") {
                $this->handleSetLocation();
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
        private function handleSetLocation() {
            $path = "../geolocation.cfg";
            if(!file_exists($path) ) {
                file_put_contents($path, json_encode([]));    
            }
            $geo = json_decode(file_get_contents($path), true);
            
            $geo[$this->bot->getChatId()] = $this->bot->getLocation();
            file_put_contents($path, json_encode($geo));
        }
    }
    ?>
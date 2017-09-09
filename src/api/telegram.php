<?php 
    require "../vendor/autoload.php";
    
   

    use GuzzleHttp\Client;


    class Telegram {
        private $httpClient;
        private $offset;
        private $location;
        public $lastChatId;
        private $index;

        public function __construct($botUrl, $botToken) {
            $this->httpClient = new Client([
                'base_uri' => $botUrl . $botToken,
                
		    ]);
        }
        public function getChatId() {
            return $this->lastChatId;
        }
        public function getLastMessage() {
            
            $response = $this->httpClient->request('POST', 'getUpdates');
            $responseBody = json_decode($response->getBody());
            
            if(empty($responseBody->result)) {
                return FALSE;
            }
            $length = count($responseBody->result);
            $this->offset = (int)$responseBody->result[$length - 1]->update_id;
            $this->lastChatId = $responseBody->result[$length - 1]->message->chat->id;

            if(!empty($responseBody->result[$length - 1]->message->location)) {
                $this->location = $responseBody->result[$length - 1]->message->location;
                return "location was set";    
            }

            if(strlen($responseBody->result[$length - 1]->message->text)  == 6 && is_numeric($responseBody->result[$length - 1]->message->text)) {
                $this->index = $responseBody->result[$length - 1]->message->text;
                return "index was set";    
            }
            return $responseBody->result[$length - 1]->message->text;
        }
        public function confirmMessage() {
            $response = $this->httpClient->request('POST', 'getUpdates', [
                'json' => [
                    'offset' => $this->offset + 1
                ]
            ]);    
        }
        public function sendMessage( $message, $keyboard ) { 
            sleep(1);
            $response = $this->httpClient->request('POST', 'sendMessage', [
			'json' => ['chat_id' => $this->lastChatId, 
						'text' => $message, 
                        'reply_markup' => json_encode($keyboard)
                    ]
            ]);
            return $response->getStatusCode();
        }
        public function sendPhoto( $path ) { 
            sleep(1);
            $res = $this->httpClient->request('POST', 'sendPhoto', [
			'multipart' => [
                [
                    'name'     => 'photo',
                    'contents' => fopen($path, 'r'),
                ],
                [
                    'name'     => 'chat_id',
                    'contents' => $this->lastChatId,
                ],
            ]    
            ]);	
            return $res->getStatusCode();
        }
        public function getLocation() {
            return $this->location;
        }
        public function getIndex() {
            return $this->index;
        }

    }
    
?>
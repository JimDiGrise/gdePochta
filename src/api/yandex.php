<?php 
    require "../vendor/autoload.php";
    
    use GuzzleHttp\Client;

    class Yandex {
        private $geo;
        public function __construct() {
            $this->httpClient = new Client();
            
        }
        public function getAdress($geo) {
            $response = $this->httpClient->request("GET", "https://geocode-maps.yandex.ru/1.x/?format=json&geocode=$geo->longitude, $geo->latitude");
            return json_decode($response->getBody())->response
                                                    ->GeoObjectCollection
                                                    ->featureMember[0]
                                                    ->GeoObject
                                                    ->metaDataProperty
                                                    ->GeocoderMetaData
                                                    ->text;  
        }    
    }
?>
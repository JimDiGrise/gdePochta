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
        public function getPostOfficeByIndex($index) {
            $response = $this->httpClient->request('GET', "https://search-maps.yandex.ru/v1/?apikey=37cc2574-03a2-444f-8f5f-caafaae8efe1&text=Отделение почты $index&lang=ru-RU&type=biz&results=1");
            $responseBody = json_decode($response->getBody())->features[0];
            $response = array();
            $response["Имя"] = "Почтовое отделение " . $index;
            $response["Адресс"] = $responseBody->properties->CompanyMetaData->address;
            $response["Сайт"] = "https://www.pochta.ru/";
            $response["Телефон"] = $responseBody->properties->CompanyMetaData->Phones[0]->formatted;
            $response["Часы"]= $responseBody->properties->CompanyMetaData->Hours->text;
            return $response;
        }    
    }
?>
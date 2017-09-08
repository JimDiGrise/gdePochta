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
            $response["Адрес"] = $responseBody->properties->CompanyMetaData->address;
            $response["Сайт"] = "https://www.pochta.ru/";
            $response["Телефон"] = $responseBody->properties->CompanyMetaData->Phones[0]->formatted;
            $response["Часы"] = $responseBody->properties->CompanyMetaData->Hours->text;
            $response["Geo"] = $responseBody->geometry->coordinates;
            return $response;
        }
        public function getSaticMap($geo) {
            $response = $this->httpClient->request('GET', "https://static-maps.yandex.ru/1.x/?ll=$geo[0],$geo[1]&size=250,250&z=17&l=map&pt=$geo[0],$geo[1],pm2dgm&scale=1.0");
            file_put_contents("img.png", $response->getBody());  
        }    
        public function getItemsByGeoLocation($geo) {
            $response = $this->httpClient->request('GET', "https://search-maps.yandex.ru/v1/?apikey=37cc2574-03a2-444f-8f5f-caafaae8efe1&text=Отделение почты &lang=ru-RU&type=biz&results=100&ll=$geo->longitude, $geo->latitude&spn=0.015,0.015&rspn=1");
            $responseBody = json_decode($response->getBody())->features;
            $pochta = new Pochta();
            foreach($responseBody as $office => $value) {
                $responseBody[$office]->properties->CompanyMetaData->name = "Почтовое отделение #" . $pochta->getIndex($responseBody[$office]->properties->CompanyMetaData->address);
            }
            return array_filter($responseBody, function ($obj) {
                static $idList = array();
                if(in_array($obj->properties->CompanyMetaData->name, $idList)) {
                    return false;
                }
                $idList []= $obj->properties->CompanyMetaData->name;
                return true;
            });;
        }
    }
?>
<?php 
    require "../vendor/autoload.php";

    use GuzzleHttp\Client;


    class Pochta {
        private $httpClient;

        public function __construct() {
            $this->httpClient = new Client();
        }
        public function getIndex($address) {
            $response = $this->httpClient->request("GET", "https://www.pochta.ru/portal-portlet/delegate/postoffice-api/method/offices.find.forAddress?address=$address");
            return json_decode($response->getBody())->postOffices[0]
                                                    ->index;
        }
    }

?>
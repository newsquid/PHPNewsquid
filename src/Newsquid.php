<?php

class Newsquid {
    
    private $newsquid_url;
    private $uid;
    private $secret;
    public $insecure;

    public function __construct($newsquid_url, $uid, $secret, $insecure = false) {
        $this->newsquid_url = $newsquid_url;
        $this->uid = $uid;
        $this->secret = $secret;
        $this->insecure = $insecure;
    }

    public function getProducts($user_token) {
        throw new Exception("Not implemented");
    }

    public function getProduct($id) {
        $url = http_build_url($this->newsquid_url."/api/v2/products/".$id,
            array(
                "user" => $this->uid,
                "pass" => $this->secret
            )
        );
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        if($this->insecure) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        if($result = curl_exec($ch)) {
            if($result == "HTTP Basic: Access denied.") {
                throw new AccessDeniedException("Userid and secret combination invalid");
            }
            else {
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                switch($http_code) {
                    case 200: //happy days!
                        $data = json_decode($result);
                        return new NewsquidProduct(
                            $data->title,
                            $data->price,
                            $data->currency,
                            $data->url
                        );
                    case 404:
                        throw new ProductNotFoundException("Could not find the product with id $id");
                }
            }
        }
        else {
            switch(curl_errno($ch)) {
                case 6:
                    throw new NewsquidException("Couldn't resolve host ".$this->newsquid_url); 
            }
            if(curl_errno($ch) == 404) {
                throw new ProductNotFoundException("Could not find product with id $id on Newsquid");
            }
            throw new Exception("cURL failed... :(\n"."Error ".curl_errno($ch).": ".curl_error($ch));
        }
    }

    public function getUsers() {
        throw new Exception("Not implemented");
    }

    public function getUser($id) {
        throw new Exception("Not implemented");
    }

}

?>

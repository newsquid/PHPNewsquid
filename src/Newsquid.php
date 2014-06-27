<?php

class Newsquid {
    
    private $newsquid_caller;

    public function __construct(RemoteCaller $newsquid_caller, $uid, $secret, $insecure = false) {
        $this->newsquid_caller = $newsquid_caller;
        $this->newsquid_caller->user = $uid;
        $this->newsquid_caller->password = $secret;
        $this->newsquid_caller->insecure = $insecure;
    }

    public function createProduct($id, $title, $price, $currency, $url, NewsquidUser $user) {
        $this->newsquid_caller->post("products", array(
            "product" => array(
                "sku" => $id,
                "url" => $url,
                "currency" => $currency,
                "price" => $price,
                "title" => $title
            ),
            "access_token" => $user->token
        ));

        return new NewsquidProduct(
            $id,
            $title,
            $price,
            $currency,
            $url,
            $this->newsquid_caller
        );
    }

    public function getProduct($id) {
        $result = $this->newsquid_caller->get("products/$id");

        $data = json_decode($result);
        return new NewsquidProduct(
            $id,
            $data->title,
            $data->price,
            $data->currency,
            $data->url,
            $this->newsquid_caller
        );
    }
}

?>

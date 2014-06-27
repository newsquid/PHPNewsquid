<?php

class NewsquidUser {

    private $id;
    public $name;
    public $email;
    public $token;
    private $newsquid_caller;

    public function __construct($id, $name, $email, $token, $newsquid_caller) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->token = $token;
        $this->newsquid_caller = $newsquid_caller;
    }

    public function __get($name) {
        if($name == "id")
            return $this->$name;
    }

    public function buyProduct(NewsquidProduct $product) {
        $this->newsquid_caller->post("consumer/orders", array(
            "product" => array(
                "sku" => $product->id
            ),
            "access_token" => $this->token
        ));
    }

    public function canAccessProduct(NewsquidProduct $product) {
        $result = $this->newsquid_caller->get("consumer/access/{$product->id}?access_token={$this->token}");

        $data = json_decode($result);
        return $data->access;
    }
}

?>

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

    public function logInUri($redirectUri) {
        $scopes = "login"; //TODO: Should not be so specific/limited.
       
        return $this->newsquid_caller->clientUrl("oauth/authorize", array(
            "redirect_uri" => $redirectUri,
            "response_type" => "code",
            "scope" => $scopes
        ));
        
    }

    public function buyProduct(NewsquidProduct $product) {
        $this->newsquid_caller->post("api/v2/consumer/orders", array(
            "product" => array(
                "sku" => $product->id
            ),
            "access_token" => $this->token
        ));
    }

    public function canAccessProduct(NewsquidProduct $product) {
        try {
            $result = $this->newsquid_caller->get("api/v2/consumer/access/{$product->id}?access_token={$this->token}");
        }
        catch(PaymentRequiredException $e) {
            return false;
        }
        catch(UnauthorizedException $e) {
            return false;
        }

        $data = json_decode($result);
        return $data->access;
    }
}

?>

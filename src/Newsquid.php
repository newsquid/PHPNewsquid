<?php

class Newsquid {
    
    private $newsquid_caller;

    public function __construct(RemoteCaller $newsquid_caller, $uid, $secret, $insecure = false) {
        $this->newsquid_caller = $newsquid_caller;
        $this->newsquid_caller->user = $uid;
        $this->newsquid_caller->password = $secret;
        $this->newsquid_caller->insecure = $insecure;
    }

    public function getProducts($user_token) {
        throw new Exception("Not implemented");
    }

    public function getProduct($id) {
        $result = $this->newsquid_caller->get("products/$id");

        $data = json_decode($result);
        return new NewsquidProduct(
            $data->title,
            $data->price,
            $data->currency,
            $data->url
        );
    }

    public function getUsers() {
        throw new Exception("Not implemented");
    }

    public function getUser($id) {
        throw new Exception("Not implemented");
    }

}

?>

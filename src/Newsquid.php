<?php
Class Newsquid {

    private $newsquid_caller;

    public function __construct(RemoteCaller $newsquid_caller, $uid, $secret, $insecure = false) {
        $this->newsquid_caller = $newsquid_caller;
        $this->newsquid_caller->user = $uid;
        $this->newsquid_caller->password = $secret;
        $this->newsquid_caller->insecure = $insecure;
    }

    public function logInUri($redirectUri) {
        $scopes = "login"; //TODO: Should not be so specific/limited.

        return $this->newsquid_caller->clientUrl("oauth/authorize", array(
            "redirect_uri" => $redirectUri,
            "response_type" => "code",
            "scope" => $scopes
        ));

    }

    public function createProduct($id, $title, $price, $currency, $url, NewsquidUser $user, $product_owner_email = NULL) {
        $product = array(
                "sku" => $id,
                "url" => $url,
                "currency" => $currency,
                "price" => $price,
                "title" => $title
        );

        $params = array(
            "product" => $product,
            "access_token" => $user->token
        );

        if ($product_owner_email != NULL) {
            $params["product_owner_email"] = $product_owner_email;
        }

        $nsqproduct = $this->newsquid_caller->post("api/v2/products", $params);

        $data = json_decode($nsqproduct);

        return new NewsquidProduct(
            $id,
            $title,
            $price,
            $currency,
            $url,
            $data->nsq_item_id,
            $this->newsquid_caller
        );
    }

    public function getProduct($id) {
        $result = $this->newsquid_caller->get("api/v2/products/$id");

        $data = json_decode($result);
        return new NewsquidProduct(
            $id,
            $data->title,
            $data->price,
            $data->currency,
            $data->url,
            $data->nsq_item_id,
            $this->newsquid_caller
        );
    }

    /**
     * Gets the current Newsquid user
     * Returns a NewsquidUser if the user is logged in
     * Returns false if the user is not logged in
     */
    public function getCurrentUser($token){
        //Return false if not authenticated
        try {
            $result = $this->newsquid_caller->get("api/v2/consumer?access_token=$token");

            $data = json_decode($result);

            return new NewsquidUser(
                $data->id,
                $data->name,
                $data->email,
                $token,
                $this->newsquid_caller
            );
        }catch(UnauthorizedException $e){
            return false;
        }

    }

    /**
     * Given a oauth grant this functions gets and access token from the server
     */
    public function getAccessToken($grant_code,$return_url){
        $result = $this->newsquid_caller->post("oauth/token",array(
            "grant_type" => "authorization_code",
            "code" => $grant_code,
            "redirect_uri" => $return_url));

        $data = json_decode($result);

        return $data->access_token;
    }
}

# vim: set ts=4 sw=4 et:
?>

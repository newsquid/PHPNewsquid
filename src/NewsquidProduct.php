<?php

class NewsquidProduct {
    
    public $title;
    public $price;
    public $currency;
    public $url;

    public function __construct($title, $price, $currency, $url) {
        $this->title = $title;
        $this->price = $price;
        $this->currency = $currency;
        $this->url = $url;

        $this->synced = array(
            "title" => $title,
            "price" => $price,
            "currency" => $currency,
            "url" => $url
        );
    } 

    public function sync() {
        throw new Exception("Not implemented");
    }
}

?>

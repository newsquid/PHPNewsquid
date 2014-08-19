<?php

class NewsquidProduct {
    
    public $title;
    public $price;
    public $currency;
    public $url;

    private $id;
    private $newsquid_caller;

    public function __construct($id, $title, $price, $currency, $url, RemoteCaller $newsquid_caller) {
        $this->id = $id;
        $this->title = $title;
        $this->price = $price;
        $this->currency = $currency;
        $this->url = $url;
        $this->newsquid_caller = $newsquid_caller;

        $this->last_synced = array(
            "title" => $title,
            "price" => $price,
            "currency" => $currency,
            "url" => $url
        );
    } 

    public function __get($name) {
        if($name == "id") {
            return $this->id;
        }
    }

    public function hasChanged() {
        foreach($this->last_synced as $key => $val) {
            if($this->$key != $val)
                return true;
        }
        return false;
    }

    public function sync(NewsquidUser $owner) {
        $to_sync = array();

        foreach($this->last_synced as $key => $val) {
            if($this->$key != $val)
                $to_sync[$key] = $this->$key;
        }

        if(!empty($to_sync)) {
            $data = array(
                "product" => $to_sync,
                "access_token" => $owner->token
            );

            $this->newsquid_caller->put("api/v2/products/{$this->id}", $data);

            foreach($to_sync as $key => $val)
                $this->last_synced[$key] = $val;
        }
    }
}


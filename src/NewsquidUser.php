<?php

class NewsquidUser {

    private $id;
    public $name;
    public $email;
    public $token;

    public function __construct($id, $name, $email, $token) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->token = $token;
    }

    public function __get($name) {
        if($name == "id")
            return $this->$name;
    }

}

?>

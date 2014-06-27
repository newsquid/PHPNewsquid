<?php

class NewsquidUser {

    private $id;
    private $name;
    private $email;
    private $token;

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

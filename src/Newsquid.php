<?php

class Newsquid {
    
    private $newsquid_url;
    private $application_url;

    public function __construct($newsquid_url, $uid, $secret) {
        $this->newsquid_url = $newsquid_url;
        $this->uid = $uid;
        $this->secret = $secret;
    }

    public function getArticles($user_token) {
        throw new Exception("Not implemented");
    }

    public function getUsers() {
        throw new Exception("Not implemented");
    }

    public function getUserById($id) {
        throw new Exception("Not implemented");
    }

}

?>

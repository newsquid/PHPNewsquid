<?php

class Newsquid {
    
    private $newsquid_url;
    private $application_url;

    public function __construct($newsquid_url, $application_url) {
        $this->newsquid_url = $newsquid_url;
        $this->application_url = $application_url;
    }

    public function getArticles($user = null) {
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

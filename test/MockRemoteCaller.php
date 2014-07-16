<?php

class MockRemoteCaller implements RemoteCaller {
    
    private $overrides;

    public function __construct($overrides = array()) {
        $this->overrides = $overrides;
    }

    public function clientUrl($path, $query = array()) {
        return $this->overrides["clientUrl"]($path, $query);
    }

    public function get($path) {
        return $this->overrides["get"]($path);
    }

    public function post($path, $data = array()) {
        return $this->overrides["post"]($path, $data);
    }

    public function put($path, $data = array()) {
        return $this->overrides["put"]($path, $data);
    }

}

?>

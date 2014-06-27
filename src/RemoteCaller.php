<?php

interface RemoteCaller {

    public function get($url);
    public function post($url, $data = array());
    public function put($url, $data = array());

}

?>

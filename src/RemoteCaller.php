<?php

interface RemoteCaller {

    public function get($url);
    public function post($url, $data = array());

}

?>

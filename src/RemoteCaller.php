<?php

interface RemoteCaller {

    public function clientUrl($url, $query = array());
    public function get($url);
    public function post($url, $data = array());
    public function put($url, $data = array());

}


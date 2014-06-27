<?php

class CurlRemoteCaller implements RemoteCaller {

    private $base_url;
    private $user;
    private $password;
    private $insecure;

    public function __construct($url) {
       $this->base_url = $url; 
       $this->insecure = false;
    }

    public function __set($name, $value) {
        if($name == "user" || $name == "password" || $name == "insecure")
            $this->$name = $value;
    }

    public function get($path) {
        $call_url = http_build_url(($this->base_url)."/".$path,
            array(
                "user" => $this->user,
                "pass" => $this->password
            )
        );
        $ch = curl_init($call_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        if($this->insecure) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        }

        $result = curl_exec($ch);
        if($result) {
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if($http_code >= 400 && $http_code < 500) {
                switch($http_code) {
                    case 401:
                        throw new UnauthorizedException("Unauthorized access on $call_url.\nReturned:\n".substr($result,0,100)."...");
                    case 404:
                        throw new NotFoundException("Request not found: $call_url.\nReturned:\n".substr($result,0,100)."...");
                    default:
                        throw new ClientErrorException("A client error ($http_code) occured while calling $call_url.\nReturned:\n".substr($result,0,100)."...");
                }
            }
            else if($http_code >= 500 && $http_code < 600) {
                throw new ServerErrorException("A server error ($http_code) occured while calling $call_url.\nReturned:\n".substr($result,0,100)."...");
            }

            return $result;
        }
        else {
            throw new RemoteCallerException("Failed to perform HTTP call. cURL error (".curl_errno($ch)."): ".curl_error($ch));
        }
    }

    public function post($url, $data = null) {
        throw new Exception("Not implemented");
    }

}

?>

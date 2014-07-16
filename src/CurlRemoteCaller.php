<?php

class CurlRemoteCaller implements RemoteCaller {

    private $base_url;
    private $user;
    private $password;
    private $insecure;
    private $curl;

    public function __construct($url) {
        $this->curl = new Curl();
        $this->base_url = $url; 
        $this->insecure = false;
    }

    public function __set($name, $value) {
        if($name == "user" || $name == "password") {
            $this->$name = $value;

            $this->base_url = http_build_url(($this->base_url), array(
                "user" => $this->user,
                "pass" => $this->password
            ));
        }

        if($name == "insecure") {
            $this->insecure = $value;

            if($value) {
                $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, 0);
                $this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, 0);
            }
            else {
                $this->curl->setOpt(CURLOPT_SSL_VERIFYPEER, 1);
                $this->curl->setOpt(CURLOPT_SSL_VERIFYHOST, 1);
            }
        }
    }

    public function clientUrl($path, $query = array()) {
        if(!isset($this->user))
            throw new LogicException("No client set; cannot generate client url. Please set CurlRemoteCaller->user.");

        $query["client_id"] = $this->user;

        $first_value = true;
        $q = "";
        foreach($query as $key => $value) {
            if($first_value) {
                $q .= $key."=".$value;
                $first_value = false;
            }
            else {
                $q .= "&".$key."=".$value;
            }
        }

        $result = http_build_url($this->base_url, array(), HTTP_URL_STRIP_USER | HTTP_URL_STRIP_PASS);
        $result = http_build_url($result, array(
            'path' => '/'.$path,
            'query' => $q
        ));

        return $result;
    }

    public function get($path) {
        $call_url = http_build_url($this->base_url, array(
            "path" => '/'.$path
        ));
        $this->curl->get($call_url);
        return $this->handleResult($call_url, $this->curl->raw_response);
    }

    public function post($path, $data = array()) {
        $call_url = http_build_url($this->base_url, array(
            "path" => '/'.$path
        ));
        $this->curl->post($call_url, $data);
        return $this->handleResult($call_url, $this->curl->raw_response);
    }

    public function put($path, $data = array()) {
        $call_url = http_build_url($this->base_url, array(
            "path" => '/'.$path
        ));
        $this->curl->put($call_url, $data);
        return $this->handleResult($call_url, $this->curl->raw_response);
    }

    private function handleResult($call_url, $result) {
        if($this->curl->error) {
            if($this->curl->curl_error) {
                throw new RemoteCallerException("Failed to perform HTTP call. cURL error (".$this->curl->error_code."): ".$this->curl->error_message);
            }

            if($this->curl->error_code >= 400 && $this->curl->error_code < 500) {
                switch($this->curl->error_code) {
                    case 401:
                        throw new UnauthorizedException("Unauthorized access on $call_url.\nReturned:\n".substr($result,0,100)."...");
                    case 402:
                        throw new PaymentRequiredException("Payment required to access content at $call_url.\nReturned:\n".substr($result,0,100)."...");
                    case 404:
                        throw new NotFoundException("Request not found: $call_url.\nReturned:\n".substr($result,0,100)."...");
                    default:
                        throw new ClientErrorException("A client error (".$this->curl->error_code.") occured while calling $call_url.\nReturned:\n".substr($result,0,100)."...");
                }
            }
            else if($this->curl->error_code >= 500 && $this->curl->error_code < 600) {
                throw new ServerErrorException("A server error (".$this->curl->error_code.") occured while calling $call_url.\nReturned:\n".substr($result,0,100)."...");
            }
        }

        return $result;
    }

}

?>

<?php

class ClientErrorException extends RemoteCallerException {
    public function __construct($msg) {
        parent::__construct($msg);
    }
}

?>

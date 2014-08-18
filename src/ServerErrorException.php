<?php

class ServerErrorException extends RemoteCallerException {
    public function __construct($msg) {
        parent::__construct($msg);
    }
}


<?php

class UnauthorizedException extends RemoteCallerException {
    public function __construct($msg) {
        parent::__construct($msg);
    }
}

?>

<?php
require_once "RemoteCallerException.php";

class AccessDeniedException extends RemoteCallerException {

    public function __construct($msg) {
        parent::__construct($msg);
    }

}


<?php

class ServerErrorException extends Exception {
    public function __construct($msg) {
        parent::__construct($msg);
    }
}

?>

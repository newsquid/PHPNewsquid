<?php

class AccessDeniedException extends Exception {

    public function __construct($msg) {
        parent::__construct($msg);
    }

}

?>

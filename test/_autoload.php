<?php

require_once "src/http_build_url.php";

function nsq_test_autoload($class_name) {
    if(file_exists("src/$class_name.php"))
        require_once "src/$class_name.php";
}

spl_autoload_register("nsq_test_autoload");

?>

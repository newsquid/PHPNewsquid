<?php

class NewsquidTest extends PHPUnit_Framework_TestCase {

    public function test_CreateProduct_Mocked() {
        global $post_called;
        $post_called = false;

        $caller = new MockRemoteCaller(array(
            "post" => function($path, $data) {
                global $post_called;
                $post_called = true;

                if($path != "products")
                    throw new Exception("Wrong path called ($path)");
                if(!is_array($data))
                    throw new Exception("data is now an array!");

                if(!is_array($data["product"]))
                    throw new Exception("Product array of data not found.");
                if($data["product"]["sku"] != 1)
                    throw new Exception("Product id wrong");

                if($data["access_token"] != "token")
                    throw new Exception("Access token wrong :(");
            }
        ));

        $nsq = new Newsquid($caller, "user", "pass", true);
        $user = new NewsquidUser(1337, "USR", "USR@USR.com", "token", $caller);
        $prod = $nsq->createProduct(1, "Hello", 10, "USD", "http://gog.com", $user);
       
        $this->assertInstanceOf("NewsquidProduct", $prod); 
        $this->assertTrue($post_called, "->post was not called");
    }
}

?>

<?php

class NewsquidTest extends PHPUnit_Framework_TestCase {

    public function test_CreateProduct_Mocked() {
        global $post_called;
        $post_called = false;

        $caller = new MockRemoteCaller(array(
            "post" => function($path, $data) {
                global $post_called;
                $post_called = true;

                if($path != "api/v2/products")
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

    public function test_Newsquid_LogInUri() {
        global $path_grab, $query_grab;
        $path_grab = null;
        $query_grab = array();

        $uri_caller = new MockRemoteCaller(array(
            "clientUrl" => function($path, $query) {
                global $path_grab, $query_grab;
                $path_grab = $path;
                $query_grab = $query;

                return "correct_uri";
            }
        ));

        $newsquid = new Newsquid($uri_caller, "nsq_usr", "nsq_pass", true);

        $uri = $newsquid->logInUri("http://back.to.me");

        $this->assertEquals("correct_uri", $uri);
        $this->assertEquals("oauth/authorize", $path_grab);
        $this->assertEquals("http://back.to.me", $query_grab["redirect_uri"]);
        $this->assertEquals("login", $query_grab["scope"]);
        $this->assertEquals("code", $query_grab["response_type"]);
    }

    public function test_GetProduct() {
        global $path_get;
        $path_get = null;

        $mocked_caller = new MockRemoteCaller(array(
            "get" => function($path) {
                global $path_get;
                $path_get = $path;

                return json_encode(array(
                    "id" => 9,
                    "title" => "test",
                    "price" => 2,
                    "currency" => "DKK",
                    "url" => "http://test.nsquid.co"
                ));
            }
        ));

        $nsq = new Newsquid($mocked_caller, "app", "app_secret", true);

        $prod = $nsq->getProduct(9);

        $this->assertEquals("api/v2/products/9", $path_get);
        $this->assertInstanceOf("NewsquidProduct", $prod);
        $this->assertEquals(9, $prod->id);
        $this->assertEquals("test", $prod->title);
        $this->assertEquals(2, $prod->price);
        $this->assertEquals("DKK", $prod->currency);
        $this->assertEquals("http://test.nsquid.co", $prod->url);
    }
}

?>

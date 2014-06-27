<?php

class NewsquidProductTest extends PHPUnit_Framework_TestCase {
    
    private $empty_caller;
    private $some_user;

    public function setUp() {
        $this->empty_caller = new MockRemoteCaller();
        $this->some_user = new NewsquidUser(1, "Mr. Watson", "mr@watson.com", "sekrit tokins", $this->empty_caller);
    }

    public function test_Product_HasChanged_Offline() {
        $prod = new NewsquidProduct(1, "Yolo", 10.0, "USD", "http://lol.com", $this->some_user, $this->empty_caller);

        $this->assertFalse($prod->hasChanged());

        $prod->title = "Hello, world.";

        $this->assertTrue($prod->hasChanged());
    }

    public function test_Product_HasChanged_Sync() {
        $indifferent_caller = new MockRemoteCaller(array(
            "put" => function($path, $data) {}
        ));

        $prod = new NewsquidProduct(1, "Yolo", 10.0, "USD", "http://lol.com", $this->some_user, $indifferent_caller);
        $prod->title = "Hello, World";
        
        $this->assertTrue($prod->hasChanged());
        
        $prod->sync();

        $this->assertFalse($prod->hasChanged());
    }

    public function test_ProductSync_NothingChanged() {
        global $put_called;
        $put_called = false;

        $monitoring_caller = new MockRemoteCaller(array(
            "put" => function($path, $data) {
                global $put_called;
                $put_called = true;
            }
        ));

        $prod = new NewsquidProduct(1, "Yolo", 10.0, "USD", "http://lol.com", $this->some_user, $monitoring_caller);

        $prod->sync();

        $this->assertFalse($put_called);
    }

    public function test_ProductSync_ChangedOneThing() {
        global $data_put, $path_put;
        $data_put = array();
        $path_put = null;

        $grabbing_caller = new MockRemoteCaller(array(
            "put" => function($path, $data) {
                global $data_put, $path_put;
                $data_put = $data;
                $path_put = $path;
            }
        ));

        $prod = new NewsquidProduct(1, "Yolo", 10.0, "USD", "http://lol.com", $this->some_user, $grabbing_caller);

        $prod->title = "Hello, World";

        $prod->sync();

        $this->assertNotEmpty($data_put);
        $this->assertNotEmpty($data_put["product"]);
        $this->assertArrayHasKey("title", $data_put["product"]);
        $this->assertContains("Hello, World", $data_put["product"]);

        $this->assertEquals($path_put, "products/1");
    }

    public function test_ProductSync_ChangedEverything() {
        global $data_put, $path_put;
        $data_put = array();
        $path_put = null;

        $grabbing_caller = new MockRemoteCaller(array(
            "put" => function($path, $data) {
                global $data_put, $path_put;
                $data_put = $data;
                $path_put = $path;
            }
        ));

        $prod = new NewsquidProduct(1, "Yolo", 10.0, "USD", "http://lol.com", $this->some_user, $grabbing_caller);

        $prod->title = "Hello, World";
        $prod->price = 0.0;
        $prod->currency = "DKK";
        $prod->url = "http://nsquid.co";

        $prod->sync();

        $this->assertNotEmpty($data_put);
        $this->assertNotEmpty($data_put["product"]);
        $this->assertArrayHasKey("title", $data_put["product"]);
        $this->assertArrayHasKey("price", $data_put["product"]);
        $this->assertArrayHasKey("currency", $data_put["product"]);
        $this->assertArrayHasKey("url", $data_put["product"]);
        $this->assertEquals("Hello, World", $data_put["product"]["title"]);
        $this->assertEquals(0.0, $data_put["product"]["price"]);
        $this->assertEquals("DKK", $data_put["product"]["currency"]);
        $this->assertEquals("http://nsquid.co", $data_put["product"]["url"]);

        $this->assertEquals($path_put, "products/1");
    }
}

?>

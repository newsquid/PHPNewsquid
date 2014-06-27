<?php

class NewsquidProductTest extends PHPUnit_Framework_TestCase {
    
    private $empty_caller;
    private $some_user;

    public function setUp() {
        $this->empty_caller = new MockRemoteCaller();
        $this->some_user = new NewsquidUser(1, "Mr. Watson", "mr@watson.com", "sekrit tokins");
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

    }

    public function test_ProductSync_ChangedOneThing() {

    }

    public function test_ProductSync_ChangedEverything() {

    }

}

?>

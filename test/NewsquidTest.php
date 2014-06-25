<?php

/**
 * This test is pretty much an integration test, from top to bottom,
 * and as such requires a fake backend to exist.
 * This version relies on nsqor to be running on :1337 
 */
class NewsquidTest extends PHPUnit_Framework_TestCase {
    
    /**
     * @expectedException NewsquidException
     */
    public function test_NoServer_ConnectFails() {
        $nsq = new Newsquid("http://no.serv.er.exists.lol", "nil", "nil", true);
        $nsq->getProduct(1);
    }

    public function test_GetProduct_HTTP200() {
        $nsq = new Newsquid("https://localhost:1337", "uid_test", "secret_test", true);
        $product = $nsq->getProduct(1);
        $this->assertTrue(is_a($product, "NewsquidProduct"));
    }

    /**
     * @expectedException ProductNotFoundException
     */
    public function test_GetProduct_HTTP404() {
        $nsq = new Newsquid("https://localhost:1337", "uid_test", "secret_test", true);
        $product = $nsq->getProduct(9991928);
    }

    public function testGetAllUsers() {
        $nsq = new Newsquid("https://localhost:1337", "uid_test", "secret_test");
        
        $nsq->getUsers();
    }

}

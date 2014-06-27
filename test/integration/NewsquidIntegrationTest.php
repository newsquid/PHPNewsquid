<?php

/**
 * This test is pretty much an integration test, from top to bottom,
 * and as such requires a fake backend to exist.
 * This version relies on nsqor to be running on :1337 
 */
class NewsquidIntegrationTest extends PHPUnit_Framework_TestCase {
    
    /**
     * @expectedException RemoteCallerException
     */
    public function test_NoServer_ConnectFails() {
        $caller = new CurlRemoteCaller("http://no.serv.er.exists.lol");
        $nsq = new Newsquid($caller, "nil", "nil", true);
        $nsq->getProduct(1);
    }

    /**
     * @expectedException UnauthorizedException
     */
    public function test_CorrectServer_WrongUser_HTTP401() {
        $caller = new CurlRemoteCaller("https://localhost:1337/api/v2");
        $nsq = new Newsquid($caller, "nousr", "wrongpass", true);
        $nsq->getProduct(1);
    }

    public function test_GetProduct_HTTP200() {
        $caller = new CurlRemoteCaller("https://localhost:1337/api/v2");
        $nsq = new Newsquid($caller, "uid_test", "secret_test", true);
        $product = $nsq->getProduct(1);
        $this->assertTrue(is_a($product, "NewsquidProduct"));
    }

    /**
     * @expectedException NotFoundException
     */
    public function test_GetProduct_HTTP404() {
        $caller = new CurlRemoteCaller("https://localhost:1337/api/v2");
        $nsq = new Newsquid($caller, "uid_test", "secret_test", true);
        $product = $nsq->getProduct(9991928);
    }

    /**
     * Currently requires clean setup of newsquid server (otherwise product
     * already exists...
     */
    public function test_CreateProduct_HTTP200() {
        $caller = new CurlRemoteCaller("https://localhost:1337/api/v2");
        $nsq = new Newsquid($caller, "uid_test", "secret_test", true);
        $user = new NewsquidUser(2, "wrier_one", "writer_one@mail.com", "johnjohn");
        $product = $nsq->createProduct(999, "Hello, World", 1.0, "USD", "http://lol.com/5", $user);
    }
}

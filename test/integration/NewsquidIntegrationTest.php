<?php

/**
 * This test is pretty much an integration test, from top to bottom,
 * and as such requires a fake backend to exist.
 * This version relies on nsqor to be running on :1337 
 */
class NewsquidIntegrationTest extends PHPUnit_Framework_TestCase {
    
    private $local_caller;

    public function setUp() {
        $this->local_caller = new CurlRemoteCaller("https://".
		getenv('NSQOR_PORT_1337_TCP_ADDR').":".getenv('NSQOR_PORT_1337_TCP_PORT'));
    }

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
        $nsq = new Newsquid($this->local_caller, "nousr", "wrongpass", true);
        $nsq->getProduct(1);
    }

    public function test_NewsquidUser_logInUri_Correct() {
        $newsquid = new Newsquid($this->local_caller, "uid_test", "secret_test", true);
        $uri = $newsquid->logInUri("http://back.to.me");

	$this->assertTrue(strpos($uri,'https://'.
		getenv('NSQOR_PORT_1337_TCP_ADDR').":".getenv('NSQOR_PORT_1337_TCP_PORT').
		'/oauth/authorize') === 0, "Wrong beginning of uri in $uri");

        $this->assertTrue(strpos($uri,'client_id=uid_test') !== false, "Wrong or missing client id in uri $uri");
        $this->assertTrue(strpos($uri,'redirect_uri=http://back.to.me') !== false, "Wrong or missing redirect uri in uri $uri");
        $this->assertTrue(strpos($uri,'response_type=code') !== false, "Wrong or missing response type in uri $uri");
        $this->assertTrue(strpos($uri,'scope=login') !== false, "Wrong or missing scope in uri $uri");
    }

    public function test_GetProduct_HTTP200() {
        $nsq = new Newsquid($this->local_caller, "uid_test", "secret_test", true);
        $product = $nsq->getProduct(1);
        $this->assertTrue(is_a($product, "NewsquidProduct"));
    }

    /**
     * @expectedException NotFoundException
     */
    public function test_GetProduct_DoesntExist_HTTP404() {
        $nsq = new Newsquid($this->local_caller, "uid_test", "secret_test", true);
        $product = $nsq->getProduct(9991928);
    }

    /**
     * Currently requires clean setup of newsquid server (otherwise product
     * already exists...
     */
    public function test_NewsquidUser_CreateProduct_VerifyExistence() {
        $nsq = new Newsquid($this->local_caller, "uid_test", "secret_test", true);
        $user = new NewsquidUser(2, "wrier_one", "writer_one@trunktrunk.org", "johnjohn", $this->local_caller);
        $product = $nsq->createProduct(999, "Hello, World", 1.0, "USD", "http://lol.com/5", $user);

        $product_get = $nsq->getProduct(999);
        $this->assertEquals($product->title, $product_get->title);
        $this->assertEquals($product->price, $product_get->price);
        $this->assertEquals($product->currency, $product_get->currency);
        $this->assertEquals($product->url, $product_get->url);
    }

    public function test_Newsquid_getCurrentUser() {
        $token = "60c9ff9c351783ef57a1a85ae36c0537cee49bfaf409bd785721a8e2e0207c77";
        $nsq = new Newsquid($this->local_caller, "uid_test", "secret_test", true);
        $user = $nsq->getCurrentUser($token);
        $this->assertEquals(2, $user->id);
        $this->assertEquals("writer_one", $user->name);
        $this->assertEquals("writer_one@trunktrunk.org", $user->email);
        $this->assertEquals($token, $user->token);
    }

    public function test_Newsquid_getAccessToken(){
        $grant = "grant1";
        $nsq = new Newsquid($this->local_caller, "uid_test", "secret_test", true);

        $token = $nsq->getAccessToken($grant,"http://localhost");
        $user = $nsq->getCurrentUser($token);
        $this->assertNotEquals($user,false);
    }

}
# vim: set ts=4 sw=4 et:

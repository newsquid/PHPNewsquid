<?php

class NewsquidUserTest extends PHPUnit_Framework_TestCase {

    private $some_user;
    private $empty_caller;

    public function setUp() {
        $this->empty_caller = new MockRemoteCaller();
        $this->some_user = new NewsquidUser(1, "Some User", "some@user.com", "token", $this->empty_caller);
    }

    public function test_NewsquidUser_LogInUri() {
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

        $buyer = new NewsquidUser(13, "Test", "user@lol.com", "yes", $uri_caller);

        $uri = $buyer->logInUri("http://back.to.me");

        $this->assertEquals("correct_uri", $uri);
        $this->assertEquals("oauth/authorize", $path_grab);
        $this->assertEquals("http://back.to.me", $query_grab["redirect_uri"]);
        $this->assertEquals("login", $query_grab["scope"]);
        $this->assertEquals("code", $query_grab["response_type"]);
    }

    public function test_NewsquidUser_BuyProduct() {
        global $path_post, $data_post;
        $data_post = array();
        $path_post = null;

        $grabbing_caller = new MockRemoteCaller(array(
            "post" => function($path, $data) {
                global $path_post, $data_post;
                $data_post = $data;
                $path_post = $path;
            }
        ));

        $buyer = new NewsquidUser(13, "UserMan", "user@man.com", "tokenz", $grabbing_caller);

        $prod = new NewsquidProduct(1, "Teetl", 10.0, "USD", "http://lo.com", $grabbing_caller);

        $buyer->buyProduct($prod);

        $this->assertEquals("api/v2/consumer/orders", $path_post);

        $this->assertNotEmpty($data_post);
        $this->assertEquals("tokenz", $data_post["access_token"]);
        $this->assertEquals(1, $data_post["product"]["sku"]);
    }

    public function test_NewsquidUser_CanAccessProduct_Fail() {
        global $path_get;
        $path_get = null;

        $mocked_caller = new MockRemoteCaller(array(
            "get" => function($path) {
                global $path_get;
                $path_get = $path;

                throw new PaymentRequiredException("Need payment to access");
            }
        ));

        $prod = new NewsquidProduct(3, "ehl", 10.0, "USD", "www.lolo.c", $mocked_caller);
        $user = new NewsquidUser(1, "usr", "usr@mail.usr", "token", $mocked_caller);

        $can_access = $user->canAccessProduct($prod);

        $this->assertFalse($can_access);
        $this->assertEquals("api/v2/consumer/access/3?access_token=token", $path_get);
    }

    public function test_NewsquidUser_CanAccessProduct_Success() {
        global $path_get;
        $path_get = null;

        $mocked_caller = new MockRemoteCaller(array(
            "get" => function($path) {
                global $path_get;
                $path_get = $path;

                return '{"access":true}';
            }
        ));

        $prod = new NewsquidProduct(3, "ehl", 10.0, "USD", "www.lolo.c", $mocked_caller);
        $user = new NewsquidUser(1, "usr", "usr@mail.usr", "token", $mocked_caller);

        $can_access = $user->canAccessProduct($prod);

        $this->assertTrue($can_access);
        $this->assertEquals("api/v2/consumer/access/3?access_token=token", $path_get);
    }

}

?>

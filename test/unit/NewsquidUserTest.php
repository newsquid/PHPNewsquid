<?php

class NewsquidUserTest extends PHPUnit_Framework_TestCase {

    private $some_user;
    private $empty_caller;

    public function setUp() {
        $this->empty_caller = new MockRemoteCaller();
        $this->some_user = new NewsquidUser(1, "Some User", "some@user.com", "token", $this->empty_caller);
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

        $this->assertEquals("consumer/orders", $path_post);

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

                return '{"reason":"no", "price": "10.0", "currency":"USD","access":false}';
            }
        ));

        $prod = new NewsquidProduct(3, "ehl", 10.0, "USD", "www.lolo.c", $mocked_caller);
        $user = new NewsquidUser(1, "usr", "usr@mail.usr", "token", $mocked_caller);

        $can_access = $user->canAccessProduct($prod);

        $this->assertFalse($can_access);
        $this->assertEquals("consumer/access/3?access_token=token", $path_get);
    }

}

?>

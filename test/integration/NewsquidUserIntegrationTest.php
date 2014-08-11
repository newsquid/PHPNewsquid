<?php

class NewsquidUserIntegrationTest extends PHPUnit_Framework_TestCase {

    private $reader_one, $writer_one;
    private $newsquid_caller;
    private $product_one, $product_bought_by_one;
    private $newsquid;

    public function setUp() {
	    $this->newsquid_caller = new CurlRemoteCaller("https://".
		getenv('NSQOR_PORT_1337_TCP_ADDR').":".getenv('NSQOR_PORT_1337_TCP_PORT'));

        $this->newsquid = new Newsquid($this->newsquid_caller, "uid_test", "secret_test", true);

        $this->reader_one = new NewsquidUser(1, "reader_one", "reader_one@trunktrunk.org", "50c9ff9c351783ef57a1a85ae36c0537cee49bfaf409bd785721a8e2e0207c77", $this->newsquid_caller);
        $this->writer_one = new NewsquidUser(2, "writer_one", "writer_one@trunktrunk.org", "40c9ff9c351783ef57a1a85ae36c0537cee49bfaf409bd785721a8e2e0207c77", $this->newsquid_caller);

        //Product owned by writer_one
        $this->product_one = new NewsquidProduct(1, "product_one", 11, "USD", "url_one", $this->newsquid_caller);
        $this->product_bought_by_one = new NewsquidProduct(5, "product_title", 1.1, "USD", "bought_url", $this->newsquid_caller);
    }

    public function test_NewsquidUser_CanAccessProduct_Fail() {
        $r1_can_access = $this->reader_one->canAccessProduct($this->product_one);

        $this->assertFalse($r1_can_access);
    }

    public function test_NewsquidUser_CanAccessProduct_OwnerSuccess() {
        $w1_can_access = $this->writer_one->canAccessProduct($this->product_one);

        $this->assertTrue($w1_can_access);
    }

    public function test_NewsquidUser_CanAccessProduct_BoughtSuccess() {
        $r1_can_access = $this->reader_one->canAccessProduct($this->product_bought_by_one);

        $this->assertTrue($r1_can_access);
    }

    public function test_NewsquiUser_BuyProduct_VeryfiyAccess() {
        $prod = $this->newsquid->createProduct(23, "Test Product", 10, "USD", "http://thismyshit.com", $this->writer_one);

        $can_access_before = $this->reader_one->canAccessProduct($prod);

        $this->assertFalse($can_access_before);

        $this->reader_one->buyProduct($prod);

        $can_access_after = $this->reader_one->canAccessProduct($prod);

        $this->assertTrue($can_access_after);
    }

}

?>

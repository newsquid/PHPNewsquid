<?php

class NewsquidUserIntegrationTest extends PHPUnit_Framework_TestCase {

    private $reader_one, $writer_one;
    private $newsquid_caller;
    private $product_one, $product_bought_by_one;

    public function setUp() {
        $this->newsquid_caller = new CurlRemoteCaller("https://localhost:1337");
        $this->newsquid_caller->insecure = true;
        $this->newsquid_caller->user = "uid_test";
        $this->newsquid_caller->password = "secret_test";
        
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

}

?>

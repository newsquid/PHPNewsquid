<?php

class CurlRemoteCallerTest extends PHPUnit_Framework_TestCase {

    /**
     * @expectedException LogicException
     */
    public function test_CurlRemoteCaller_clientUrl_noClient() {
        $caller = new CurlRemoteCaller("http://test.me");

        $uri = $caller->clientUrl("test_url");
    }

    public function test_CurlRemoteCaller_clientUrl_simplePath() {
        $caller = new CurlRemoteCaller("http://test.me");
        $caller->user = "test_user";

        $uri = $caller->clientUrl("simple_path");

        $this->assertEquals("http://test.me/simple_path?client_id=test_user", $uri);
    }

    public function test_CurlRemoteCaller_clientUrl_urlEndsInSlash_simplePath() {
        $caller = new CurlRemoteCaller("http://test.me/");
        $caller->user = "test_user";

        $uri = $caller->clientUrl("simple_path");

        $this->assertEquals("http://test.me/simple_path?client_id=test_user", $uri);
    }

    public function test_CurlRemoteCaller_clientUrl_advancedUrl() {
        $caller = new CurlRemoteCaller("http://test.me");
        $caller->user = "test_user";

        $uri = $caller->clientUrl("simple_path", array(
            "arg0" => "val0",
            "arg1" => "val1"
        ));

        $this->assertTrue(strpos($uri,'arg0=val0') !== false, "Arg0 missing");
        $this->assertTrue(strpos($uri,'arg1=val1') !== false, "Arg1 missing");
        $this->assertTrue(strpos($uri,'http://test.me/simple_path') === 0, "Start of url wrong!");
    }

}

?>

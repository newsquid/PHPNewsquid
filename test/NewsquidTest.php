<?php

/**
 * This test is pretty much an integration test, from top to bottom,
 * and as such requires a fake backend to exist.
 * This version relies on nsqor to be running on :1337 
 */
class NewsquidTest extends PHPUnit_Framework_TestCase {
    
    public function testGetAllUsers() {
        $nsq = new Newsquid("https://localhost:1337", "uid_test", "secret_test");
        
        $nsq->getUsers();
    }

}

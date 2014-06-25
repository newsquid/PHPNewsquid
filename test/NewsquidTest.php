<?php

class NewsquidTest extends PHPUnit_Framework_TestCase {
    
    public function testGetAllUsers() {
        $nsq = new Newsquid("http://payment.nsquid.co", null);
        
        $nsq->getUsers();
    }

}

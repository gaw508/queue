<?php

namespace Gaw508\Queue;

use PHPUnit_Framework_TestCase;

/**
 * Class MessageTest
 *
 * @author George Webb <george@webb.uno>
 * @package Gaw508\Queue
 */
class MessageTest extends PHPUnit_Framework_TestCase
{
    public function testCreateNew()
    {
        $data = array('data' => 100);
        $message = Message::createNew($data);
        $this->assertInstanceOf('Gaw508\\Queue\\Message', $message);
        $this->assertEquals($data, $message->getData());
    }

    public function testCreateFromQueue()
    {
        $handle = 123;
        $data = array('data' => 100);
        $message = Message::createFromQueue($handle, json_encode($data));
        $this->assertInstanceOf('Gaw508\\Queue\\Message', $message);
        $this->assertEquals($data, $message->getData());
        $this->assertEquals($handle, $message->getHandle());
    }

    public function testSetGetData()
    {
        $data = array('data' => 100, 'data2' => 'blah');
        $message = new Message();
        $message->setData($data);
        $this->assertEquals($data, $message->getData());
    }

    public function testSetGetHandle()
    {
        $handle = 'string_handle';
        $message = new Message();
        $message->setHandle($handle);
        $this->assertEquals($handle, $message->getHandle());
    }

    public function testGetDataAsJson()
    {
        $data = array('data' => 100, 'data2' => 'blah');
        $message = new Message();
        $message->setData($data);
        $this->assertEquals(json_encode($data), $message->getDataAsJson());
    }
}

<?php

namespace Gaw508\Queue;

use PHPUnit_Framework_TestCase;

/**
 * Class QueueFactoryTest
 *
 * @author George Webb <george@webb.uno>
 * @package Gaw508\Queue
 */
class QueueFactoryTest extends PHPUnit_Framework_TestCase
{
    public function testCreateExistingQueueType()
    {
        $queue = QueueFactory::create('Sqs', array());
        $this->assertInstanceOf('Gaw508\\Queue\\Sqs', $queue);
    }

    public function testCreateNonExistingQueueType()
    {
        $queue = QueueFactory::create('Blah', array());
        $this->assertFalse($queue);
    }
}

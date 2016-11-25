<?php

namespace Gaw508\Queue;

use PHPUnit_Framework_TestCase;

/**
 * Class SqsTest
 *
 * @author George Webb <george@webb.uno>
 * @package Gaw508\Queue
 */
class SqsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Sqs
     */
    private $sqs;

    /**
     * @var string
     */
    private $queue_url;

    /**
     * @var int
     */
    private $long_poll_time;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $sqs_client_mock;

    public function setUp()
    {
        $this->sqs_client_mock = $this
            ->getMockBuilder('Aws\Sqs\SqsClient')
            ->setMethods(array('sendMessage', 'receiveMessage', 'deleteMessage', 'changeMessageVisibility'))
            ->disableOriginalConstructor()
            ->getMock();

        $this->queue_url = 'url123';
        $this->long_poll_time = 7;

        $this->sqs = new Sqs(array(
            'sqs_client' => $this->sqs_client_mock,
            'queue_url' => $this->queue_url,
            'long_poll_time' => $this->long_poll_time
        ));
    }

    public function testPut()
    {
        $data = array('data' => 10);
        $message = Message::createNew($data);

        $this->sqs_client_mock
            ->expects($this->once())
            ->method('sendMessage')
            ->with($this->equalTo(array(
                'QueueUrl' => $this->queue_url,
                'MessageBody' => $message->getDataAsJson()
            )));

        $this->sqs->put($message);
    }

    public function testPutThrowsException()
    {
        $this->setExpectedException(
            'Gaw508\Queue\Exception\QueueException',
            'Error putting message to SQS queue: my_message_123'
        );

        $data = array('data' => 10);
        $message = Message::createNew($data);

        $this->sqs_client_mock
            ->expects($this->once())
            ->method('sendMessage')
            ->will($this->throwException(new \Exception('my_message_123')));

        $this->sqs->put($message);
    }

    public function testGet()
    {
        $handle = 'my_handle';
        $data = array('data' => 10);

        $this->sqs_client_mock
            ->expects($this->once())
            ->method('receiveMessage')
            ->with($this->equalTo(array(
                'QueueUrl' => $this->queue_url,
                'WaitTimeSeconds' => $this->long_poll_time
            )))
            ->willReturn(array('Messages' => array(
                array(
                    'ReceiptHandle' => $handle,
                    'Body' => json_encode($data)
                )
            )));

        $actual_message = $this->sqs->get();

        $this->assertEquals($data, $actual_message->getData());
        $this->assertEquals($handle, $actual_message->getHandle());
    }

    public function testGetThrowsException()
    {
        $this->setExpectedException(
            'Gaw508\Queue\Exception\QueueException',
            'Error getting message from SQS queue: my_message_456'
        );

        $this->sqs_client_mock
            ->expects($this->once())
            ->method('receiveMessage')
            ->will($this->throwException(new \Exception('my_message_456')));

        $actual_message = $this->sqs->get();
    }

    public function testDelete()
    {
        $handle = 'my_handle_2';
        $data = array('data' => 10);
        $message = Message::createNew($data);
        $message->setHandle($handle);

        $this->sqs_client_mock
            ->expects($this->once())
            ->method('deleteMessage')
            ->with($this->equalTo(array(
                'QueueUrl' => $this->queue_url,
                'ReceiptHandle' => $handle
            )));

        $this->sqs->delete($message);
    }

    public function testDeleteThrowsException()
    {
        $this->setExpectedException(
            'Gaw508\Queue\Exception\QueueException',
            'Error deleting message from SQS queue: my_message_789'
        );

        $this->sqs_client_mock
            ->expects($this->once())
            ->method('deleteMessage')
            ->will($this->throwException(new \Exception('my_message_789')));

        $this->sqs->delete(Message::createNew(array()));
    }

    public function testRelease()
    {
        $handle = 'my_handle_2';
        $data = array('data' => 10);
        $message = Message::createNew($data);
        $message->setHandle($handle);

        $this->sqs_client_mock
            ->expects($this->once())
            ->method('changeMessageVisibility')
            ->with($this->equalTo(array(
                'QueueUrl' => $this->queue_url,
                'ReceiptHandle' => $handle,
                'VisibilityTimeout' => 0
            )));

        $this->sqs->release($message);
    }

    public function testReleaseThrowsException()
    {
        $this->setExpectedException(
            'Gaw508\Queue\Exception\QueueException',
            'Error releasing message back to SQS queue: my_message_012'
        );

        $this->sqs_client_mock
            ->expects($this->once())
            ->method('changeMessageVisibility')
            ->will($this->throwException(new \Exception('my_message_012')));

        $this->sqs->release(Message::createNew(array()));
    }
}

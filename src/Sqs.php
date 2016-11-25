<?php

namespace Gaw508\Queue;

use Aws\Sqs\SqsClient;
use Gaw508\Queue\Exception\QueueException;

/**
 * Sqs queue class
 *
 * Queue interface implementation for Amazon SQS
 *
 * @author George Webb <george@webb.uno>
 * @package Gaw508\Queue
 */
class Sqs implements QueueInterface
{
    /**
     * The sqs url
     *
     * @var string
     */
    private $sqs_url;

    /**
     * The sqs client
     *
     * @var SqsClient
     */
    private $sqs_client;

    /**
     * The long poll time in seconds - reduces number of requests to SQS API, defaults to 10 seconds
     *
     * @var SqsClient
     */
    private $long_poll_time;

    /**
     * Sqs constructor.
     *
     * @param string $config    Config data for the queue (specific to queue used)
     */
    public function __construct($config)
    {
        $this->sqs_client = isset($config['sqs_client']) ? $config['sqs_client'] : null;
        $this->sqs_url = isset($config['queue_url']) ? $config['queue_url'] : '';
        $this->long_poll_time = isset($config['long_poll_time']) ? $config['long_poll_time'] : 10;
    }

    /**
     * Puts a message object into the queue
     *
     * @param Message $message  The message object to insert
     * @throws QueueException   When the operation fails
     * @return void
     */
    public function put(Message $message)
    {
        try {
            // Send the message
            $this->sqs_client->sendMessage(array(
                'QueueUrl' => $this->sqs_url,
                'MessageBody' => $message->getDataAsJson()
            ));
        } catch (\Exception $e) {
            throw new QueueException('Error putting message to SQS queue: ' . $e->getMessage());
        }
    }

    /**
     * Gets a message off the queue and returns a Message object
     *
     * @throws QueueException   When the operation fails
     * @return Message|bool     The message object received from the queue or false if no message available
     */
    public function get()
    {
        try {
            // Receive a message from the queue
            $result = $this->sqs_client->receiveMessage(array(
                'QueueUrl' => $this->sqs_url,
                'WaitTimeSeconds' => $this->long_poll_time
            ));

            if ($result['Messages'] == null) {
                // No message to process
                return false;
            }

            // Get the message and return it
            $result_message = array_pop($result['Messages']);

            return Message::createFromQueue(
                $result_message['ReceiptHandle'],
                $result_message['Body']
            );
        } catch (\Exception $e) {
            throw new QueueException('Error getting message from SQS queue: ' . $e->getMessage());
        }
    }

    /**
     * Deletes a message off the queue
     *
     * @param Message $message  The message object to delete
     * @throws QueueException   When the operation fails
     * @return void
     */
    public function delete(Message $message)
    {
        try {
            // Delete the message
            $this->sqs_client->deleteMessage(array(
                'QueueUrl' => $this->sqs_url,
                'ReceiptHandle' => $message->getHandle()
            ));
        } catch (\Exception $e) {
            throw new QueueException('Error deleting message from SQS queue: ' . $e->getMessage());
        }
    }

    /**
     * Releases a message back to the queue, so it can picked up again
     *
     * @param Message $message  The message object to release
     * @throws QueueException   When the operation fails
     * @return void
     */
    public function release(Message $message)
    {
        try {
            // Set the visibility timeout to 0 to make the message visible in the queue again straight away
            $this->sqs_client->changeMessageVisibility(array(
                'QueueUrl' => $this->sqs_url,
                'ReceiptHandle' => $message->getHandle(),
                'VisibilityTimeout' => 0
            ));
        } catch (\Exception $e) {
            throw new QueueException('Error releasing message back to SQS queue: ' . $e->getMessage());
        }
    }
}

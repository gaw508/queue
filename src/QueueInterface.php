<?php

namespace Gaw508\Queue;

use Gaw508\Queue\Exception\QueueException;

/**
 * Interface Queue
 *
 * The interface of a queue object
 *
 * @author George Webb <george@webb.uno>
 * @package Gaw508\Queue
 */
interface QueueInterface
{
    /**
     * QueueInterface constructor.
     *
     * @param array $config     Config data for the queue (specific to queue used)
     */
    public function __construct($config);

    /**
     * Puts a message object into the queue
     *
     * @param Message $message  The message object to insert
     * @throws QueueException   When the operation fails
     * @return void
     */
    public function put(Message $message);

    /**
     * Gets a message off the queue and returns a Message object
     *
     * @throws QueueException   When the operation fails
     * @return Message|bool     The message object received from the queue or false if no message available
     */
    public function get();

    /**
     * Deletes a message off the queue
     *
     * @param Message $message  The message object to delete
     * @throws QueueException   When the operation fails
     * @return void
     */
    public function delete(Message $message);

    /**
     * Releases a message back to the queue, so it can picked up again
     *
     * @param Message $message  The message object to release
     * @throws QueueException   When the operation fails
     * @return void
     */
    public function release(Message $message);
}

<?php

namespace Gaw508\Queue;

/**
 * Message class
 *
 * A queue message, used by instances of queue interface
 *
 * @author George Webb <george@webb.uno>
 * @package Gaw508\Queue
 */
class Message
{
    /**
     * The data of the message
     *
     * @var array
     */
    private $data;

    /**
     * The message's handle from the queue
     *
     * @var string
     */
    private $handle;

    /**
     * Creates a new Message object from data
     *
     * @param array $data           The array of message data
     * @return static               The new Message object
     */
    public static function createNew($data)
    {
        $message = new static();
        $message->setData($data);
        return $message;
    }

    /**
     * Creates a new Message object with data and a queue handle
     *
     * @param string $handle        The handle of the message in the queue
     * @param string $data          JSON string of queue data
     * @return static               The new Message object
     */
    public static function createFromQueue($handle, $data)
    {
        $message = static::createNew(json_decode($data, true));
        $message->setHandle($handle);
        return $message;
    }

    /**
     * Sets the data param
     *
     * @param array $data   The array of data to set
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * Gets the current value of the data param
     *
     * @return array    Array of message data
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Gets the data as a JSON string
     *
     * @return string   JSON string of the data param
     */
    public function getDataAsJson()
    {
        return json_encode($this->getData());
    }

    /**
     * Sets the handle of the message
     *
     * @param array $handle     The handle
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
    }

    /**
     * Gets the queue handle of the message
     *
     * @return string   The handle
     */
    public function getHandle()
    {
        return $this->handle;
    }
}

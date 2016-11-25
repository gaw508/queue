<?php

namespace Gaw508\Queue;

/**
 * QueueFactory class
 *
 * Creates instances of QueueInterface of given type
 *
 * @author George Webb <george@webb.uno>
 * @package Gaw508\Queue
 */
class QueueFactory
{
    /**
     * Creates a new queue instance of a given type
     *
     * @param string $queue_type    The type of queue, e.g. Sqs
     * @param string $config        Config data for the queue (specific to queue used)
     * @return QueueInterface       The queue instance
     */
    public static function create($queue_type, $config)
    {
        $class = "Gaw508\\Queue\\$queue_type";
        return new $class($config);
    }
}

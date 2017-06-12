<?php

namespace Queue;

class RedisQueue implements MessageQueueInterface
{
    protected $redis;

    protected $channel;

    public function __construct(\Redis $redis, string $channel = 'default')
    {
        $this->redis = $redis;
        $this->channel = $channel;
    }

    public function count(): int
    {
        return $this->redis->lSize('queue:' . $this->channel);
    }

    public function push(string $message)
    {
        // append to the end (right)
        $this->redis->rPush('queue:' . $this->channel, $message);
    }

    public function pop(): string
    {
        if (! $this->count()) {
            throw new \RuntimeException('Queue is empty');
        }

        // pop from the start (left)
        return $this->redis->lPop('queue:' . $this->channel);
    }
}

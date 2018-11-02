<?php

namespace Queue;

class RedisQueue implements MessageQueueInterface
{
    use ChannelTrait;

    protected $redis;

    public function __construct(\Redis $redis, string $channel = 'default')
    {
        $this->redis = $redis;
        $this->setChannel($channel);
    }

    public function count(): int
    {
        return $this->redis->lSize('queue:' . $this->getChannel());
    }

    public function push(string $message)
    {
        // append to the end (right)
        $this->redis->rPush('queue:' . $this->getChannel(), $message);
    }

    public function pop(): string
    {
        if (! $this->count()) {
            throw new \RuntimeException('Queue is empty');
        }

        // pop from the start (left)
        return $this->redis->lPop('queue:' . $this->getChannel());
    }
}

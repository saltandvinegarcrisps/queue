<?php

namespace Queue;

class ArrayQueue implements MessageQueueInterface
{
    use ChannelTrait;

    protected $map;

    public function __construct(
        array $map = [],
        string $channel = 'default'
    ) {
        $this->map = $map;
        $this->setChannel($channel);
    }

    public function count(): int
    {
        return count($this->map);
    }

    public function push(string $message)
    {
        array_push($this->map, $message);
    }

    public function pop(): string
    {
        if (! $this->count()) {
            throw new \RuntimeException('Queue is empty');
        }

        return array_pop($this->map);
    }
}

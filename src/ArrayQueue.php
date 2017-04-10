<?php

namespace Queue;

class ArrayQueue implements MessageQueueInterface
{
    protected $map;

    public function __construct(array $map = [])
    {
        $this->map = $map;
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
        if( ! $this->count()) {
            throw new \RuntimeException('Queue is empty');
        }

        return array_pop($this->map);
    }
}

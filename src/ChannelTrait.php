<?php

namespace Queue;

trait ChannelTrait
{
    protected $channel;

    public function setChannel(string $channel): void
    {
        $this->channel = $channel;
    }

    public function getChannel(): string
    {
        return $this->channel;
    }
}

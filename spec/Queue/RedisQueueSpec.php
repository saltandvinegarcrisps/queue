<?php

namespace spec\Queue;

use Queue\RedisQueue;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class RedisQueueSpec extends ObjectBehavior
{
    function it_should_remove_a_message_from_the_queue(\Redis $redis)
    {
        $redis->lSize('queue:default')->willReturn(1);
        $redis->lPop('queue:default')->willReturn('test_1');

        $this->beConstructedWith($redis);
        $this->pop()->shouldEqual('test_1');
    }

    function it_should_add_a_message_to_the_queue(\Redis $redis)
    {
        $redis->rPush('queue:default', 'test_2')->shouldBeCalled();
        $redis->lSize('queue:default')->willReturn(1);
        $redis->lPop('queue:default')->willReturn('test_2');

        $this->beConstructedWith($redis);

        $this->push('test_2');
        $this->pop()->shouldEqual('test_2');
    }
}

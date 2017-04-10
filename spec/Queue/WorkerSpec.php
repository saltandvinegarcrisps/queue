<?php

namespace spec\Queue;

use Queue\Worker;
use Queue\MessageQueueInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class WorkerSpec extends ObjectBehavior
{
    function let(MessageQueueInterface $queue)
    {
        $this->beConstructedWith($queue);
    }

    function it_should_process_a_message(MessageQueueInterface $queue)
    {
        $queue->count()->willReturn(1);
        $queue->pop()->willReturn('test');

        $this->beConstructedWith($queue, function() {});
        $this->runOnce()->shouldReturn(true);
    }
}

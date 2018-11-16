<?php

namespace spec\Queue;

use Queue\MessageQueueInterface;
use PhpSpec\ObjectBehavior;

class WorkerSpec extends ObjectBehavior
{
    public function let(MessageQueueInterface $queue)
    {
        $this->beConstructedWith($queue);
    }

    public function it_should_process_a_message(MessageQueueInterface $queue)
    {
        $queue->count()->willReturn(1);
        $queue->pop()->willReturn('test');

        $this->beConstructedWith($queue, function () {
        });
        $this->runOnce()->shouldReturn(true);
    }
}

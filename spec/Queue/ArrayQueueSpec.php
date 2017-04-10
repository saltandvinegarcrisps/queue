<?php

namespace spec\Queue;

use Queue\ArrayQueue;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ArrayQueueSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ArrayQueue::class);
    }

    function it_should_remove_a_message_from_the_queue()
    {
        $this->beConstructedWith(['test_1']);
        $this->pop()->shouldEqual('test_1');
    }

    function it_should_add_a_message_to_the_queue()
    {
        $this->push('test_2');
        $this->pop()->shouldEqual('test_2');
    }

    function it_should_return_a_count_of_messages()
    {
        $this->count()->shouldEqual(0);
        $this->push('test_3');
        $this->push('test_4');
        $this->push('test_5');
        $this->count()->shouldEqual(3);
        $this->pop();
        $this->count()->shouldEqual(2);
    }
}

<?php

namespace spec\Queue;

use Queue\PdoQueue;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PdoQueueSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(new \PDO('sqlite::memory:'));
    }

    function it_should_remove_a_message_from_the_queue()
    {
        $pdo = new \PDO('sqlite::memory:');
        $sql = 'CREATE TABLE IF NOT EXISTS queue (
            id CHAR(10) NOT NULL PRIMARY KEY,
            created_at DATETIME(6) NOT NULL,
            message TEXT NOT NULL
        )';
        $pdo->exec($sql);
        $sql = 'CREATE INDEX IF NOT EXISTS queue.created_at ON (created_at)';
        $pdo->exec($sql);
        $sql = 'INSERT INTO queue (id, created_at, message) VALUES(\'abcdefghij\', \'2000-01-01 00:00:00.000001\', \'test_1\')';
        $pdo->exec($sql);

        $this->beConstructedWith($pdo);

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

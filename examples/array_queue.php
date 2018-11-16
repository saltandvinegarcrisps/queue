<?php

require __DIR__ . '/../vendor/autoload.php';

class CsvReport
{
    public function handle(string $format, string $email)
    {
        echo 'handled '.$format.' sent to '.$email.PHP_EOL;
    }
}

$queue = new Queue\ArrayQueue;
$queue->setChannel('reports');

$queue->push(new Queue\Job(CsvReport::class, ['csv', 'bob@acme.com']));
$queue->push(new Queue\Job(CsvReport::class, ['xml', 'bill@acme.com']));
$queue->push(new Queue\Job(CsvReport::class, ['pdf', 'baz@acme.com']));
$queue->push(new Queue\Job(CsvReport::class, ['jpg', 'ben@acme.com']));

$handler = function (string $message) use ($queue) {
    $job = Queue\Job::parse($message);

    echo PHP_EOL;
    echo 'Received job'.PHP_EOL;
    echo 'Attempts: '.$job->getAttempts().PHP_EOL;
    echo 'Status: '.$job->getStatus().PHP_EOL;
    echo 'Task: '.$job->getTask().PHP_EOL;

    if ($job->getAttempts() > 3) {
        echo 'Received failed'.PHP_EOL;
        return;
    }

    if ($job->getStatus() == $job::STATUS_PENDING && $job->getArgs()[0] == 'pdf') {
        $job->failing();
        return $queue->push($job);
    }

    if ($job->getStatus() == $job::STATUS_FAILING) {
        $job->retry();
        return $queue->push($job);
    }

    [$class, $method] = explode('@', $job->getTask(), 2);

    $ref = new ReflectionClass($class);
    $ref->getMethod($method)->invokeArgs(new $class, $job->getArgs());
};

$worker = new Queue\Worker($queue, $handler, 4);
$worker->run();

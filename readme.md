### Example

ReportJob.php

    $queue = new Queue\ArrayQueue;
    $queue->setChannel('reports');

    $job = new Queue\Job('Report@export', ['csv', 'bob@acme.com']);
    $queue->push($job);

    $job = new Queue\Job(['Report', 'export'], ['csv', 'bob@acme.com']);
    $queue->push($job);

    $job = new Queue\Job('Report', ['csv', 'bob@acme.com']); // Report@handle
    $queue->push($job);

    $job = new Queue\Job(Report::class, ['csv', 'bob@acme.com']); // Report@handle
    $queue->push($job);

ReportsWorker.php

    $queue = new Queue\ArrayQueue;
    $queue->setChannel('reports');

    $handler = function(string $message) {
        $job = Queue\Job::parse($message);

        // maybe check attempts
        if($job->getAttempts() > 3) {
            $logger->error('Job Failed', ['job' => $job]);
        }

        $task = $job->getTask(); // Report@export
        [$class, $action] = explode('@', $task, 2);

        $ref = ReflectionClass($class);
        $result = $ref->getMethod($action)->invokeArgs(new $class, $job->getArgs());
    };

    $interval = 10; // seconds
    $worker = new Queue\Worker($queue, $handler, $interval);
    $worker->run();

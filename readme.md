### Example

Queues:

	$queue = new Queue\ArrayQueue;
	$queue->push('some message or json string');

Runner:

	$interval = 10; // seconds
	$handler = function(string $message) {
		echo $message.PHP_EOL;
	};
	$worker = new Queue\Worker($queue, $handler, $interval);
	$worker->run();

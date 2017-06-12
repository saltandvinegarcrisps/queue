
Queues:

	$queue = new Queue\ArrayQueue;
	$queue->push('some message or json string');


Runner:

	$worker = new Queue\Worker($queue);
	$worker->run();


Queues:

	$memcached = new Memcached();
	$queue = new Queue\MemcachedQueue($memcached);
	$queue->push(new TestJob);


Runner:

	$memcached = new Memcached();
	$queue = new Queue\MemcachedQueue($memcached);
	$worker = new Queue\Worker($queue);
	$worker->run();

<?php

namespace Queue;

class Worker {

	protected $queue;

	protected $interval;

	protected $running;

	public function __construct(Queue $queue, $interval = 1) {
		$this->queue = $queue;
		$this->interval = $interval;
		$this->running = true;
	}

	public function runOnce() {
		$data = $this->queue->pop();

		if(false === $data) {
			return;
		}

		$vars = json_decode($data);

		$obj = unserialize($vars->job);

		$obj->run();
	}

	public function halt() {
		$this->running = false;
	}

	public function run() {
		while($this->running) {
			$this->runOnce();

			sleep($this->interval);
		}
	}

}

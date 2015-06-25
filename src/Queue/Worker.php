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

	public function runOnce($callback) {
		$vars = $this->queue->pop();

		if(false === $data) {
			return;
		}

		$callback($vars->job, $vars->data);
	}

	public function halt() {
		$this->running = false;
	}

	public function run($callback) {
		while($this->running) {
			$this->runOnce($callback);

			sleep($this->interval);
		}
	}

}

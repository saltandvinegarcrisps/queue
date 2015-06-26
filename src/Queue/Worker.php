<?php

namespace Queue;

class Worker {

	use Console;

	protected $queue;

	protected $interval;

	protected $handler;

	protected $running;

	public function __construct(MessageQueue $queue, $handler, $interval = 1) {
		$this->queue = $queue;
		$this->handler = $handler;
		$this->interval = $interval;
		$this->running = true;
	}

	public function runOnce() {
		if($this->queue->count()) {
			$message = $this->queue->pop();

			call_user_func($this->handler, $message);
		}
	}

	public function signal($signo) {
		$this->output($signo);
		$this->halt();
	}

	public function halt() {
		$this->success('Stopping worker');

		$this->running = false;
	}

	public function run() {
		$this->success('Starting worker');

		while($this->running) {
			$this->runOnce();

			sleep($this->interval);
		}
	}

}

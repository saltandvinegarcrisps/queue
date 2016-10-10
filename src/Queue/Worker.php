<?php

namespace Queue;

class Worker {

	use ConsoleTrait;

	protected $queue;

	protected $interval;

	protected $handler;

	protected $running;

	public function __construct(MessageQueue $queue, $handler, $interval = 0.5) {
		$this->queue = $queue;
		$this->handler = $handler;
		$this->interval = $interval * 1000000;
		$this->running = true;
	}

	public function runOnce() {
		if($this->queue->count()) {
			$message = $this->queue->pop();

			call_user_func($this->handler, $message);

			return true;
		}

		return false;
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
		$this->success('Queue Size ' . $this->queue->count());

		while($this->running) {
			$this->runOnce() || usleep($this->interval);
		}
	}

}

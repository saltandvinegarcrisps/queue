<?php

namespace Queue;

trait Console {

	public function error($message) {
		fwrite(STDERR, sprintf("\033[0;31m%s\033[0m", $message).PHP_EOL);
	}

	public function success($message) {
		$this->output(sprintf("\033[0;32m%s\033[0m", $message));
	}

	public function output($message) {
		fwrite(STDOUT, $message.PHP_EOL);
	}

}

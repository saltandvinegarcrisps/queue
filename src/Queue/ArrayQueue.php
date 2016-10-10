<?php

namespace Queue;

class ArrayQueue implements MessageQueue, \Countable {

	protected $map;

	public function __construct() {
		$this->map = [];
	}

	public function count() {
		return count($this->map);
	}

	public function push(string $message) {
		array_push($this->map, $message);
	}

	public function pop(): string {
		return array_pop($this->map, $message);
	}

}

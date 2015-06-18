<?php

namespace Queue;

abstract class Queue {

	public function push(Job $obj) {
		$data = [
			'job' => serialize($obj),
		];

		$this->pushRaw(json_encode($data));
	}

	abstract public function pop();

}

<?php

namespace Queue;

abstract class Queue {

	public function push($job, array $data = []) {
		$str = $this->pack($job, array $data);

		$this->pushRaw($str);
	}

	protected function pack($job, array $data) {
		return json_encode(compact('job', 'data'));
	}

	protected function unpack($str) {
		return json_decode($str);
	}

	abstract public function pop();

}

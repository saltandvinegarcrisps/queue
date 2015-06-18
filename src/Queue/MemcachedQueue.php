<?php

namespace Queue;

class MemcachedQueue extends Queue implements \Countable {

	public function __construct(\Memcached $server) {
		$this->server = $server;
	}

	protected function key($position) {
		return sprintf('queue:%d', $position);
	}

	public function count() {
		$this->server->add('queue_position', 0);

		return $this->server->get('queue_position');
	}

	public function pushRaw($data) {
		$this->server->add('queue_position', 0);

		$this->server->increment('queue_position');

		$position = $this->server->get('queue_position');

		$this->server->set($this->key($position), $data);
	}

	public function pop() {
		$position = $this->server->get('queue_position');

		$key = $this->key($position);

		$value = $this->server->get($key);

		$this->server->decrement('queue_position');

		$this->server->delete($key);

		return $value;
	}

}

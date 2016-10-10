<?php

namespace Queue;

interface MessageQueue {

	public function push(string $message);

	public function pop(): string;

}

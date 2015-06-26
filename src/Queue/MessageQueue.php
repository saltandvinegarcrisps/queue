<?php

namespace Queue;

interface MessageQueue {

	public function push($message);

	public function pop();

}

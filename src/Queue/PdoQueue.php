<?php

namespace Queue;

class PdoQueue implements MessageQueue, \Countable {

	protected $pdo;

	protected $retries = 0;

	public function __construct(\PDO $pdo) {
		$this->pdo = $pdo;
		$this->createTable($this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME));
	}

	protected function createTable($driver) {
		switch($driver) {
			case 'sqlite':
				$sql = 'CREATE TABLE IF NOT EXISTS queue (id INTEGER NOT NULL PRIMARY KEY, data TEXT NOT NULL)';
				$this->pdo->exec($sql);
				break;
			case 'mysql':
				$sql = 'CREATE TABLE IF NOT EXISTS queue (id INT NOT NULL AUTO_INCREMENT, data TEXT NOT NULL, PRIMARY KEY (id))';
				$this->pdo->exec($sql);
				break;
		}
	}

	public function count() {
		$sth = $this->pdo->prepare('SELECT COUNT(id) FROM queue');
		$sth->execute();

		return $sth->fetchColumn();
	}

	public function push($message) {
		$sth = $this->pdo->prepare('INSERT INTO queue (data) VALUES(?)');
		$sth->execute([$message]);
	}

	public function pop() {
		try {
			$this->pdo->beginTransaction();

			$sth = $this->pdo->prepare('SELECT * FROM queue ORDER BY id ASC');
			$sth->execute();

			$row = $sth->fetch(\PDO::FETCH_OBJ);

			$sth = $this->pdo->prepare('DELETE FROM queue WHERE id = ?');
			$sth->execute([$row->id]);

			$this->pdo->commit();
		}
		catch(\PDOException $e) {
			// retry deadlock
			if($e->getCode() == 40001 || $e->getCode() == 1213) {
				if($this->retries > 3) {
					throw new \RuntimeException('Failed to recover from deadlock: ' . $e->getMessage());
				}

				$this->retries++;
				return $this->pop();
			}
			else {
				// contine throwing
				throw $e;
			}
		}

		// reset retries if everything worked
		$this->retries = 0;

		return $row->data;
	}

}

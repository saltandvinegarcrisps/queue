<?php

namespace Queue;

class PdoQueue extends Queue implements \Countable {

	protected $pdo;

	public function __construct(\PDO $pdo) {
		$this->pdo = $pdo;
	}

	protected function createTable() {
		$sql = 'CREATE TABLE queue (id INT PRIMARY KEY AUTO_INCREMENT, data TEXT NOT NULL)';
		$this->pdo->exec($sql);
	}

	public function count() {
		$sth = $this->pdo->prepare('SELECT COUNT(id) FROM queue');
		$sth->execute();

		return $sth->fetchColumn();
	}

	public function pushRaw($data) {
		$sth = $this->pdo->prepare('INSERT INTO queue (data) VALUES(?)');
		$sth->execute([$data]);
	}

	public function pop() {
		$sth = $this->pdo->prepare('SELECT * FROM queue ORDER BY id ASC');
		$sth->execute();

		$row = $sth->fetch(\PDO::FETCH_OBJ);

		$value = $this->unpack($row->data);

		$sth = $this->pdo->prepare('DELETE FROM queue WHERE id = ?');
		$sth->execute([$row->id]);

		return $value;
	}

}

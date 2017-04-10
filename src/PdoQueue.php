<?php

namespace Queue;

class PdoQueue implements MessageQueueInterface
{
    protected $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->createTable($this->pdo->getAttribute(\PDO::ATTR_DRIVER_NAME));
    }

    public function createTable(string $driver)
    {
        switch ($driver) {
            case 'sqlite':
                $sql = 'CREATE TABLE IF NOT EXISTS queue (
                    id CHAR(10) NOT NULL PRIMARY KEY,
                    created_at DATETIME(6) NOT NULL,
                    message TEXT NOT NULL
                )';
                $this->pdo->exec($sql);
                $sql = 'CREATE INDEX IF NOT EXISTS queue.created_at ON (created_at)';
                $this->pdo->exec($sql);
                break;
            case 'mysql':
                $sql = 'CREATE TABLE IF NOT EXISTS queue (
                    id CHAR(10) NOT NULL,
                    created_at DATETIME(6) NOT NULL,
                    message TEXT NOT NULL,
                    PRIMARY KEY (id),
                    INDEX created_at (created_at)
                )';
                $this->pdo->exec($sql);
                break;
            default:
                throw new \RuntimeException('unsupported database driver: '.$driver);
        }
    }

    public function count(): int
    {
        $sth = $this->pdo->prepare('SELECT COUNT(created_at) FROM queue');
        $sth->execute();

        return $sth->fetchColumn();
    }

    public function push(string $message)
    {
        $now = (new \DateTime)->format('Y-m-d H:i:s.u');
        $id = substr(hash('sha1', $now.$message), 0, 10);

        $sth = $this->pdo->prepare('INSERT INTO queue (id, created_at, message) VALUES(?, ?, ?)');
        $sth->execute([$id, $now, $message]);
    }

    public function pop(): string
    {
        if( ! $this->count()) {
            throw new \RuntimeException('Queue is empty');
        }

        for ($retries = 3; true; $retries--) {
            try {
                $this->pdo->beginTransaction();

                $sth = $this->pdo->prepare('SELECT id, message FROM queue ORDER BY created_at ASC LIMIT 1');
                $sth->execute();

                $row = $sth->fetch(\PDO::FETCH_OBJ);

                $sth = $this->pdo->prepare('DELETE FROM queue WHERE id = ?');
                $sth->execute([$row->id]);

                $this->pdo->commit();

                break;
            } catch (\PDOException $e) {
                $this->pdo->rollBack();

                if ($retries <= 0) {
                    throw $e;
                }
            }
        }

        return $row->message;
    }
}

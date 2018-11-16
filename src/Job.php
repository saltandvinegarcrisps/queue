<?php

namespace Queue;

use JsonSerializable;
use RuntimeException;
use InvalidArgumentException;

class Job implements JsonSerializable
{
    public const STATUS_PENDING = 'pending';

    public const STATUS_FAILING = 'failing';

    protected $task;

    protected $args;

    protected $status;

    protected $attempts;

    public function __construct($task, array $args, string $status = 'pending', int $attempts = 0)
    {
        $this->task = self::getValidTask($task);
        $this->args = $args;
        $this->status = $status;
        $this->attempts = $attempts;
    }

    /**
     * Get a valid task as a string
     * @param string|array
     * @return string
     */
    protected static function getValidTask($task): string
    {
        if (\is_string($task)) {
            // only contains class name
            if (false === \strpos($task, '@')) {
                return \sprintf('%s@handle', $task);
            }
            return $task;
        }

        if (\is_array($task)) {
            $values = \array_values($task);
            return \sprintf('%s@%s', $values[0], $values[1]);
        }

        throw new InvalidArgumentException('Task must be either a String
            "class@method" or Array with values [class, method]');
    }

    /**
     * Create a new job from json string
     * @param string
     * @return Job
     */
    public static function parse(string $json): self
    {
        $attrs = \json_decode($json, true);

        if (\json_last_error()) {
            throw new RuntimeException('Failed to json decode string: '.\json_last_error_msg());
        }

        if (false === \array_key_exists('task', $attrs)) {
            throw new RuntimeException('Message does not contain a valid job task');
        }

        if (false === \array_key_exists('args', $attrs)) {
            throw new RuntimeException('Message does not contain a valid job args');
        }

        return new self(
            self::getValidTask($attrs['task']),
            $attrs['args'],
            $attrs['status'] ?? self::STATUS_PENDING,
            $attrs['attempts'] ?? 0
        );
    }

    /**
     * Get number of attempts
     *
     * @return int
     */
    public function getAttempts(): int
    {
        return $this->attempts;
    }

    /**
     * Get job status
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Get job task
     *
     * @return string
     */
    public function getTask(): string
    {
        return $this->task;
    }

    /**
     * Get arguments
     *
     * @return array<mixed>
     */
    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * Mark the job for retry
     *
     * @return Job
     */
    public function retry(): self
    {
        $this->status = self::STATUS_PENDING;
        $this->attempts += 1;

        return $this;
    }

    /**
     * Mark job as failing
     *
     * @return Job
     */
    public function failing(): self
    {
        $this->status = self::STATUS_FAILING;

        return $this;
    }

    /**
     * Mark job as pending
     *
     * @return Job
     */
    public function pending(): self
    {
        $this->status = self::STATUS_PENDING;

        return $this;
    }

    /**
     * Json Encode job
     *
     * @return string
     */
    public function jsonSerialize(): string
    {
        $jsonStr = json_encode([
            'task' => $this->getTask(),
            'args' => $this->getArgs(),
            'status' => $this->getStatus(),
            'attempts' => $this->getAttempts(),
        ]);

        if (false === $jsonStr) {
            throw new RuntimeException('Failed to json encode string: '.\json_last_error_msg());
        }

        return $jsonStr;
    }

    /**
     * Stringify Job
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->jsonSerialize();
    }
}

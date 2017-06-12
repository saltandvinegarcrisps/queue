<?php

namespace Queue;

class Job
{
    protected $attributes;

    protected $defaults = [
        'attempts' => 0,
        'status' => 'pending',
        'task' => '\Queue\Debug@dump',
        'args' => [],
    ];

    public function __construct(array $attributes = [])
    {
        $this->attributes = array_merge($this->defaults, $attributes);
    }

    public static function parse(string $json): self
    {
        $attributes = json_decode($json, true);

        return new self($attributes);
    }

    public function attempts(): int
    {
        return $this->attributes['attempts'];
    }

    public function retry()
    {
        $this->attributes['status'] = 'pending';
        $this->attributes['attempts'] += 1;

        return $this;
    }

    public function status(): string
    {
        return $this->attributes['status'];
    }

    public function task(): string
    {
        return $this->attributes['task'];
    }

    public function args(): array
    {
        return $this->attributes['args'];
    }

    public function failing()
    {
        $this->attributes['status'] = 'failing';

        return $this;
    }

    public function pending()
    {
        $this->attributes['status'] = 'pending';

        return $this;
    }

    public function toJson(): string
    {
        return json_encode($this->attributes);
    }
}

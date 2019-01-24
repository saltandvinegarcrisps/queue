<?php

namespace Queue;

class Worker
{
    use ConsoleTrait;

    protected $queue;

    protected $interval;

    protected $handler;

    protected $running;

    public function __construct(MessageQueueInterface $queue, callable $handler, float $interval = 0.5)
    {
        $this->queue = $queue;
        $this->handler = $handler;
        $this->interval = $interval * 1000000;
        $this->running = true;
    }

    /**
     * Run a task from the queue
     *
     * @return boolean
     */
    public function runOnce(): bool
    {
        if ($this->queue->count() > 0) {
            $message = $this->queue->pop();
            
            $task = json_decode($message, true)['task'];
            $this->output(sprintf('[%s] Processing: %s', (new \DateTime)->format(\DateTime::RFC3339_EXTENDED), $task));

            try {
                \call_user_func($this->handler, $message);
            } catch (\Throwable $exception) {
                $this->error(sprintf('[%s] Failure: %s', (new \DateTime)->format(\DateTime::RFC3339_EXTENDED), $task));
                $this->error($exception->getMessage());
            }

            $this->success(sprintf('[%s] Finished: %s', (new \DateTime)->format(\DateTime::RFC3339_EXTENDED), $task));

            return true;
        }

        return false;
    }

    /**
     * Register signal handler
     */
    protected function registerSignalHandler(): void
    {
        $hangup = function (int $signo) {
            $this->output('Interrupt received: '.$signo);
            $this->halt();
        };

        \pcntl_async_signals(true);
        \pcntl_signal(SIGHUP, $hangup); // 1
        \pcntl_signal(SIGINT, $hangup); // 2
        \pcntl_signal(SIGQUIT, $hangup); // 3
        \pcntl_signal(SIGTERM, $hangup); // 15
    }

    /**
     * Stop the worker
     */
    public function halt(): void
    {
        $this->success('Stopping worker');
        $this->running = false;
    }

    /**
     * Start the worker
     */
    public function run(): void
    {
        $this->success('Starting worker');
        $this->registerSignalHandler();

        while ($this->running) {
            if ($this->runOnce()) {
                // No time for sleep!
                continue;
            }

            // if nothing ran we will sleep for now
            \usleep($this->interval);
        }
    }
}

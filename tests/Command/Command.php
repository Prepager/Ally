<?php

namespace ZapsterStudios\Ally\Tests\Command;

class Command
{
    /**
     * Store the command events.
     *
     * @var array
     */
    public $events = [];

    /**
     * Construct the command class.
     *
     * @return void
     */
    public function __construct()
    {
        $this->resetEvents();
    }

    /**
     * Reset events.
     *
     * @return void
     */
    public function resetEvents()
    {
        $this->events = [
            'line' => [],
            'info' => [],
            'comment' => [],
            'question' => [],
            'error' => [],
        ];
    }

    /**
     * Record all calls.
     *
     * @return void
     */
    public function __call($name, $arguments)
    {
        if (! isset($this->events[$name])) {
            $this->events[$name] = [];
        }

        $this->events[$name][] = $arguments[0];
    }
}

<?php

namespace ZapsterStudios\Ally\Commands\Publish;

class Publisher
{
    /**
     * Whether or not the files was published.
     *
     * @var bool
     */
    protected $moved = true;

    /**
     * Get the installation stubs folder dir.
     *
     * @return string
     */
    protected function stubs()
    {
        return explode('/src/', __DIR__)[0].'/install-stubs/';
    }

    /**
     * Move a file from install-stubs to laravel.
     *
     * @param  string  $source
     * @param  string  $dest
     * @return bool
     */
    protected function move($source, $dest)
    {
        $source = $this->stubs().$source;
        if (! $this->exists($source)) {
            return false;
        }

        $moved = copy($source, $dest);
        $this->moved = $this->moved && $moved;

        return $moved;
    }

    /**
     * Append another stub file to laravel.
     *
     * @param  string  $source
     * @param  string  $dest
     * @return bool
     */
    protected function append($source, $dest)
    {
        $source = $this->stubs().$source;
        if (! $this->exists($source) || ! $this->exists($dest)) {
            return false;
        }

        $moved = file_put_contents($dest, file_get_contents($source), FILE_APPEND);
        $this->moved = $this->moved && $moved;

        return $moved;
    }

    /**
     * Check that a file exists.
     *
     * @param  string  $dist
     * @return bool
     */
    protected function exists($dist)
    {
        if (! file_exists($dist)) {
            $this->command->comment('[✕] > File missing: '.$source);
            $this->moved = false;

            return false;
        }

        return true;
    }

    /**
     * Notify the user of the movement status.
     *
     * @param  string  $message
     * @return void;
     */
    protected function notify($message)
    {
        if ($this->moved) {
            $this->command->info('[✓] > '.$message);

            return;
        }

        $this->command->error('[✕] > '.$message);
    }
}

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
     * Construct the publishable command.
     *
     * @param  string  $command
     * @param  string  $testing
     * @return void
     */
    public function __construct($command, $testing)
    {
        $this->command = $command;
        $this->testing = $testing;
    }

    /**
     * Get the installation stubs folder dir.
     *
     * @param  string  $path
     * @return string
     */
    protected function stubs($path)
    {
        $base = explode('/src/', __DIR__)[0].'/install-stubs/';

        return realpath($base.$path);
    }

    /**
     * Move a file from install-stubs to laravel.
     *
     * @param  string  $source
     * @param  string  $dest
     * @return bool
     */
    protected function move($source, $folder, $file)
    {
        $source = $this->stubs($source);
        if (! $this->exists($source)) {
            return false;
        }

        $path = ($this->testing ? $this->testing : $folder).'/'.$file;

        $moved = copy($source, $path);
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
    protected function append($source, $folder, $file, $spacer = false)
    {
        $path = ($this->testing ? $this->testing : $folder).'/'.$file;

        $source = $this->stubs($source);
        if (! $this->exists($source) || ! $this->exists($path)) {
            return false;
        }

        $content = ($spacer ? $spacer : '').file_get_contents($source);

        $moved = file_put_contents($path, $content, FILE_APPEND);
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
        if (! $dist || ! file_exists($dist)) {
            $this->command->comment('[✕] > File missing: '.$dist);
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

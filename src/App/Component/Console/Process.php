<?php

declare(strict_types=1);

namespace App\Component\Console;

class Process
{

    /**
     * error
     *
     * @var Boolean
     */
    private $error;

    /**
     * error message
     *
     * @var String
     */
    private $errorMesage;

    /**
     * command to execute
     *
     * @var String
     */
    private $command;

    /**
     * execute ouput
     *
     * @var String
     */
    private $result;


    /**
     * instanciate
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * set command to be executed
     *
     * @param string $command
     * @return Process
     */
    public function setCommand(string $command): Process
    {
        $this->reset();
        $this->command = $command;
        return $this;
    }

    /**
     * return string result for a given system command
     *
     * @param string $command
     * @return string
     */
    public function run(): Process
    {
        if (!\function_exists('proc_open')) {
            $this->error = true;
            $this->errorMesage = 'undefined function proc_open';
            return null;
        }
        $this->error = true;
        $this->errorMesage = 'process is not a resource';
        $process = proc_open(
            $this->command,
            $this->getDescriptors(),
            $pipes
        );
        if (is_resource($process)) {
            $this->result = stream_get_contents($pipes[1]);
            $this->errorMesage = stream_get_contents($pipes[2]);
            $this->error = !empty($this->errorMesage);
            fclose($pipes[1]);
            fclose($pipes[2]);
            proc_close($process);
        }
        return $this;
    }

    /**
     * return true if error
     *
     * @return boolean
     */
    public function isError(): bool
    {
        return $this->error === true;
    }

    /**
     * returns error message
     *
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMesage;
    }

    /**
     * return execute result as string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->result;
    }

    /**
     * reset errors
     *
     * @return Process
     */
    protected function reset(): Process
    {
        $this->error = false;
        $this->errorMesage = '';
        $this->result = '';
        return $this;
    }

    /**
     * return pipes descriptors
     *
     * @return array
     */
    protected function getDescriptors(): array
    {
        return [
            ['pipe', 'r'], // stdin
            ['pipe', 'w'], // stdout
            ['pipe', 'w'], // stderr
        ];
    }
}

<?php

namespace App\Component\Console;

class Process
{

    /**
     * return string result for a given system command
     *
     * @param string $command
     * @return string
     */
    public static function readFromProcess(string $command): string
    {
        if (!\function_exists('proc_open')) {
            return null;
        }
        $descriptorspec = [
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];
        $process = proc_open($command, $descriptorspec, $pipes, null, null, ['suppress_errors' => false]);
        if (!\is_resource($process)) {
            return null;
        }
        $info = stream_get_contents($pipes[1]);

        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);
        return $info;
    }
}

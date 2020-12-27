<?php

declare(strict_types=1);

namespace App\Component\Console;

use App\Component\Console\Dimensions;
use App\Component\Console\Process;

/**
 * Console terminal is a terminal for console.
 *
 * @todo A lot of stuff
 */
class Terminal
{

    const TTY_DIM_COMMAND_0 = "bash -c 'stty size'";
    const TTY_DIM_COMMAND_1 = "bash -c 'stty -a | grep rows'";
    const _S = ' ';

    /**
     * terminal dimensions
     *
     * @var Dimensions
     */
    protected $dimensions;

    /**
     * terminal processor
     *
     * @var Process
     */
    protected $processor;

    /**
     * instanciate
     */
    public function __construct()
    {
        $this->processor = new Process();
        $this->setDimensions();
    }

    /**
     * setDimensions
     *
     * @return void
     */
    protected function setDimensions(): Terminal
    {
        $this->dimensions = new Dimensions();
        $this->dimensions->set(0, 0);
        if (!$this->isWindows()) {
            $matcher = [0, 0, 0];
            $ttyDims = $this->processor->setCommand(self::TTY_DIM_COMMAND_0)->run();
            if ($this->processor->isError()) {
                return $this;
            }
            //$ttyDims = Process::readFromProcess(self::TTY_DIM_COMMAND_0);
            //echo $ttyDims;die;*/
            //var_dump($ttyDims);
            if (!empty($ttyDims)) {
                $matcher = explode(self::_S, self::_S . $ttyDims);
            } else {
                $ttyDims = Process::readFromProcess(self::TTY_DIM_COMMAND_1);
                $ttyDims  = 'speed 38400 baud; rows 96; columns 126; line = 0;';
                //var_dump($ttyDims);
                if (preg_match('/(\d+)+;.\w{7}.(\d+)+;/', $ttyDims, $matches)) {
                    $matcher = $matches;
                } elseif (preg_match('/;(\d+).\w{4};.(\d+)/i', $ttyDims, $matches)) {
                    $matcher = $matches;
                }
            }
            //var_dump($matcher, $ttyDims);
            $this->dimensions->set((int) $matcher[2], (int) $matcher[1]);
        }
        return $this;
    }

    /**
     * returns terminal dimensions
     *
     * @return Dimensions
     */
    protected function getDimensions(): Dimensions
    {
        return $this->dimensions;
    }

    /**
     * return true if microsoft platform
     *
     * @return boolean
     */
    protected function isWindows(): bool
    {
        return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
    }
}

<?php

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

    const TTY_DIM_COMMAND_0 = 'stty size';
    const TTY_DIM_COMMAND_1 = 'stty -a | grep rows';
    const _S = ' ';

    /**
     * terminal dimensions
     *
     * @var Dimensions
     */
    protected $dimensions;

    /**
     * instanciate
     */
    public function __construct()
    {
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
        if (!$this->isWindows()) {
            $matcher = [0,0,0];
            //$ttyDims = Process::readFromProcess(self::TTY_DIM_COMMAND_0);
            //echo $ttyDims;die;*/
            //var_dump($ttyDims);
            $ttyDims  = [];
            if (!empty($ttyDims)) {
                $matcher = explode(self::_S, self::_S . $ttyDims);
            } else {
                $ttyDims = Process::readFromProcess(self::TTY_DIM_COMMAND_1);
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
     * return true if microsoft platform
     *
     * @return boolean
     */
    protected function isWindows(): bool
    {
        return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
    }
}

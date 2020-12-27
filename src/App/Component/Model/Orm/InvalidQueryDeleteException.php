<?php

declare(strict_types=1);

namespace App\Component\Model\Orm;

/**
 * This exception is triggered when delete query
 * is called without where condition
 *
 * @author Pierre Fromager <pf@pier_infor.fr>
 * @version 1.0
 */
class InvalidQueryDeleteException extends \Exception
{

    const MSG_CONDITION = 'Delete requires at least one condition';

    /**
     * constructor
     *
     * @param string $message
     * @param integer $code
     */
    public function __construct(string $message, int $code = 13)
    {
        parent::__construct($message, $code);
    }

    /**
     * retuns message
     *
     * @return string
     */
    public function __toString()
    {
        return $this->message;
    }
}

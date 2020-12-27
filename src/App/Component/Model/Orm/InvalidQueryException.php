<?php

declare(strict_types=1);

namespace App\Component\Model\Orm;

/**
 * This exception is triggered when
 * either query is not an objet
 * or query is not in allowed queries
 *
 * @author Pierre Fromager <pf@pier_infor.fr>
 * @version 1.0
 */
class InvalidQueryException extends \Exception
{

    const MSG_INSTANCE = 'Invalid query instance';
    const MSG_TYPE = 'Invalid query type';

    /**
     * constructor
     *
     * @param string $message
     * @param integer $code
     */
    public function __construct(string $message, int $code = 10)
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

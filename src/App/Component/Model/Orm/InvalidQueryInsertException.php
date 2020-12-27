<?php

declare(strict_types=1);

namespace App\Component\Model\Orm;

/**
 * This exception is triggered when insert query
 * is called without payload
 *
 * @author Pierre Fromager <pf@pier_infor.fr>
 * @version 1.0
 */
class InvalidQueryInsertException extends \Exception
{

    const MSG_PAYLOAD = 'Insert requires not empty payload';

    /**
     * constructor
     *
     * @param string $message
     * @param integer $code
     */
    public function __construct(string $message, int $code = 12)
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

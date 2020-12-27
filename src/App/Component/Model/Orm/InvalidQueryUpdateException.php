<?php

declare(strict_types=1);

namespace App\Component\Model\Orm;

/**
 * This exception is triggered when update query
 * is called without payload or where condition
 *
 * @author Pierre Fromager <pf@pier_infor.fr>
 * @version 1.0
 */
class InvalidQueryUpdateException extends \Exception
{

    const MSG_PAYLOAD = 'Update requires not empty payload';
    const MSG_CONDITION = 'Update requires at least where condition';

    /**
     * constructor
     *
     * @param string $message
     * @param integer $code
     */
    public function __construct(string $message, int $code = 11)
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

<?php

namespace App\Component\Db;

use ArrayAccess;
use Countable;

/**
 * Pool is a storage to store object identified by a string key
 */
class Pool implements ArrayAccess, Countable
{

    /**
     * connections
     *
     * @var array
     */
    private $connections = [];

    /**
     * Assigns a value to the specified offset
     *
     * @param string The offset to assign the value to
     * @param mixed  The value to set
     */
    public function offsetSet($connexionId, $connection)
    {
        if ($this->valid($connexionId, $connection)) {
            $this->connections[$connexionId] = $connection;
        }
    }

    /**
     * Whether or not an offset exists
     *
     * @param string An offset to check for
     * @return boolean
     */
    public function offsetExists($connexionId)
    {
        return isset($this->connections[$connexionId]);
    }

    /**
     * Unsets an offset
     *
     * @param string The offset to unset
     */
    public function offsetUnset($connexionId)
    {
        if ($this->offsetExists($connexionId)) {
            unset($this->connections[$connexionId]);
        }
    }

    /**
     * Returns the value at specified offset
     *
     * @param string The offset to retrieve
     * @return mixed
     */
    public function offsetGet($connexionId)
    {
        return $this->offsetExists($connexionId)
            ? $this->connections[$connexionId]
            : null;
    }

    /**
     * return number of connections
     *
     * @return integer
     */
    public function count()
    {
        return count($this->connections);
    }

    /**
     * return true id connectionId is not null and is string
     * and connection is object
     *
     * @param string $connexionId
     * @param object $connection
     * @return boolean
     */
    protected function valid($connexionId, $connection): bool
    {
        return (!is_null($connexionId)
            && is_string($connexionId)
            && is_object($connection));
    }
}

<?php

namespace KolayIK\Auth\Http\Parser;

trait KeyTrait
{
    /**
     * The key.
     *
     * @var string
     */
    protected $key = 'authorization_key';

    /**
     * Set the key.
     *
     * @param  string  $key
     *
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Get the key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
}

<?php

namespace KolayIK\Auth\Http\Parser;

use Illuminate\Http\Request;

class Parser
{
    /**
     * The chain.
     *
     * @var array
     */
    private $chain;

    /**
     * The request.
     *
     * @var \Illuminate\Http\Request
     */
    protected $request;

    /**
     * Parser constructor.
     * @param Request $request
     * @param array $chain
     */
    public function __construct(Request $request, array $chain = [])
    {
        $this->request = $request;
        $this->chain = $chain;
    }

    /**
     * Get the parser chain.
     *
     * @return array
     */
    public function getChain()
    {
        return $this->chain;
    }

    /**
     * Set the order of the parser chain.
     *
     * @param  array  $chain
     *
     * @return $this
     */
    public function setChain(array $chain)
    {
        $this->chain = $chain;

        return $this;
    }

    /**
     * Alias for setting the order of the chain.
     *
     * @param  array  $chain
     *
     * @return $this
     */
    public function setChainOrder(array $chain)
    {
        return $this->setChain($chain);
    }

    /**
     * Iterate through the parsers and attempt to retrieve
     * a value, otherwise return null.
     *
     * @return string|null
     */
    public function parseToken()
    {
        foreach ($this->chain as $parser) {
            if ($response = $parser->parse($this->request)) {
                return $response;
            }
        }
    }

    /**
     * Check whether a token exists in the chain.
     *
     * @return bool
     */
    public function hasToken()
    {
        return $this->parseToken() !== null;
    }

    /**
     * Set the request instance.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }
}

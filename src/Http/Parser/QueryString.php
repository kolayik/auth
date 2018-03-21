<?php

namespace KolayIK\Auth\Http\Parser;

use Illuminate\Http\Request;

class QueryString implements ParserInterface
{
    use KeyTrait;

    /**
     * Try to parse the token from the request query string.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return null|string
     */
    public function parse(Request $request)
    {
        return $request->query($this->key);
    }
}

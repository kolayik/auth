<?php

namespace KolayIK\Auth\Http\Parser;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class Cookies implements ParserInterface
{
    use KeyTrait;

    /**
     * Decrypt or not the cookie while parsing.
     *
     * @var bool
     */
    private $decrypt;

    public function __construct($decrypt = false)
    {
        $this->decrypt = $decrypt;
    }

    /**
     * Try to parse the token from the request cookies.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return null|string
     */
    public function parse(Request $request)
    {
        if ($this->decrypt && $request->hasCookie($this->key)) {
            return Crypt::decrypt($request->cookie($this->key));
        }

        return $request->cookie($this->key);
    }
}

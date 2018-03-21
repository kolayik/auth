<?php

namespace KolayIK\Auth\Http\Parser;

use Illuminate\Http\Request;

interface ParserInterface
{
    /**
     * Parse the request.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return null|string
     */
    public function parse(Request $request);
}

<?php

namespace KolayIK\Auth\Http\Middleware;

use Closure;
use KolayIK\Auth\Exceptions\KolayAuthException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class RefreshToken extends BaseMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->checkForToken($request);

        try {
            $token = $this->auth->parser->parseToken()->refresh();
        } catch (KolayAuthException $e) {
            throw new UnauthorizedHttpException('kolay-auth', $e->getMessage(), $e, $e->getCode());
        }
        $response = $next($request);

        return $this->setAuthenticationHeader($response, $token);
    }
}

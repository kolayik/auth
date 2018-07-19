<?php

namespace KolayIK\Auth\Http\Middleware;


use KolayIK\Auth\Authorizer;
use \Illuminate\Http\Request;
use KolayIK\Auth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

abstract class BaseMiddleware
{
    /**
     * @var Authorizer
     */
    protected $auth;

    /**
     * BaseMiddleware constructor.
     * @param Authorizer $auth
     */
    public function __construct(Authorizer $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param Request $request
     * @throws \Exception
     */
    public function authenticate(Request $request)
    {
        try {
            $auth = $this->auth->authenticate();
            if (!$auth) {
                if ($auth instanceof TokenInvalidException) {
                    throw $auth;
                }
                throw new UnauthorizedHttpException('kolay-auth', 'User not found');
            }
        } catch (\Exception $e) {
            if ($e instanceof TokenInvalidException) {
                throw $e;
            }
            throw new UnauthorizedHttpException('kolay-auth', 'User not found', $e, $e->getCode());
        }
    }

    /**
     * Check the request for the presence of a token.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     *
     * @return void
     */
    public function checkForToken(Request $request)
    {
        if (! $this->auth->parser->setRequest($request)->hasToken()) {
            throw new UnauthorizedHttpException('kolay-auth', 'Token not provided');
        }
    }

    /**
     * Set the authentication header.
     *
     * @param  \Illuminate\Http\Response|\Illuminate\Http\JsonResponse  $response
     * @param  string|null  $token
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    protected function setAuthenticationHeader($response, $token = null)
    {
        $token = $token ?: $this->auth->refresh();
        $response->headers->set('Authorization', 'Bearer ' . $token);
        return $response;
    }
}

<?php

namespace KolayIK\Auth\Test\Http;

use Mockery;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Crypt;
use KolayIK\Auth\Http\Parser\Parser;
use KolayIK\Auth\Http\Parser\Cookies;
use KolayIK\Auth\Http\Parser\AuthHeaders;
use KolayIK\Auth\Http\Parser\InputSource;
use KolayIK\Auth\Http\Parser\QueryString;
use KolayIK\Auth\Http\Parser\RouteParams;

use KolayIK\Auth\Test\AbstractTestCase;

class ParserTest extends AbstractTestCase
{
    /** @test */
    public function it_should_return_the_token_from_the_authorization_header()
    {
        $request = Request::create('foo', 'POST');
        $request->headers->set('Authorization', 'Bearer foobar');

        $parser = new Parser($request);

        $parser->setChain([
            new QueryString,
            new InputSource,
            new AuthHeaders,
            new RouteParams,
        ]);

        $this->assertSame($parser->parseToken(), 'foobar');
        $this->assertTrue($parser->hasToken());
    }

    /** @test */
    public function it_should_return_the_token_from_the_prefixed_authentication_header()
    {
        $request = Request::create('foo', 'POST');
        $request->headers->set('Authorization', 'Custom foobar');

        $parser = new Parser($request);

        $parser->setChain([
            new QueryString,
            new InputSource,
            (new AuthHeaders)->setHeaderPrefix('Custom'),
            new RouteParams,
        ]);

        $this->assertSame($parser->parseToken(), 'foobar');
        $this->assertTrue($parser->hasToken());
    }

    /** @test */
    public function it_should_return_the_token_from_the_custom_authentication_header()
    {
        $request = Request::create('foo', 'POST');
        $request->headers->set('custom_authorization', 'Bearer foobar');

        $parser = new Parser($request);

        $parser->setChain([
            new QueryString,
            new InputSource,
            (new AuthHeaders)->setHeaderName('custom_authorization'),
            new RouteParams,
        ]);

        $this->assertSame($parser->parseToken(), 'foobar');
        $this->assertTrue($parser->hasToken());
    }

    /** @test */
    public function it_should_return_the_token_from_the_alt_authorization_headers()
    {
        $request1 = Request::create('foo', 'POST');
        $request1->server->set('HTTP_AUTHORIZATION', 'Bearer foobar');

        $request2 = Request::create('foo', 'POST');
        $request2->server->set('REDIRECT_HTTP_AUTHORIZATION', 'Bearer foobarbaz');

        $parser = new Parser($request1, [
            new AuthHeaders,
            new QueryString,
            new InputSource,
            new RouteParams,
        ]);

        $this->assertSame($parser->parseToken(), 'foobar');
        $this->assertTrue($parser->hasToken());

        $parser->setRequest($request2);
        $this->assertSame($parser->parseToken(), 'foobarbaz');
        $this->assertTrue($parser->hasToken());
    }

    /** @test */
    public function it_should_return_the_token_from_query_string()
    {
        $request = Request::create('foo', 'GET', ['authorization_key' => 'foobar']);

        $parser = new Parser($request);
        $parser->setChain([
            new AuthHeaders,
            new QueryString,
            new InputSource,
            new RouteParams,
        ]);

        $this->assertSame($parser->parseToken(), 'foobar');
        $this->assertTrue($parser->hasToken());
    }

    /** @test */
    public function it_should_return_the_token_from_the_custom_query_string()
    {
        $request = Request::create('foo', 'GET', ['custom_token_key' => 'foobar']);

        $parser = new Parser($request);
        $parser->setChain([
            new AuthHeaders,
            (new QueryString)->setKey('custom_token_key'),
            new InputSource,
            new RouteParams,
        ]);

        $this->assertSame($parser->parseToken(), 'foobar');
        $this->assertTrue($parser->hasToken());
    }

    /** @test */
    public function it_should_return_the_token_from_the_query_string_not_the_input_source()
    {
        $request = Request::create('foo?authorization_key=foobar', 'POST', [], [], [], [], json_encode(['authorization_key' => 'foobarbaz']));

        $parser = new Parser($request);
        $parser->setChain([
            new AuthHeaders,
            new QueryString,
            new InputSource,
            new RouteParams,
        ]);

        $this->assertSame($parser->parseToken(), 'foobar');
        $this->assertTrue($parser->hasToken());
    }

    /** @test */
    public function it_should_return_the_token_from_the_custom_query_string_not_the_custom_input_source()
    {
        $request = Request::create('foo?custom_token_key=foobar', 'POST', [], [], [], [], json_encode(['custom_token_key' => 'foobarbaz']));

        $parser = new Parser($request);
        $parser->setChain([
            new AuthHeaders,
            (new QueryString)->setKey('custom_token_key'),
            (new InputSource)->setKey('custom_token_key'),
            new RouteParams,
        ]);

        $this->assertSame($parser->parseToken(), 'foobar');
        $this->assertTrue($parser->hasToken());
    }

    /** @test */
    public function it_should_return_the_token_from_input_source()
    {
        $request = Request::create('foo', 'POST', [], [], [], [], json_encode(['authorization_key' => 'foobar']));
        $request->headers->set('Content-Type', 'application/json');

        $parser = new Parser($request);
        $parser->setChain([
            new AuthHeaders,
            new QueryString,
            new InputSource,
            new RouteParams,
        ]);

        $this->assertSame($parser->parseToken(), 'foobar');
        $this->assertTrue($parser->hasToken());
    }

    /** @test */
    public function it_should_return_the_token_from_the_custom_input_source()
    {
        $request = Request::create('foo', 'POST', [], [], [], [], json_encode(['custom_token_key' => 'foobar']));
        $request->headers->set('Content-Type', 'application/json');

        $parser = new Parser($request);
        $parser->setChain([
            new AuthHeaders,
            new QueryString,
            (new InputSource)->setKey('custom_token_key'),
            new RouteParams,
        ]);

        $this->assertSame($parser->parseToken(), 'foobar');
        $this->assertTrue($parser->hasToken());
    }

    /** @test */
    public function it_should_return_the_token_from_an_unencrypted_cookie()
    {
        $request = Request::create('foo', 'POST', [], ['authorization_key' => 'foobar']);

        $parser = new Parser($request);
        $parser->setChain([
            new AuthHeaders,
            new QueryString,
            new InputSource,
            new RouteParams,
            new Cookies(false),
        ]);

        $this->assertSame($parser->parseToken(), 'foobar');
        $this->assertTrue($parser->hasToken());
    }

    /** @test */
    public function it_should_return_the_token_from_a_crypted_cookie()
    {
        Crypt::shouldReceive('encrypt')
            ->with('foobar')
            ->once()
            ->andReturn('cryptedFoobar');

        $request = Request::create('foo', 'POST', [], ['authorization_key' => Crypt::encrypt('foobar')]);

        $parser = new Parser($request);
        $parser->setChain([
            new AuthHeaders,
            new QueryString,
            new InputSource,
            new RouteParams,
            new Cookies(true),
        ]);

        Crypt::shouldReceive('decrypt')
            ->with('cryptedFoobar')
            ->times(2)
            ->andReturn('foobar');

        $this->assertSame($parser->parseToken(), 'foobar');
        $this->assertTrue($parser->hasToken());
    }

    /** @test */
    public function it_should_return_the_token_from_route()
    {
        $request = Request::create('foo', 'GET', ['foo' => 'bar']);
        $request->setRouteResolver(function () {
            return $this->getRouteMock('foobar');
        });

        $parser = new Parser($request);
        $parser->setChain([
            new AuthHeaders,
            new QueryString,
            new InputSource,
            new RouteParams,
        ]);

        $this->assertSame($parser->parseToken(), 'foobar');
        $this->assertTrue($parser->hasToken());
    }

    /** @test */
    public function it_should_return_the_token_from_route_with_a_custom_param()
    {
        $request = Request::create('foo', 'GET', ['foo' => 'bar']);
        $request->setRouteResolver(function () {
            return $this->getRouteMock('foobar', 'custom_route_param');
        });

        $parser = new Parser($request);
        $parser->setChain([
            new AuthHeaders,
            new QueryString,
            new InputSource,
            (new RouteParams)->setKey('custom_route_param'),
        ]);

        $this->assertSame($parser->parseToken(), 'foobar');
        $this->assertTrue($parser->hasToken());
    }

    /** @test */
    public function it_should_ignore_routeless_requests()
    {
        $request = Request::create('foo', 'GET', ['foo' => 'bar']);
        $request->setRouteResolver(function () {
            //
        });

        $parser = new Parser($request);
        $parser->setChain([
            new AuthHeaders,
            new QueryString,
            new InputSource,
            new RouteParams,
        ]);

        $this->assertNull($parser->parseToken());
        $this->assertFalse($parser->hasToken());
    }

    /** @test */
    public function it_should_return_null_if_no_token_in_request()
    {
        $request = Request::create('foo', 'GET', ['foo' => 'bar']);
        $request->setRouteResolver(function () {
            return $this->getRouteMock();
        });

        $parser = new Parser($request);
        $parser->setChain([
            new AuthHeaders,
            new QueryString,
            new InputSource,
            new RouteParams,
        ]);

        $this->assertNull($parser->parseToken());
        $this->assertFalse($parser->hasToken());
    }

    /** @test */
    public function it_should_retrieve_the_chain()
    {
        $chain = [
            new AuthHeaders,
            new QueryString,
            new InputSource,
            new RouteParams,
        ];

        $parser = new Parser(Mockery::mock(Request::class));
        $parser->setChain($chain);

        $this->assertSame($parser->getChain(), $chain);
    }

    /** @test */
    public function it_should_retrieve_the_chain_with_alias()
    {
        $chain = [
            new AuthHeaders,
            new QueryString,
            new InputSource,
            new RouteParams,
        ];

        /* @var \Illuminate\Http\Request $request */
        $request = Mockery::mock(Request::class);

        $parser = new Parser($request);
        $parser->setChainOrder($chain);

        $this->assertSame($parser->getChain(), $chain);
    }

    /** @test */
    public function it_should_set_the_cookie_key()
    {
        $cookies = (new Cookies)->setKey('test');
        $this->assertInstanceOf(Cookies::class, $cookies);
    }

    protected function getRouteMock($expectedParameterValue = null, $expectedParameterName = 'authorization_key')
    {
        return Mockery::mock(Route::class)
            ->shouldReceive('parameter')
            ->with($expectedParameterName)
            ->andReturn($expectedParameterValue)
            ->getMock();
    }
}

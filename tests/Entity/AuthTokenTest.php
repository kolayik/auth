<?php

namespace KolayIK\Auth\Test\Entity;

use Carbon\Carbon;
use KolayIK\Auth\Entity\AuthToken;

use KolayIK\Auth\Test\AbstractTestCase;

class AuthTokenTest extends AbstractTestCase
{
    /** @test */
    public function it_should_return_false_from_is_expired_check()
    {
        $now = Carbon::now();

        $authToken = new AuthToken();
        $authToken->setUserId('foo');
        $authToken->setToken('fooToken');
        $authToken->setUpdatedAt($now);
        $authToken->setCreatedAt($now);
        $authToken->setExpirationDate($now->addDays(2));

        $this->assertInstanceOf(Carbon::class, $authToken->getExpirationDate());
        $this->assertFalse($authToken->isExpired());
    }

    /** @test */
    public function it_should_return_true_from_is_expired_check()
    {
        $now = Carbon::now();

        $authToken = new AuthToken();
        $authToken->setUserId('foo');
        $authToken->setToken('fooToken');
        $authToken->setUpdatedAt($now);
        $authToken->setCreatedAt($now);
        $authToken->setExpirationDate($now->subDays(2));

        $this->assertInstanceOf(Carbon::class, $authToken->getExpirationDate());
        $this->assertTrue($authToken->isExpired());
    }
}

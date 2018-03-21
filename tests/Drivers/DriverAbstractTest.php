<?php

namespace KolayIK\Auth\Test\Drivers;

use KolayIK\Auth\Drivers\DriverAbstract;
use KolayIK\Auth\Test\AbstractTestCase;

use Illuminate\Contracts\Cache\Repository as CacheContract;
use Mockery;

class DriverAbstractTest extends AbstractTestCase
{
    /** @test */
    public function it_should_be_return_true()
    {
        $cacheContract = Mockery::mock(CacheContract::class);

        $driverAbstract = new DriverAbstract();
        $driverAbstract->setCache($cacheContract);
        $driverAbstract->setConfig([
            'ttl' => 2000
        ]);

        $this->assertInstanceOf(CacheContract::class, $driverAbstract->getCache());
        $this->assertArrayHasKey('ttl', $driverAbstract->getConfig());
    }
}

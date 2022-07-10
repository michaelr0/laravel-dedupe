<?php

namespace Michaelr0\LaravelDedupe\Tests;

use Michaelr0\LaravelDedupe\LaravelDedupeServiceProvider;

class ExampleTest extends TestCase
{
    protected function getPackageProviders($app)
    {
        return [LaravelDedupeServiceProvider::class];
    }

    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}

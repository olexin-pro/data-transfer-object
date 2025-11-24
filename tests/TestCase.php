<?php

declare(strict_types=1);

namespace DataTransferObject\Tests;

use Illuminate\Support\ServiceProvider;
use Ol3x1n\DataTransferObject\DTOProvider;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

class TestCase extends OrchestraTestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string<ServiceProvider>>
     */
    protected function getPackageProviders($app): array
    {
        return [
            DTOProvider::class
        ];
    }
    /**
     * Automatically enables package discoveries.
     *
     * @var bool
     */
    protected $enablesPackageDiscoveries = true;
}
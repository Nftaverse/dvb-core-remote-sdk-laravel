<?php

namespace DVB\Core\SDK\Tests;

use Dotenv\Dotenv;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            \DVB\Core\SDK\DvbServiceProvider::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
        
        // Load test environment variables using Dotenv
        $envFile = __DIR__ . '/../.env.testing';
        if (file_exists($envFile)) {
            $dotenv = Dotenv::createUnsafeImmutable(dirname($envFile), basename($envFile));
            $dotenv->load();
        }
    }
}
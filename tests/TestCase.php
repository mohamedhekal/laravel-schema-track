<?php

namespace MohamedHekal\LaravelSchemaTrack\Tests;

use MohamedHekal\LaravelSchemaTrack\SchemaTrackServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            SchemaTrackServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        // Setup schema-track config
        $app['config']->set('schema-track.storage_path', storage_path('schema-track-test'));
        $app['config']->set('schema-track.auto_snapshot', false);
    }
}

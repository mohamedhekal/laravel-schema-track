<?php

namespace MohamedHekal\LaravelSchemaTrack;

use Illuminate\Support\ServiceProvider;
use MohamedHekal\LaravelSchemaTrack\Commands\SchemaTrackCommand;
use MohamedHekal\LaravelSchemaTrack\Commands\SchemaDiffCommand;
use MohamedHekal\LaravelSchemaTrack\Commands\SchemaCompareCommand;
use MohamedHekal\LaravelSchemaTrack\Commands\SchemaChangelogCommand;
use MohamedHekal\LaravelSchemaTrack\Commands\SchemaListCommand;
use MohamedHekal\LaravelSchemaTrack\Services\SchemaSnapshotService;
use MohamedHekal\LaravelSchemaTrack\Services\SchemaDiffService;
use MohamedHekal\LaravelSchemaTrack\Services\SchemaChangelogService;
use MohamedHekal\LaravelSchemaTrack\Contracts\SchemaSnapshotInterface;
use MohamedHekal\LaravelSchemaTrack\Contracts\SchemaDiffInterface;
use MohamedHekal\LaravelSchemaTrack\Contracts\SchemaChangelogInterface;

class SchemaTrackServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/schema-track.php', 'schema-track');

        $this->app->singleton(SchemaSnapshotInterface::class, SchemaSnapshotService::class);
        $this->app->singleton(SchemaDiffInterface::class, SchemaDiffService::class);
        $this->app->singleton(SchemaChangelogInterface::class, SchemaChangelogService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/schema-track.php' => config_path('schema-track.php'),
            ], 'schema-track-config');

            $this->commands([
                SchemaTrackCommand::class,
                SchemaDiffCommand::class,
                SchemaCompareCommand::class,
                SchemaChangelogCommand::class,
                SchemaListCommand::class,
            ]);
        }

        // Auto-snapshot after migrations
        if (config('schema-track.auto_snapshot', true)) {
            $this->registerMigrationListener();
        }
    }

    /**
     * Register migration listener for auto-snapshot
     */
    protected function registerMigrationListener(): void
    {
        $this->app['events']->listen('Illuminate\Database\Events\MigrationsEnded', function ($event) {
            $snapshotService = $this->app->make(SchemaSnapshotInterface::class);
            $snapshotService->takeSnapshot('auto_migration_' . date('Y_m_d_His'));
        });
    }
}

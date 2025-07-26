<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Schema Track Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the Laravel SchemaTrack package.
    | You can customize these settings according to your needs.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Storage Path
    |--------------------------------------------------------------------------
    |
    | The path where schema snapshots will be stored. This should be a
    | writable directory within your Laravel application.
    |
    */
    'storage_path' => storage_path('schema-track'),

    /*
    |--------------------------------------------------------------------------
    | Auto Snapshot
    |--------------------------------------------------------------------------
    |
    | Whether to automatically take a snapshot after each migration.
    | Set to false to disable automatic snapshots.
    |
    */
    'auto_snapshot' => env('SCHEMA_TRACK_AUTO_SNAPSHOT', true),

    /*
    |--------------------------------------------------------------------------
    | Snapshot Prefix
    |--------------------------------------------------------------------------
    |
    | The prefix to use for snapshot filenames. This helps organize
    | snapshots and avoid naming conflicts.
    |
    */
    'snapshot_prefix' => env('SCHEMA_TRACK_PREFIX', 'schema_snapshot'),

    /*
    |--------------------------------------------------------------------------
    | Supported Databases
    |--------------------------------------------------------------------------
    |
    | List of database drivers that are supported by this package.
    | Currently supports: mysql, pgsql, sqlite
    |
    */
    'supported_databases' => [
        'mysql',
        'pgsql',
        'sqlite',
    ],

    /*
    |--------------------------------------------------------------------------
    | Exclude Tables
    |--------------------------------------------------------------------------
    |
    | Tables to exclude from schema snapshots. These tables are typically
    | Laravel system tables that don't need to be tracked.
    |
    */
    'exclude_tables' => [
        'migrations',
        'failed_jobs',
        'password_reset_tokens',
        'personal_access_tokens',
        'sessions',
        'cache',
        'cache_locks',
    ],

    /*
    |--------------------------------------------------------------------------
    | Changelog Formats
    |--------------------------------------------------------------------------
    |
    | Supported output formats for changelog generation.
    |
    */
    'changelog_formats' => [
        'markdown',
        'json',
        'text',
    ],

    /*
    |--------------------------------------------------------------------------
    | Environment Comparison
    |--------------------------------------------------------------------------
    |
    | Configuration for comparing schemas across different environments.
    | This requires additional setup for database connections.
    |
    */
    'environments' => [
        'staging' => [
            'connection' => 'staging',
            'enabled' => env('SCHEMA_TRACK_STAGING_ENABLED', false),
        ],
        'production' => [
            'connection' => 'production',
            'enabled' => env('SCHEMA_TRACK_PRODUCTION_ENABLED', false),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Breaking Change Detection
    |--------------------------------------------------------------------------
    |
    | Configuration for detecting breaking changes in schema modifications.
    |
    */
    'breaking_changes' => [
        'enabled' => true,
        'warn_on' => [
            'column_removal',
            'table_removal',
            'type_changes',
            'nullable_to_not_null',
            'unique_constraint_removal',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Notification Settings
    |--------------------------------------------------------------------------
    |
    | Configure notifications for schema changes (future feature).
    |
    */
    'notifications' => [
        'enabled' => env('SCHEMA_TRACK_NOTIFICATIONS_ENABLED', false),
        'channels' => [
            'slack' => env('SCHEMA_TRACK_SLACK_WEBHOOK'),
            'discord' => env('SCHEMA_TRACK_DISCORD_WEBHOOK'),
            'email' => env('SCHEMA_TRACK_EMAIL_RECIPIENTS'),
        ],
        'events' => [
            'breaking_changes',
            'new_tables',
            'column_removals',
        ],
    ],
];

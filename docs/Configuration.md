# Configuration Guide

Complete configuration reference for Laravel SchemaTrack.

## 📋 Configuration File

The configuration file is located at `config/schema-track.php` and contains all customizable options.

### Publishing Configuration

```bash
php artisan vendor:publish --provider="MohamedHekal\LaravelSchemaTrack\SchemaTrackServiceProvider"
```

## ⚙️ Configuration Options

### Basic Settings

```php
<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Storage Path
    |--------------------------------------------------------------------------
    |
    | The path where schema snapshots will be stored.
    |
    */
    'storage_path' => env('SCHEMA_TRACK_STORAGE_PATH', 'storage/schema-track'),

    /*
    |--------------------------------------------------------------------------
    | Auto Snapshot
    |--------------------------------------------------------------------------
    |
    | Automatically take a snapshot after each migration.
    |
    */
    'auto_snapshot' => env('SCHEMA_TRACK_AUTO_SNAPSHOT', true),

    /*
    |--------------------------------------------------------------------------
    | Snapshot Naming
    |--------------------------------------------------------------------------
    |
    | Naming convention for auto-generated snapshots.
    |
    */
    'snapshot_naming' => [
        'format' => 'Y_m_d_His',  // PHP date format
        'prefix' => '',           // Optional prefix
        'suffix' => '',           // Optional suffix
    ],
];
```

### Database Settings

```php
    /*
    |--------------------------------------------------------------------------
    | Excluded Tables
    |--------------------------------------------------------------------------
    |
    | Tables to exclude from snapshots (e.g., system tables).
    |
    */
    'excluded_tables' => [
        'migrations',
        'failed_jobs',
        'password_reset_tokens',
        'personal_access_tokens',
        'sessions',
        'oauth_access_tokens',
        'oauth_refresh_tokens',
        'cache',
        'cache_locks',
    ],

    /*
    |--------------------------------------------------------------------------
    | Excluded Columns
    |--------------------------------------------------------------------------
    |
    | Columns to exclude from snapshots (e.g., sensitive data).
    |
    */
    'excluded_columns' => [
        'password',
        'remember_token',
        'api_token',
        'secret_key',
    ],

    /*
    |--------------------------------------------------------------------------
    | Supported Databases
    |--------------------------------------------------------------------------
    |
    | Database drivers that are supported by SchemaTrack.
    |
    */
    'supported_databases' => [
        'mysql',
        'pgsql',
        'sqlite',
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Connection
    |--------------------------------------------------------------------------
    |
    | Default database connection to use for snapshots.
    |
    */
    'database_connection' => env('SCHEMA_TRACK_DB_CONNECTION', 'default'),
```

### Changelog Settings

```php
    /*
    |--------------------------------------------------------------------------
    | Changelog Formats
    |--------------------------------------------------------------------------
    |
    | Available formats for changelog generation.
    |
    */
    'changelog_formats' => [
        'markdown',
        'json',
        'text',
        'html',
    ],

    /*
    |--------------------------------------------------------------------------
    | Changelog Templates
    |--------------------------------------------------------------------------
    |
    | Custom templates for changelog generation.
    |
    */
    'changelog_templates' => [
        'markdown' => [
            'header' => '# Schema Changelog',
            'section_header' => '## {date}',
            'new_table' => '### New Tables',
            'modified_table' => '### Modified Tables',
            'removed_table' => '### Removed Tables',
        ],
        'text' => [
            'header' => 'Schema Changelog',
            'section_header' => '{date}',
            'new_table' => 'New Tables:',
            'modified_table' => 'Modified Tables:',
            'removed_table' => 'Removed Tables:',
        ],
    ],
```

### Environment Comparison

```php
    /*
    |--------------------------------------------------------------------------
    | Environment Comparison
    |--------------------------------------------------------------------------
    |
    | Settings for comparing schemas across environments.
    |
    */
    'environment_comparison' => [
        'enabled' => env('SCHEMA_TRACK_ENV_COMPARISON', true),
        'environments' => [
            'local',
            'staging',
            'production',
            'testing',
        ],
        'connection_mapping' => [
            'staging' => 'staging_db',
            'production' => 'production_db',
            'testing' => 'testing_db',
        ],
    ],
```

### Breaking Change Detection

```php
    /*
    |--------------------------------------------------------------------------
    | Breaking Change Detection
    |--------------------------------------------------------------------------
    |
    | Detect potentially breaking schema changes.
    |
    */
    'breaking_change_detection' => [
        'enabled' => env('SCHEMA_TRACK_BREAKING_CHANGES', true),
        'warn_on_drop_column' => true,
        'warn_on_drop_table' => true,
        'warn_on_modify_index' => true,
        'warn_on_modify_constraint' => true,
        'warn_on_change_column_type' => true,
        'warn_on_change_column_nullable' => true,
        'warn_on_change_column_default' => true,
        'warn_on_drop_foreign_key' => true,
    ],
```

### Performance Settings

```php
    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    |
    | Performance optimization settings.
    |
    */
    'performance' => [
        'memory_limit' => env('SCHEMA_TRACK_MEMORY_LIMIT', '256M'),
        'timeout' => env('SCHEMA_TRACK_TIMEOUT', 300),  // 5 minutes
        'batch_size' => env('SCHEMA_TRACK_BATCH_SIZE', 100),
        'max_tables_per_snapshot' => env('SCHEMA_TRACK_MAX_TABLES', 1000),
        'compress_snapshots' => env('SCHEMA_TRACK_COMPRESS', false),
    ],
```

### Notification Settings

```php
    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    |
    | Notification settings for schema changes.
    |
    */
    'notifications' => [
        'enabled' => env('SCHEMA_TRACK_NOTIFICATIONS', false),
        'channels' => [
            'mail',
            'slack',
            'discord',
            'webhook',
        ],
        'events' => [
            'snapshot_created',
            'breaking_changes_detected',
            'schema_drift_detected',
            'migration_completed',
        ],
        'recipients' => [
            'admin@example.com',
            'dev-team@example.com',
        ],
        'webhook_url' => env('SCHEMA_TRACK_WEBHOOK_URL'),
        'slack_webhook' => env('SCHEMA_TRACK_SLACK_WEBHOOK'),
        'discord_webhook' => env('SCHEMA_TRACK_DISCORD_WEBHOOK'),
    ],
```

### Security Settings

```php
    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    |
    | Security and access control settings.
    |
    */
    'security' => [
        'encrypt_snapshots' => env('SCHEMA_TRACK_ENCRYPT', false),
        'encryption_key' => env('SCHEMA_TRACK_ENCRYPTION_KEY'),
        'allowed_ips' => [
            '127.0.0.1',
            '::1',
        ],
        'require_authentication' => env('SCHEMA_TRACK_AUTH', false),
        'max_snapshots_per_day' => env('SCHEMA_TRACK_MAX_SNAPSHOTS', 100),
    ],
```

## 🌍 Environment Variables

You can configure SchemaTrack using environment variables in your `.env` file:

```env
# SchemaTrack Configuration
SCHEMA_TRACK_STORAGE_PATH=storage/schema-track
SCHEMA_TRACK_AUTO_SNAPSHOT=true
SCHEMA_TRACK_BREAKING_CHANGES=true
SCHEMA_TRACK_ENV_COMPARISON=true
SCHEMA_TRACK_NOTIFICATIONS=false

# Performance
SCHEMA_TRACK_MEMORY_LIMIT=512M
SCHEMA_TRACK_TIMEOUT=600
SCHEMA_TRACK_BATCH_SIZE=200
SCHEMA_TRACK_MAX_TABLES=2000
SCHEMA_TRACK_COMPRESS=true

# Security
SCHEMA_TRACK_ENCRYPT=false
SCHEMA_TRACK_AUTH=false
SCHEMA_TRACK_MAX_SNAPSHOTS=50

# Notifications
SCHEMA_TRACK_WEBHOOK_URL=https://hooks.slack.com/services/YOUR/WEBHOOK/URL
SCHEMA_TRACK_SLACK_WEBHOOK=https://hooks.slack.com/services/YOUR/SLACK/WEBHOOK
SCHEMA_TRACK_DISCORD_WEBHOOK=https://discord.com/api/webhooks/YOUR/DISCORD/WEBHOOK

# Database
SCHEMA_TRACK_DB_CONNECTION=default
```

## 🔧 Advanced Configuration

### Custom Snapshot Naming

```php
'snapshot_naming' => [
    'format' => 'Y_m_d_His',
    'prefix' => 'schema_',
    'suffix' => '_snapshot',
    'custom_generator' => null,  // Custom closure for naming
],
```

### Custom Excluded Tables Logic

```php
'excluded_tables' => [
    'migrations',
    'failed_jobs',
    'password_reset_tokens',
    'personal_access_tokens',
    'sessions',
],

// Custom exclusion logic
'exclude_tables_callback' => function ($tableName) {
    return str_starts_with($tableName, 'temp_') || 
           str_ends_with($tableName, '_backup');
},
```

### Custom Changelog Template

```php
'changelog_templates' => [
    'markdown' => [
        'header' => '# Database Schema Changes',
        'section_header' => '## Changes on {date}',
        'new_table' => '### 🆕 New Tables',
        'modified_table' => '### 📝 Modified Tables',
        'removed_table' => '### ❌ Removed Tables',
        'breaking_changes' => '### ⚠️ Breaking Changes',
        'summary' => '### 📊 Summary',
    ],
],
```

### Performance Optimization

```php
'performance' => [
    'memory_limit' => '1G',
    'timeout' => 600,  // 10 minutes
    'batch_size' => 50,
    'max_tables_per_snapshot' => 500,
    'compress_snapshots' => true,
    'use_cache' => true,
    'cache_ttl' => 3600,  // 1 hour
],
```

## 🎯 Configuration Examples

### Development Environment

```php
// config/schema-track.php (Development)
return [
    'storage_path' => 'storage/schema-track',
    'auto_snapshot' => true,
    'excluded_tables' => [
        'migrations',
        'failed_jobs',
        'sessions',
    ],
    'breaking_change_detection' => [
        'enabled' => true,
        'warn_on_drop_column' => true,
        'warn_on_drop_table' => true,
    ],
    'performance' => [
        'memory_limit' => '256M',
        'timeout' => 300,
        'batch_size' => 100,
    ],
];
```

### Production Environment

```php
// config/schema-track.php (Production)
return [
    'storage_path' => '/var/www/schema-track',
    'auto_snapshot' => false,  // Manual snapshots only
    'excluded_tables' => [
        'migrations',
        'failed_jobs',
        'sessions',
        'audit_logs',
        'user_activity',
    ],
    'breaking_change_detection' => [
        'enabled' => true,
        'warn_on_drop_column' => true,
        'warn_on_drop_table' => true,
        'warn_on_modify_index' => true,
    ],
    'notifications' => [
        'enabled' => true,
        'channels' => ['slack', 'mail'],
        'events' => ['breaking_changes_detected'],
    ],
    'security' => [
        'encrypt_snapshots' => true,
        'require_authentication' => true,
        'max_snapshots_per_day' => 10,
    ],
    'performance' => [
        'memory_limit' => '512M',
        'timeout' => 600,
        'batch_size' => 50,
        'compress_snapshots' => true,
    ],
];
```

### Testing Environment

```php
// config/schema-track.php (Testing)
return [
    'storage_path' => 'storage/schema-track-test',
    'auto_snapshot' => true,
    'excluded_tables' => [
        'migrations',
        'failed_jobs',
    ],
    'breaking_change_detection' => [
        'enabled' => false,  // Disable for testing
    ],
    'performance' => [
        'memory_limit' => '128M',
        'timeout' => 60,
        'batch_size' => 10,
    ],
];
```

## 🔄 Configuration Caching

After making configuration changes, clear the cache:

```bash
# Clear configuration cache
php artisan config:clear

# Cache configuration for production
php artisan config:cache
```

## 📊 Configuration Validation

Validate your configuration:

```bash
# Check configuration
php artisan config:show schema-track

# Validate configuration
php artisan schema:track --validate-config
```

## 📚 Related Documentation

- [Installation Guide](Installation.md) - Setup instructions
- [Quick Start Guide](Quick-Start.md) - Get started quickly
- [Best Practices](Best-Practices.md) - Configuration best practices
- [Troubleshooting](Troubleshooting.md) - Common configuration issues 
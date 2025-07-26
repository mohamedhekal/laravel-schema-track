# Installation Guide

This guide will walk you through installing and setting up Laravel SchemaTrack in your Laravel application.

## 📋 Prerequisites

Before installing Laravel SchemaTrack, ensure you have:

- **Laravel 10.x or 11.x**
- **PHP 8.1 or higher**
- **Composer**
- **Database connection** (MySQL, PostgreSQL, or SQLite)

## 🚀 Installation

### Step 1: Install via Composer

```bash
composer require mohamedhekal/laravel-schema-track
```

### Step 2: Publish Configuration (Optional)

Publish the configuration file to customize SchemaTrack settings:

```bash
php artisan vendor:publish --provider="MohamedHekal\LaravelSchemaTrack\SchemaTrackServiceProvider"
```

This will create `config/schema-track.php` in your application.

### Step 3: Create Storage Directory

Ensure the storage directory exists:

```bash
mkdir -p storage/schema-track
```

Or the package will create it automatically on first use.

## 🔧 Configuration

### Basic Configuration

The configuration file `config/schema-track.php` contains all customizable options:

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
    'storage_path' => 'storage/schema-track',

    /*
    |--------------------------------------------------------------------------
    | Auto Snapshot
    |--------------------------------------------------------------------------
    |
    | Automatically take a snapshot after each migration.
    |
    */
    'auto_snapshot' => true,

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
    ],

    /*
    |--------------------------------------------------------------------------
    | Environment Comparison
    |--------------------------------------------------------------------------
    |
    | Settings for comparing schemas across environments.
    |
    */
    'environment_comparison' => [
        'enabled' => true,
        'environments' => [
            'local',
            'staging',
            'production',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Breaking Change Detection
    |--------------------------------------------------------------------------
    |
    | Detect potentially breaking schema changes.
    |
    */
    'breaking_change_detection' => [
        'enabled' => true,
        'warn_on_drop_column' => true,
        'warn_on_drop_table' => true,
        'warn_on_modify_index' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Notifications
    |--------------------------------------------------------------------------
    |
    | Notification settings for schema changes.
    |
    */
    'notifications' => [
        'enabled' => false,
        'channels' => [
            'mail',
            'slack',
        ],
        'events' => [
            'snapshot_created',
            'breaking_changes_detected',
        ],
    ],
];
```

### Environment Variables

You can also configure SchemaTrack using environment variables:

```env
# SchemaTrack Configuration
SCHEMA_TRACK_AUTO_SNAPSHOT=true
SCHEMA_TRACK_STORAGE_PATH=storage/schema-track
SCHEMA_TRACK_BREAKING_CHANGE_DETECTION=true
```

## 🔍 Verification

### Step 1: Check Installation

Verify that the package is properly installed:

```bash
php artisan list | grep schema
```

You should see the following commands:
- `schema:track`
- `schema:list`
- `schema:diff`
- `schema:changelog`
- `schema:compare`

### Step 2: Test Basic Functionality

Take your first schema snapshot:

```bash
php artisan schema:track
```

List available snapshots:

```bash
php artisan schema:list
```

## 🗂️ Directory Structure

After installation, your project will have:

```
your-laravel-app/
├── config/
│   └── schema-track.php          # Configuration file
├── storage/
│   └── schema-track/             # Snapshot storage
│       ├── 2024_01_01_120000_initial.json
│       ├── 2024_01_02_140000_add_users_table.json
│       └── ...
└── vendor/
    └── mohamedhekal/
        └── laravel-schema-track/  # Package files
```

## 🔒 Security Considerations

### File Permissions

Ensure proper file permissions for the storage directory:

```bash
chmod 755 storage/schema-track
```

### Backup Strategy

Consider backing up your schema snapshots:

```bash
# Add to your backup script
cp -r storage/schema-track /backup/schema-track-$(date +%Y%m%d)
```

## 🚨 Troubleshooting

### Common Issues

#### Issue: "Command not found"
**Solution:** Ensure the service provider is registered in `config/app.php`:

```php
'providers' => [
    // ...
    MohamedHekal\LaravelSchemaTrack\SchemaTrackServiceProvider::class,
],
```

#### Issue: "Storage directory not writable"
**Solution:** Check permissions and create directory:

```bash
mkdir -p storage/schema-track
chmod 755 storage/schema-track
```

#### Issue: "Database connection failed"
**Solution:** Verify your database configuration in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

## 📚 Next Steps

- [Quick Start Guide](Quick-Start.md) - Get up and running quickly
- [Configuration](Configuration.md) - Advanced configuration options
- [Artisan Commands](Artisan-Commands.md) - Learn about available commands 
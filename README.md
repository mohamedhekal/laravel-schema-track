# 📦 Laravel SchemaTrack

**Smart Version-Controlled Schema History & Change Logger for Laravel Projects**

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mohamedhekal/laravel-schema-track.svg)](https://packagist.org/packages/mohamedhekal/laravel-schema-track)
[![Tests](https://github.com/mohamedhekal/laravel-schema-track/workflows/Tests/badge.svg)](https://github.com/mohamedhekal/laravel-schema-track/actions)
[![Total Downloads](https://img.shields.io/packagist/dt/mohamedhekal/laravel-schema-track.svg)](https://packagist.org/packages/mohamedhekal/laravel-schema-track)

---

## 🚀 Overview

**Laravel SchemaTrack** is a developer-focused package that brings **schema versioning**, **change tracking**, and **visual diffs** to Laravel's database structure — turning your migrations and database changes into a clear, documented, and reversible timeline.

Think of it as **Git for your database schema**, fully integrated into Laravel.

---

## 💡 Key Features

* 🧠 **Auto Snapshot**: Automatically takes a snapshot of your entire database schema after each `migrate` operation
* 🧾 **Schema Diff Viewer**: Shows visual differences between schema versions
* 📚 **Changelog Generator**: Generates Markdown or JSON changelog with human-readable schema changes
* 🌐 **Multi-Environment Compare**: Compare schema state between environments
* 📦 **Custom Artisan Commands**: Complete CLI interface for schema management
* 🛠️ **Schema Explorer Dashboard**: Web UI for browsing schema versions (Optional)
* 🔐 **Safe by Design**: Never alters your schema, only reads metadata

---

## 📦 Installation

```bash
composer require mohamedhekal/laravel-schema-track
```

Publish the configuration file:

```bash
php artisan vendor:publish --provider="MohamedHekal\LaravelSchemaTrack\SchemaTrackServiceProvider"
```

---

## 🧰 Usage

### Basic Commands

```bash
# Take a manual snapshot
php artisan schema:track

# View differences between snapshots
php artisan schema:diff --from=2024_07_01 --to=latest

# Compare with another environment
php artisan schema:compare --env=staging

# Generate changelog
php artisan schema:changelog --format=markdown --output=CHANGELOG.md

# List all snapshots
php artisan schema:list
```

### Auto-Snapshot on Migrations

The package automatically takes snapshots after each migration. You can disable this in the config:

```php
// config/schema-track.php
'auto_snapshot' => true,
```

---

## 🧪 Use Case Examples

| Scenario | Value |
|----------|-------|
| 🏢 Large teams | Ensure developers don't override each other's migration logic |
| 🧪 QA testing | Communicate DB changes clearly across versions |
| 🕵️ Debugging | Quickly inspect when a schema column was added/changed |
| 🚀 CI/CD | Detect unintentional or risky schema changes before deployment |

---

## 📁 Directory Structure

```
storage/schema-track/
├── 2024_07_01_123000_initial_snapshot.json
├── 2024_07_10_093000_add_users_table.json
├── ...
```

---

## 📜 Sample Output

### Changelog (Markdown)

```markdown
## 🗂 Schema Changes (2024-07-26)

### Modified Table: `users`
- Added Column: `is_verified` → boolean, default: false
- Changed Column: `email` → now unique

### New Table: `user_profiles`
- `id`, `user_id`, `bio`, `avatar`, timestamps
```

### Diff Output

```bash
$ php artisan schema:diff --from=2024_07_01 --to=latest

📊 Schema Diff Report
====================

🆕 New Tables:
  - user_profiles

📝 Modified Tables:
  - users:
    + Added: is_verified (boolean, default: false)
    + Changed: email (now unique)
```

---

## ⚙️ Configuration

```php
// config/schema-track.php

return [
    'storage_path' => storage_path('schema-track'),
    'auto_snapshot' => true,
    'snapshot_prefix' => 'schema_snapshot',
    'supported_databases' => ['mysql', 'pgsql', 'sqlite'],
    'exclude_tables' => ['migrations', 'failed_jobs'],
    'changelog_formats' => ['markdown', 'json'],
];
```

---

## 🧠 How It's Better Than Migrations Alone

| Laravel Migrations | SchemaTrack |
|-------------------|-------------|
| Code-first schema | Snapshot of actual DB |
| Doesn't track change history | Tracks every change |
| Manual diffing | Auto-diff between any two states |
| Can become out-of-sync | Always reflects real DB state |

---

## 🔮 Roadmap

* ✅ Auto-generate test cases for schema constraints
* ✅ Slack/Discord notifications on critical changes
* ✅ Integration with Laravel Nova or Filament dashboard
* ✅ Detect risky changes (dropping indexed columns, foreign keys)

---

## 🧪 Testing

```bash
# Run all tests
composer test

# Run tests with coverage
composer test-coverage

# Run static analysis
composer analyse

# Format code
composer format

# Check code style
composer format-check
```

### Using Makefile

```bash
# Setup development environment
make setup

# Run all CI checks
make ci

# Show available commands
make help
```

### Using Docker for Testing

```bash
# Start test databases
docker-compose up -d

# Run tests
composer test
```

---

## 📄 License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

---

## 🤝 Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

---

## 🛠️ Development

### Prerequisites

- PHP 8.1+
- Composer
- Laravel 10+ or 11+

### Setup

```bash
# Clone the repository
git clone https://github.com/mohamedhekal/laravel-schema-track.git
cd laravel-schema-track

# Install dependencies
composer install

# Setup development environment
make setup
```

### Running Tests

```bash
# Run all tests
make test

# Run tests with coverage
make test-coverage

# Run static analysis
make analyse

# Format code
make format
```

### Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

### Prerequisites

- PHP 8.1+
- Composer
- Laravel 10+ or 11+

### Setup

```bash
# Clone the repository
git clone https://github.com/mohamedhekal/laravel-schema-track.git
cd laravel-schema-track

# Install dependencies
composer install

# Setup development environment
make setup
```

### Running Tests

```bash
# Run all tests
make test

# Run tests with coverage
make test-coverage

# Run static analysis
make analyse

# Format code
make format
```

### Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## 📞 Support

- 📧 Email: mohamedhekal@example.com
- 🐛 Issues: [GitHub Issues](https://github.com/mohamedhekal/laravel-schema-track/issues)
- 📖 Documentation: [Wiki](https://github.com/mohamedhekal/laravel-schema-track/wiki) 
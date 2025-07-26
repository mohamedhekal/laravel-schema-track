# Laravel SchemaTrack Wiki

Welcome to the Laravel SchemaTrack Wiki! This comprehensive guide will help you understand, install, configure, and use Laravel SchemaTrack effectively.

## 📚 Table of Contents

### Getting Started
- [Installation](Installation.md)
- [Quick Start Guide](Quick-Start.md)
- [Configuration](Configuration.md)

### Core Features
- [Schema Snapshots](Schema-Snapshots.md)
- [Schema Diffing](Schema-Diffing.md)
- [Changelog Generation](Changelog-Generation.md)
- [Environment Comparison](Environment-Comparison.md)

### Commands Reference
- [Artisan Commands](Artisan-Commands.md)
- [Command Examples](Command-Examples.md)

### Advanced Usage
- [Custom Snapshots](Custom-Snapshots.md)
- [Integration with CI/CD](CI-CD-Integration.md)
- [Best Practices](Best-Practices.md)
- [Troubleshooting](Troubleshooting.md)

### Development
- [Contributing](Contributing.md)
- [Testing](Testing.md)
- [Architecture](Architecture.md)

## 🚀 What is Laravel SchemaTrack?

Laravel SchemaTrack is a powerful package that provides **version-controlled schema history** and **change tracking** for Laravel applications. Think of it as **Git for your database schema**.

### Key Features

- 🔄 **Auto Snapshots**: Automatically captures schema state after migrations
- 📊 **Visual Diffs**: Compare schema versions with detailed change reports
- 📝 **Changelog Generation**: Generate human-readable change logs
- 🌐 **Multi-Environment Support**: Compare schemas across environments
- 🛡️ **Safe by Design**: Read-only operations, never modifies your database
- 🎯 **Laravel Integration**: Seamless integration with Laravel's migration system

### Use Cases

| Scenario | Benefit |
|----------|---------|
| **Large Teams** | Prevent migration conflicts and track schema evolution |
| **QA Testing** | Communicate database changes clearly across versions |
| **Debugging** | Quickly identify when schema changes occurred |
| **CI/CD** | Detect risky schema changes before deployment |
| **Documentation** | Maintain up-to-date schema documentation |

## 🎯 Quick Overview

```bash
# Take a snapshot of current schema
php artisan schema:track

# List all snapshots
php artisan schema:list

# Compare two schema versions
php artisan schema:diff --from=2024_01_01 --to=latest

# Generate changelog
php artisan schema:changelog --format=markdown

# Compare with another environment
php artisan schema:compare --env=staging
```

## 📦 Installation

```bash
composer require mohamedhekal/laravel-schema-track
```

## 🔧 Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="MohamedHekal\LaravelSchemaTrack\SchemaTrackServiceProvider"
```

## 📖 Next Steps

- [Installation Guide](Installation.md) - Detailed installation instructions
- [Quick Start](Quick-Start.md) - Get up and running in 5 minutes
- [Configuration](Configuration.md) - Customize SchemaTrack for your needs

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](Contributing.md) for details.

## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE.md). 
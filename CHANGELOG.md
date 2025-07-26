# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Initial release of Laravel SchemaTrack
- Schema snapshot functionality
- Schema diff comparison
- Changelog generation
- Artisan commands for schema management
- Auto-snapshot on migrations
- Support for MySQL, PostgreSQL, and SQLite
- Breaking change detection
- Multiple output formats (text, markdown, JSON)

### Commands
- `schema:track` - Take a snapshot of current schema
- `schema:diff` - Compare two snapshots
- `schema:compare` - Compare with another environment
- `schema:changelog` - Generate changelog
- `schema:list` - List all snapshots

### Features
- Automatic schema snapshots after migrations
- Visual diff output with emojis and formatting
- Configurable excluded tables
- Environment comparison support
- Comprehensive test coverage
- GitHub Actions CI/CD pipeline

## [1.0.0] - 2024-01-XX

### Added
- Initial release 
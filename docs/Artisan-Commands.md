# Artisan Commands Reference

Complete reference for all Laravel SchemaTrack Artisan commands.

## 📋 Available Commands

| Command | Description | Usage |
|---------|-------------|-------|
| `schema:track` | Take a schema snapshot | `php artisan schema:track [options]` |
| `schema:list` | List all snapshots | `php artisan schema:list [options]` |
| `schema:diff` | Compare two snapshots | `php artisan schema:diff [options]` |
| `schema:changelog` | Generate changelog | `php artisan schema:changelog [options]` |
| `schema:compare` | Compare with environment | `php artisan schema:compare [options]` |

## 🔍 schema:track

Take a snapshot of the current database schema.

### Syntax
```bash
php artisan schema:track [--name=NAME] [--description=DESCRIPTION] [--force]
```

### Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `--name` | string | auto-generated | Custom name for the snapshot |
| `--description` | string | null | Description of the snapshot |
| `--force` | flag | false | Overwrite existing snapshot with same name |

### Examples

```bash
# Take snapshot with auto-generated name
php artisan schema:track

# Take snapshot with custom name
php artisan schema:track --name="before_user_migration"

# Take snapshot with description
php artisan schema:track --name="v1.2.0" --description="Schema before adding user roles"

# Force overwrite existing snapshot
php artisan schema:track --name="latest" --force
```

### Output
```
✅ Schema snapshot created successfully!
📁 Name: 2024_01_15_143022_before_user_migration
📊 Tables: 5
💾 Size: 2.3 KB
```

## 📋 schema:list

List all available schema snapshots.

### Syntax
```bash
php artisan schema:list [--limit=LIMIT] [--verbose] [--format=FORMAT]
```

### Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `--limit` | integer | all | Limit number of snapshots to display |
| `--verbose` | flag | false | Show detailed information |
| `--format` | string | table | Output format (table, json, csv) |

### Examples

```bash
# List all snapshots
php artisan schema:list

# List last 5 snapshots
php artisan schema:list --limit=5

# Show detailed information
php artisan schema:list --verbose

# Export as JSON
php artisan schema:list --format=json

# Export as CSV
php artisan schema:list --format=csv
```

### Output

#### Table Format
```
+----------------------+---------------------+------------------+------------------+
| Name                 | Created At          | Tables           | Description      |
+----------------------+---------------------+------------------+------------------+
| 2024_01_15_143022    | 2024-01-15 14:30:22 | 5                | Auto-snapshot    |
| 2024_01_15_140000    | 2024-01-15 14:00:00 | 4                | Before migration |
| 2024_01_14_120000    | 2024-01-14 12:00:00 | 3                | Initial schema   |
+----------------------+---------------------+------------------+------------------+
```

#### Verbose Format
```
📁 Snapshot: 2024_01_15_143022
⏰ Created: 2024-01-15 14:30:22
📊 Tables: 5 (users, posts, comments, categories, tags)
💾 Size: 2.3 KB
📝 Description: Auto-snapshot after migration
🔗 File: storage/schema-track/2024_01_15_143022.json

📁 Snapshot: 2024_01_15_140000
⏰ Created: 2024-01-15 14:00:00
📊 Tables: 4 (users, posts, comments, categories)
💾 Size: 1.8 KB
📝 Description: Before migration
🔗 File: storage/schema-track/2024_01_15_140000.json
```

## 🔍 schema:diff

Compare two schema snapshots and show differences.

### Syntax
```bash
php artisan schema:diff --from=FROM --to=TO [--format=FORMAT] [--breaking-only] [--output=OUTPUT]
```

### Options

| Option | Type | Required | Description |
|--------|------|----------|-------------|
| `--from` | string | yes | Source snapshot (name or 'latest', 'previous') |
| `--to` | string | yes | Target snapshot (name or 'latest', 'previous') |
| `--format` | string | no | Output format (text, markdown, json) |
| `--breaking-only` | flag | no | Show only breaking changes |
| `--output` | string | no | Save output to file |

### Examples

```bash
# Compare latest with previous
php artisan schema:diff --from=latest --to=previous

# Compare specific snapshots
php artisan schema:diff --from=2024_01_01 --to=2024_01_15

# Compare with markdown format
php artisan schema:diff --from=latest --to=previous --format=markdown

# Show only breaking changes
php artisan schema:diff --from=latest --to=previous --breaking-only

# Save diff to file
php artisan schema:diff --from=latest --to=previous --output=diff.md
```

### Output

#### Text Format
```
📊 Schema Comparison: 2024_01_15_140000 → 2024_01_15_143022

🆕 New Tables:
  - user_profiles (id, user_id, bio, avatar, created_at, updated_at)

📝 Modified Tables:
  - users
    + Added Column: role (string, nullable, default: 'user')
    + Added Index: users_role_index
    + Modified Column: email (now unique)

❌ Removed Tables:
  - temp_data

✅ Summary: 1 new table, 1 modified table, 1 removed table, 0 breaking changes
```

#### Markdown Format
```markdown
# Schema Changes (2024-01-15)

## New Tables

### user_profiles
- `id` (bigint, primary key, auto-increment)
- `user_id` (bigint, foreign key to users.id)
- `bio` (text, nullable)
- `avatar` (string, nullable)
- `created_at` (timestamp, nullable)
- `updated_at` (timestamp, nullable)

## Modified Tables

### users
- **Added Column**: `role` (string, nullable, default: 'user')
- **Added Index**: `users_role_index` on `role` column
- **Modified Column**: `email` (now unique)

## Removed Tables

### temp_data
- This table was completely removed
```

## 📝 schema:changelog

Generate a changelog from schema snapshots.

### Syntax
```bash
php artisan schema:changelog [--from=FROM] [--to=TO] [--format=FORMAT] [--output=OUTPUT] [--date-range]
```

### Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `--from` | string | null | Start snapshot or date |
| `--to` | string | null | End snapshot or date |
| `--format` | string | markdown | Output format (markdown, json, text) |
| `--output` | string | null | Save to file |
| `--date-range` | flag | false | Use date range instead of snapshots |

### Examples

```bash
# Generate full changelog
php artisan schema:changelog

# Generate changelog between snapshots
php artisan schema:changelog --from=2024_01_01 --to=2024_01_15

# Generate changelog for date range
php artisan schema:changelog --from=2024-01-01 --to=2024-01-31 --date-range

# Generate JSON changelog
php artisan schema:changelog --format=json

# Save changelog to file
php artisan schema:changelog --output=CHANGELOG.md
```

### Output

#### Markdown Format
```markdown
# Schema Changelog

## 2024-01-15

### New Tables

#### user_profiles
- `id` (bigint, primary key, auto-increment)
- `user_id` (bigint, foreign key to users.id)
- `bio` (text, nullable)
- `avatar` (string, nullable)
- `created_at` (timestamp, nullable)
- `updated_at` (timestamp, nullable)

### Modified Tables

#### users
- **Added Column**: `role` (string, nullable, default: 'user')
- **Added Index**: `users_role_index` on `role` column

## 2024-01-14

### New Tables

#### categories
- `id` (bigint, primary key, auto-increment)
- `name` (string, not null)
- `slug` (string, unique)
- `created_at` (timestamp, nullable)
- `updated_at` (timestamp, nullable)
```

## 🌐 schema:compare

Compare current schema with another environment.

### Syntax
```bash
php artisan schema:compare --env=ENV [--format=FORMAT] [--output=OUTPUT]
```

### Options

| Option | Type | Required | Description |
|--------|------|----------|-------------|
| `--env` | string | yes | Environment to compare with |
| `--format` | string | no | Output format (text, markdown, json) |
| `--output` | string | no | Save output to file |

### Examples

```bash
# Compare with staging environment
php artisan schema:compare --env=staging

# Compare with production
php artisan schema:compare --env=production

# Save comparison to file
php artisan schema:compare --env=staging --output=staging-diff.md
```

### Output
```
🌐 Environment Comparison: local → staging

📊 Database: staging_db
🔗 Connection: staging

🆕 New Tables in Local:
  - user_profiles
  - audit_logs

📝 Modified Tables:
  - users (added role column)
  - posts (added published_at column)

❌ Missing Tables in Local:
  - temp_cache

✅ Summary: 2 new tables, 2 modified tables, 1 missing table
```

## 🎯 Special Keywords

### Snapshot References

| Keyword | Description |
|---------|-------------|
| `latest` | Most recent snapshot |
| `previous` | Second most recent snapshot |
| `first` | Oldest snapshot |
| `last` | Latest snapshot |

### Date Formats

| Format | Example | Description |
|--------|---------|-------------|
| `YYYY-MM-DD` | `2024-01-15` | Full date |
| `YYYY_MM_DD` | `2024_01_15` | Date with underscores |
| `YYYYMMDD` | `20240115` | Compact date |

## 🔧 Global Options

All commands support these global Laravel options:

| Option | Description |
|--------|-------------|
| `--help` | Show command help |
| `--quiet` | Suppress output |
| `--verbose` | Increase verbosity |
| `--version` | Show version |
| `--ansi` | Force ANSI output |
| `--no-ansi` | Disable ANSI output |

## 📚 Related Documentation

- [Quick Start Guide](Quick-Start.md) - Get started quickly
- [Configuration](Configuration.md) - Configure SchemaTrack
- [Best Practices](Best-Practices.md) - Command best practices
- [Troubleshooting](Troubleshooting.md) - Common issues 
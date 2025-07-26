# Quick Start Guide

Get Laravel SchemaTrack up and running in your project in under 5 minutes!

## ⚡ 5-Minute Setup

### 1. Install the Package

```bash
composer require mohamedhekal/laravel-schema-track
```

### 2. Publish Configuration (Optional)

```bash
php artisan vendor:publish --provider="MohamedHekal\LaravelSchemaTrack\SchemaTrackServiceProvider"
```

### 3. Take Your First Snapshot

```bash
php artisan schema:track
```

That's it! You're ready to start tracking your database schema changes.

## 🎯 Basic Usage

### Taking Snapshots

```bash
# Take a snapshot with custom name
php artisan schema:track --name="before_user_migration"

# Take a snapshot with description
php artisan schema:track --description="Schema before adding user roles"
```

### Listing Snapshots

```bash
# List all snapshots
php artisan schema:list

# List last 5 snapshots
php artisan schema:list --limit=5

# Show detailed information
php artisan schema:list --verbose
```

### Comparing Schemas

```bash
# Compare latest with previous
php artisan schema:diff --from=latest --to=previous

# Compare specific snapshots
php artisan schema:diff --from=2024_01_01 --to=2024_01_15

# Compare with specific environment
php artisan schema:compare --env=staging
```

### Generating Changelogs

```bash
# Generate markdown changelog
php artisan schema:changelog --format=markdown

# Generate changelog for specific date range
php artisan schema:changelog --from=2024-01-01 --to=2024-01-31

# Save changelog to file
php artisan schema:changelog --output=CHANGELOG.md
```

## 🔄 Auto-Snapshots

SchemaTrack automatically takes snapshots after migrations when enabled:

```bash
# Run migrations (auto-snapshot will be taken)
php artisan migrate

# Check the new snapshot
php artisan schema:list
```

## 📊 Sample Output

### Schema List
```
+----------------------+---------------------+------------------+------------------+
| Name                 | Created At          | Tables           | Description      |
+----------------------+---------------------+------------------+------------------+
| 2024_01_15_143022    | 2024-01-15 14:30:22 | 5                | Auto-snapshot    |
| 2024_01_15_140000    | 2024-01-15 14:00:00 | 4                | Before migration |
| 2024_01_14_120000    | 2024-01-14 12:00:00 | 3                | Initial schema   |
+----------------------+---------------------+------------------+------------------+
```

### Schema Diff
```
📊 Schema Comparison: 2024_01_15_140000 → 2024_01_15_143022

🆕 New Tables:
  - user_profiles (id, user_id, bio, avatar, created_at, updated_at)

📝 Modified Tables:
  - users
    + Added Column: role (string, nullable, default: 'user')
    + Added Index: users_role_index

✅ Summary: 1 new table, 1 modified table, 0 breaking changes
```

### Changelog
```markdown
# Schema Changelog (2024-01-15)

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
```

## 🛠️ Integration Examples

### In Your Workflow

```bash
# 1. Before making changes
php artisan schema:track --name="before_changes"

# 2. Make your database changes
php artisan migrate

# 3. Review changes
php artisan schema:diff --from="before_changes" --to=latest

# 4. Generate changelog for team
php artisan schema:changelog --from="before_changes" --to=latest --output=changes.md
```

### In CI/CD Pipeline

```yaml
# .github/workflows/deploy.yml
- name: Track Schema Changes
  run: |
    php artisan schema:track --name="pre-deploy-$(date +%Y%m%d_%H%M%S)"
    
- name: Check for Breaking Changes
  run: |
    php artisan schema:diff --from=latest --to=previous --breaking-only
```

### In Development

```bash
# Daily workflow
php artisan schema:track --name="daily-$(date +%Y%m%d)"
php artisan schema:list --limit=7  # Show last week
```

## 🎯 Common Use Cases

### 1. Team Development
```bash
# Before starting work
php artisan schema:track --name="before-feature-branch"

# After completing work
php artisan schema:diff --from="before-feature-branch" --to=latest
```

### 2. Release Management
```bash
# Before release
php artisan schema:track --name="v1.2.0-pre"

# After release
php artisan schema:track --name="v1.2.0-post"
php artisan schema:changelog --from="v1.2.0-pre" --to="v1.2.0-post" --output=RELEASE_NOTES.md
```

### 3. Debugging
```bash
# When issue occurs
php artisan schema:track --name="issue-$(date +%Y%m%d_%H%M%S)"

# Compare with known good state
php artisan schema:diff --from="known-good-state" --to="issue-20240115_143022"
```

## 🚀 Next Steps

- [Configuration](Configuration.md) - Customize SchemaTrack settings
- [Artisan Commands](Artisan-Commands.md) - Complete command reference
- [Best Practices](Best-Practices.md) - Learn best practices
- [Troubleshooting](Troubleshooting.md) - Common issues and solutions 
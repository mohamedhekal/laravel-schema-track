# Best Practices

Learn the best practices for using Laravel SchemaTrack effectively in your projects.

## 🎯 General Best Practices

### 1. Naming Conventions

#### Snapshot Names
Use descriptive, consistent naming patterns:

```bash
# ✅ Good naming patterns
php artisan schema:track --name="v1.2.0-pre-release"
php artisan schema:track --name="before-user-roles-migration"
php artisan schema:track --name="feature-branch-user-profiles"
php artisan schema:track --name="daily-$(date +%Y%m%d)"

# ❌ Avoid generic names
php artisan schema:track --name="test"
php artisan schema:track --name="snapshot1"
```

#### Recommended Naming Patterns

| Pattern | Example | Use Case |
|---------|---------|----------|
| `v{version}-{stage}` | `v1.2.0-pre-release` | Release management |
| `before-{feature}-migration` | `before-user-roles-migration` | Before migrations |
| `feature-{branch}-{feature}` | `feature-branch-user-profiles` | Feature development |
| `daily-{YYYYMMDD}` | `daily-20240115` | Daily snapshots |
| `{environment}-{date}` | `staging-20240115` | Environment snapshots |

### 2. Snapshot Frequency

#### Development Workflow
```bash
# Before starting work
php artisan schema:track --name="before-feature-$(date +%Y%m%d)"

# After completing work
php artisan schema:track --name="after-feature-$(date +%Y%m%d)"

# Generate diff for review
php artisan schema:diff --from="before-feature-$(date +%Y%m%d)" --to=latest
```

#### Release Management
```bash
# Before release
php artisan schema:track --name="v1.2.0-pre"

# After release
php artisan schema:track --name="v1.2.0-post"

# Generate release notes
php artisan schema:changelog --from="v1.2.0-pre" --to="v1.2.0-post" --output=RELEASE_NOTES.md
```

### 3. Storage Management

#### Regular Cleanup
```bash
# List snapshots older than 30 days
php artisan schema:list --older-than=30

# Keep only last 50 snapshots
php artisan schema:list --limit=50
```

#### Backup Strategy
```bash
# Daily backup script
#!/bin/bash
DATE=$(date +%Y%m%d)
cp -r storage/schema-track /backup/schema-track-$DATE
find /backup -name "schema-track-*" -mtime +30 -delete
```

## 🔄 Workflow Integration

### 1. Git Workflow

#### Pre-commit Hook
```bash
#!/bin/bash
# .git/hooks/pre-commit

# Take snapshot before commit
php artisan schema:track --name="pre-commit-$(date +%Y%m%d_%H%M%S)"
```

#### Feature Branch Workflow
```bash
# 1. Create feature branch
git checkout -b feature/user-roles

# 2. Take initial snapshot
php artisan schema:track --name="feature-user-roles-start"

# 3. Make changes and run migrations
php artisan migrate

# 4. Take final snapshot
php artisan schema:track --name="feature-user-roles-end"

# 5. Generate diff for PR
php artisan schema:diff --from="feature-user-roles-start" --to="feature-user-roles-end" --output=SCHEMA_CHANGES.md
```

### 2. CI/CD Integration

#### GitHub Actions
```yaml
# .github/workflows/deploy.yml
name: Deploy

on:
  push:
    branches: [main]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
          
      - name: Install dependencies
        run: composer install
        
      - name: Take pre-deploy snapshot
        run: php artisan schema:track --name="pre-deploy-$(date +%Y%m%d_%H%M%S)"
        
      - name: Run migrations
        run: php artisan migrate
        
      - name: Check for breaking changes
        run: php artisan schema:diff --from=latest --to=previous --breaking-only
        
      - name: Generate deployment changelog
        run: php artisan schema:changelog --from=previous --to=latest --output=deployment-changes.md
```

#### GitLab CI
```yaml
# .gitlab-ci.yml
stages:
  - test
  - deploy

test:
  stage: test
  script:
    - composer install
    - php artisan schema:track --name="test-$(date +%Y%m%d_%H%M%S)"
    - php artisan migrate
    - php artisan schema:diff --from=latest --to=previous

deploy:
  stage: deploy
  script:
    - php artisan schema:track --name="pre-deploy-$(date +%Y%m%d_%H%M%S)"
    - php artisan migrate
    - php artisan schema:changelog --from=previous --to=latest --output=deployment-changes.md
```

### 3. Team Collaboration

#### Code Review Process
```bash
# 1. Developer creates feature branch
git checkout -b feature/new-table

# 2. Takes initial snapshot
php artisan schema:track --name="feature-new-table-start"

# 3. Makes changes
php artisan migrate

# 4. Takes final snapshot
php artisan schema:track --name="feature-new-table-end"

# 5. Generates diff for review
php artisan schema:diff --from="feature-new-table-start" --to="feature-new-table-end" --output=REVIEW_CHANGES.md

# 6. Adds to PR description
echo "## Schema Changes" >> PR_DESCRIPTION.md
cat REVIEW_CHANGES.md >> PR_DESCRIPTION.md
```

#### Team Communication
```bash
# Daily standup script
#!/bin/bash
echo "📊 Schema Changes Yesterday:"
php artisan schema:changelog --from="$(date -d 'yesterday' +%Y-%m-%d)" --to="$(date +%Y-%m-%d)" --format=text

echo "🔍 Breaking Changes:"
php artisan schema:diff --from=latest --to=previous --breaking-only
```

## 🛡️ Security Best Practices

### 1. File Permissions
```bash
# Set proper permissions
chmod 755 storage/schema-track
chown www-data:www-data storage/schema-track

# For production
chmod 750 storage/schema-track
```

### 2. Sensitive Data
```bash
# Exclude sensitive tables from snapshots
# config/schema-track.php
'excluded_tables' => [
    'migrations',
    'failed_jobs',
    'password_reset_tokens',
    'personal_access_tokens',
    'sessions',
    'oauth_access_tokens',  // Add sensitive tables
    'oauth_refresh_tokens',
    'user_secrets',
],
```

### 3. Backup Security
```bash
# Encrypt backups
gpg --encrypt --recipient your-email@example.com schema-track-backup.tar.gz

# Store in secure location
aws s3 cp schema-track-backup.tar.gz.gpg s3://secure-backups/
```

## 📊 Monitoring and Alerting

### 1. Breaking Changes Alert
```bash
#!/bin/bash
# check-breaking-changes.sh

CHANGES=$(php artisan schema:diff --from=latest --to=previous --breaking-only --format=json)

if [ ! -z "$CHANGES" ]; then
    echo "🚨 Breaking schema changes detected!"
    echo "$CHANGES"
    
    # Send notification
    curl -X POST -H 'Content-type: application/json' \
        --data "{\"text\":\"🚨 Breaking schema changes detected!\"}" \
        $SLACK_WEBHOOK_URL
fi
```

### 2. Schema Drift Detection
```bash
#!/bin/bash
# detect-schema-drift.sh

# Compare with expected schema
php artisan schema:compare --env=production --output=drift-report.md

if [ -s drift-report.md ]; then
    echo "⚠️ Schema drift detected!"
    cat drift-report.md
    
    # Send notification
    curl -X POST -H 'Content-type: application/json' \
        --data "{\"text\":\"⚠️ Schema drift detected in production!\"}" \
        $SLACK_WEBHOOK_URL
fi
```

## 🎯 Performance Optimization

### 1. Large Database Optimization
```php
// config/schema-track.php
'performance' => [
    'batch_size' => 100,  // Process tables in batches
    'memory_limit' => '512M',
    'timeout' => 300,  // 5 minutes
],
```

### 2. Selective Snapshot
```bash
# Take snapshot of specific tables only
php artisan schema:track --tables="users,posts,comments"

# Exclude large tables
php artisan schema:track --exclude="logs,audit_trails"
```

## 📚 Documentation Best Practices

### 1. Schema Documentation
```bash
# Generate comprehensive documentation
php artisan schema:changelog --from=first --to=latest --format=markdown --output=SCHEMA_HISTORY.md

# Add to project documentation
echo "# Database Schema History" >> docs/DATABASE.md
cat SCHEMA_HISTORY.md >> docs/DATABASE.md
```

### 2. Release Documentation
```bash
# Generate release notes
php artisan schema:changelog --from="v1.1.0" --to="v1.2.0" --output=RELEASE_NOTES.md

# Add to GitHub release
gh release create v1.2.0 --notes-file RELEASE_NOTES.md
```

## 🚨 Anti-Patterns to Avoid

### ❌ Don't Do This

```bash
# Don't take snapshots too frequently
php artisan schema:track  # Every few minutes

# Don't use generic names
php artisan schema:track --name="snapshot"

# Don't ignore breaking changes
php artisan schema:diff --from=latest --to=previous  # Without checking

# Don't store snapshots in version control
git add storage/schema-track/  # Large binary files
```

### ✅ Do This Instead

```bash
# Take snapshots at meaningful points
php artisan schema:track --name="before-migration-$(date +%Y%m%d)"

# Use descriptive names
php artisan schema:track --name="add-user-roles-feature"

# Always check for breaking changes
php artisan schema:diff --from=latest --to=previous --breaking-only

# Exclude snapshots from version control
echo "storage/schema-track/" >> .gitignore
```

## 📖 Related Documentation

- [Installation Guide](Installation.md) - Setup instructions
- [Configuration](Configuration.md) - Configuration options
- [Artisan Commands](Artisan-Commands.md) - Command reference
- [Troubleshooting](Troubleshooting.md) - Common issues 
# Troubleshooting Guide

Common issues and their solutions when using Laravel SchemaTrack.

## 🚨 Common Issues

### 1. Command Not Found

#### Issue
```bash
php artisan schema:track
# Command "schema:track" is not defined.
```

#### Solution
1. **Check if package is installed:**
   ```bash
   composer show mohamedhekal/laravel-schema-track
   ```

2. **Reinstall the package:**
   ```bash
   composer remove mohamedhekal/laravel-schema-track
   composer require mohamedhekal/laravel-schema-track
   ```

3. **Clear Laravel cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

4. **Check service provider registration:**
   ```php
   // config/app.php
   'providers' => [
       // ...
       MohamedHekal\LaravelSchemaTrack\SchemaTrackServiceProvider::class,
   ],
   ```

### 2. Storage Directory Issues

#### Issue
```bash
php artisan schema:track
# Error: Storage directory is not writable
```

#### Solution
1. **Create storage directory:**
   ```bash
   mkdir -p storage/schema-track
   ```

2. **Set proper permissions:**
   ```bash
   chmod 755 storage/schema-track
   chown www-data:www-data storage/schema-track  # For web server
   ```

3. **Check disk space:**
   ```bash
   df -h storage/
   ```

### 3. Database Connection Issues

#### Issue
```bash
php artisan schema:track
# Error: Could not connect to database
```

#### Solution
1. **Check database configuration:**
   ```bash
   php artisan config:show database
   ```

2. **Test database connection:**
   ```bash
   php artisan tinker
   DB::connection()->getPdo();
   ```

3. **Verify environment variables:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

### 4. Permission Denied Errors

#### Issue
```bash
php artisan schema:track
# Permission denied: storage/schema-track/
```

#### Solution
1. **Check current permissions:**
   ```bash
   ls -la storage/
   ```

2. **Fix permissions:**
   ```bash
   sudo chown -R $USER:$USER storage/
   chmod -R 755 storage/
   ```

3. **For production servers:**
   ```bash
   sudo chown -R www-data:www-data storage/
   chmod -R 750 storage/
   ```

### 5. Memory Issues

#### Issue
```bash
php artisan schema:track
# Fatal error: Allowed memory size exhausted
```

#### Solution
1. **Increase PHP memory limit:**
   ```bash
   php -d memory_limit=512M artisan schema:track
   ```

2. **Update php.ini:**
   ```ini
   memory_limit = 512M
   ```

3. **Use configuration optimization:**
   ```php
   // config/schema-track.php
   'performance' => [
       'memory_limit' => '512M',
       'batch_size' => 50,
   ],
   ```

### 6. Large Database Performance

#### Issue
```bash
php artisan schema:track
# Takes too long to complete
```

#### Solution
1. **Exclude large tables:**
   ```php
   // config/schema-track.php
   'excluded_tables' => [
       'migrations',
       'failed_jobs',
       'large_log_table',
       'temp_data',
   ],
   ```

2. **Use selective snapshot:**
   ```bash
   php artisan schema:track --tables="users,posts,comments"
   ```

3. **Increase timeout:**
   ```bash
   php -d max_execution_time=300 artisan schema:track
   ```

### 7. Snapshot Not Found

#### Issue
```bash
php artisan schema:diff --from=latest --to=previous
# Error: Snapshot not found
```

#### Solution
1. **List available snapshots:**
   ```bash
   php artisan schema:list
   ```

2. **Check snapshot files:**
   ```bash
   ls -la storage/schema-track/
   ```

3. **Use correct snapshot name:**
   ```bash
   php artisan schema:diff --from="2024_01_15_143022" --to="2024_01_15_140000"
   ```

### 8. JSON Parsing Errors

#### Issue
```bash
php artisan schema:list
# Error: Invalid JSON in snapshot file
```

#### Solution
1. **Check corrupted files:**
   ```bash
   find storage/schema-track/ -name "*.json" -exec php -l {} \;
   ```

2. **Remove corrupted snapshots:**
   ```bash
   rm storage/schema-track/corrupted_file.json
   ```

3. **Regenerate snapshots:**
   ```bash
   php artisan schema:track --name="recovery-$(date +%Y%m%d_%H%M%S)"
   ```

### 9. Auto-Snapshot Not Working

#### Issue
```bash
php artisan migrate
# No auto-snapshot was taken
```

#### Solution
1. **Check auto-snapshot configuration:**
   ```php
   // config/schema-track.php
   'auto_snapshot' => true,
   ```

2. **Verify event listener:**
   ```php
   // Check if listener is registered
   php artisan event:list | grep MigrationsEnded
   ```

3. **Enable manually:**
   ```bash
   php artisan schema:track --name="after-migration-$(date +%Y%m%d_%H%M%S)"
   ```

### 10. Environment Comparison Issues

#### Issue
```bash
php artisan schema:compare --env=staging
# Error: Environment configuration not found
```

#### Solution
1. **Check environment configuration:**
   ```php
   // config/database.php
   'connections' => [
       'staging' => [
           'driver' => 'mysql',
           'host' => env('STAGING_DB_HOST'),
           'database' => env('STAGING_DB_DATABASE'),
           'username' => env('STAGING_DB_USERNAME'),
           'password' => env('STAGING_DB_PASSWORD'),
       ],
   ],
   ```

2. **Set environment variables:**
   ```env
   STAGING_DB_HOST=staging.example.com
   STAGING_DB_DATABASE=staging_db
   STAGING_DB_USERNAME=staging_user
   STAGING_DB_PASSWORD=staging_password
   ```

## 🔧 Debugging Commands

### 1. Check Package Status
```bash
# Verify installation
composer show mohamedhekal/laravel-schema-track

# Check Laravel version compatibility
php artisan --version
```

### 2. Debug Configuration
```bash
# Show configuration
php artisan config:show schema-track

# Check storage path
php artisan tinker
config('schema-track.storage_path')
```

### 3. Test Database Connection
```bash
# Test connection
php artisan tinker
DB::connection()->getPdo();

# Check table list
php artisan tinker
DB::select('SHOW TABLES');
```

### 4. Verify File System
```bash
# Check storage directory
ls -la storage/schema-track/

# Check permissions
stat storage/schema-track/

# Check disk space
df -h storage/
```

## 📊 Diagnostic Scripts

### 1. Health Check Script
```bash
#!/bin/bash
# health-check.sh

echo "🔍 Laravel SchemaTrack Health Check"
echo "=================================="

# Check package installation
if composer show mohamedhekal/laravel-schema-track > /dev/null 2>&1; then
    echo "✅ Package installed"
else
    echo "❌ Package not installed"
fi

# Check storage directory
if [ -d "storage/schema-track" ]; then
    echo "✅ Storage directory exists"
    if [ -w "storage/schema-track" ]; then
        echo "✅ Storage directory writable"
    else
        echo "❌ Storage directory not writable"
    fi
else
    echo "❌ Storage directory missing"
fi

# Check database connection
if php artisan tinker --execute="DB::connection()->getPdo();" > /dev/null 2>&1; then
    echo "✅ Database connection OK"
else
    echo "❌ Database connection failed"
fi

# Check commands
if php artisan list | grep schema > /dev/null 2>&1; then
    echo "✅ Commands registered"
else
    echo "❌ Commands not found"
fi

echo "=================================="
```

### 2. Snapshot Recovery Script
```bash
#!/bin/bash
# recover-snapshots.sh

echo "🔄 Recovering Schema Snapshots"
echo "=============================="

# Backup existing snapshots
if [ -d "storage/schema-track" ]; then
    cp -r storage/schema-track storage/schema-track-backup-$(date +%Y%m%d_%H%M%S)
    echo "✅ Existing snapshots backed up"
fi

# Remove corrupted files
find storage/schema-track/ -name "*.json" -exec php -l {} \; 2>&1 | grep "Parse error" | cut -d: -f1 | xargs rm -f
echo "✅ Corrupted files removed"

# Take new snapshot
php artisan schema:track --name="recovery-$(date +%Y%m%d_%H%M%S)"
echo "✅ New snapshot taken"

echo "=============================="
```

## 🆘 Getting Help

### 1. Enable Debug Mode
```bash
# Enable Laravel debug mode
APP_DEBUG=true php artisan schema:track

# Enable verbose output
php artisan schema:track --verbose
```

### 2. Check Logs
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log

# Check system logs
tail -f /var/log/syslog | grep php
```

### 3. Report Issues
When reporting issues, include:

1. **Laravel version:**
   ```bash
   php artisan --version
   ```

2. **Package version:**
   ```bash
   composer show mohamedhekal/laravel-schema-track
   ```

3. **PHP version:**
   ```bash
   php --version
   ```

4. **Database type and version:**
   ```bash
   php artisan tinker
   DB::connection()->getPdo()->getAttribute(PDO::ATTR_SERVER_VERSION)
   ```

5. **Error message and stack trace**
6. **Configuration (without sensitive data)**
7. **Steps to reproduce**

### 4. Community Support

- **GitHub Issues:** [Report bugs and feature requests](https://github.com/mohamedhekal/laravel-schema-track/issues)
- **Discussions:** [Ask questions and share solutions](https://github.com/mohamedhekal/laravel-schema-track/discussions)
- **Documentation:** [Browse the wiki](https://github.com/mohamedhekal/laravel-schema-track/wiki)

## 📚 Related Documentation

- [Installation Guide](Installation.md) - Setup instructions
- [Configuration](Configuration.md) - Configuration options
- [Best Practices](Best-Practices.md) - Best practices
- [Artisan Commands](Artisan-Commands.md) - Command reference 
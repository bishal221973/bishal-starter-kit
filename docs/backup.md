# Backup

The Bishal Starter Kit uses **Spatie Laravel Backup** for application backup management.

Spatie Laravel Backup provides functionality for creating backups of the application and database and managing backup retention.

The starter kit also provides configuration options for controlling automatic backup behavior.

## Backup Configuration

Backup settings are stored in the `configurations` table.

The following fields control automatic backups:

```text
enable_auto_backup
backup_frequency
backup_retention_days
```

## Enable Automatic Backup

Automatic backups are controlled by:

```text
enable_auto_backup
```

The default value is:

```text
enable_auto_backup = false
```

When enabled, the application can run its configured backup process automatically.

## Backup Frequency

The `backup_frequency` setting determines how frequently automatic backups should run.

The default value is:

```text
backup_frequency = daily
```

Example:

```text
[
    'enable_auto_backup' => true,
    'backup_frequency' => 'daily',
]
```

The exact supported frequency values depend on the scheduler implementation used by the starter kit.

## Backup Retention

The starter kit provides:

```text
backup_retention_days
```

This controls how long backups should be retained.

The default value is:

```text
backup_retention_days = 30
```

For example:

```text
backup_retention_days = 7
```

can be used when backups should be retained for 7 days.

The actual deletion of old backups is handled through the backup cleanup functionality provided by Spatie Laravel Backup.

## Spatie Laravel Backup

The starter kit uses the Spatie Laravel Backup package rather than implementing its own backup engine.

The package is responsible for the backup process, including the configured backup sources and backup storage.

The package configuration is normally available in:

```text
config/backup.php
```

If the package configuration has been published, it can be customized according to the application's backup requirements.

## Creating a Backup

Spatie Laravel Backup provides Artisan commands for creating backups.

The main backup command is:

```bash
php artisan backup:run
```

This command creates a new backup using the application's backup configuration.

To create a backup without certain optional operations, use the options supported by the installed version of Spatie Laravel Backup.

## Cleaning Old Backups

Spatie Laravel Backup provides a cleanup command:

```bash
php artisan backup:clean
```

This removes old backups according to the configured cleanup strategy.

The starter kit's:

```text
backup_retention_days
```

setting can be used as part of the application's retention configuration.

## Checking Backup Configuration

You can inspect the available backup commands using:

```bash
php artisan list
```

Search for backup commands:

```bash
php artisan list | grep backup
```

You should see commands provided by the Spatie backup package.

## Backup Scheduling

Automatic backups should be executed through Laravel's task scheduling system.

A scheduled backup can execute:

```bash
php artisan backup:run
```

The scheduling frequency should correspond to the starter kit's:

```text
backup_frequency
```

configuration.

For example, a daily backup can be scheduled through Laravel's scheduler.

The exact scheduler implementation depends on the version and configuration of the starter kit.

## Backup Storage

Spatie Laravel Backup supports configurable backup destinations.

The backup destination is configured through the package's backup configuration and filesystem configuration.

Check:

```text
config/backup.php
```

and:

```text
config/filesystems.php
```

when configuring backup storage.

The backup can be configured to use local or supported remote filesystem storage depending on the application's requirements.

## Database Backup

The backup package can include the application's database in the backup process.

The database configuration is taken from the application's Laravel database configuration.

Verify the database connection in:

```text
.env
```

For example:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=root
DB_PASSWORD=
```

A working database configuration is required for database backups to succeed.

## Application Files

In addition to the database, the backup package can back up configured application files.

The exact directories included in the backup are controlled by the Spatie backup configuration.

Check:

```text
config/backup.php
```

to determine which files and directories are included or excluded.

## Automatic Backup Configuration Example

An example configuration is:

```text
enable_auto_backup = true
backup_frequency = daily
backup_retention_days = 30
```

This enables automatic backups, schedules them according to the application's daily backup configuration, and retains backups according to the configured 30-day retention policy.

## Manual Backup

A backup can be triggered manually from the command line:

```bash
php artisan backup:run
```

This is useful for creating a backup before:

* Deploying a new version
* Running database migrations
* Performing major configuration changes
* Updating application dependencies
* Performing maintenance
* Making significant database changes

## Troubleshooting

### Backup Command Is Not Available

Check that Spatie Laravel Backup is installed:

```bash
composer show spatie/laravel-backup
```

Then check the available commands:

```bash
php artisan list | grep backup
```

If the package is installed correctly, its backup commands should be available.

### Backup Creation Fails

Run:

```bash
php artisan backup:run
```

and inspect the error message.

Check:

* Database connection
* Filesystem configuration
* Backup destination
* File permissions
* Available disk space
* `config/backup.php`

### Database Backup Fails

Verify the database connection:

```bash
php artisan about
```

Also check the database configuration in `.env`.

For MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=root
DB_PASSWORD=
```

### Old Backups Are Not Being Deleted

Run:

```bash
php artisan backup:clean
```

Then verify the package's cleanup configuration and the starter kit's:

```text
backup_retention_days
```

setting.

## Backup Configuration Summary

The Bishal Starter Kit provides the following application-level backup settings:

| Setting                 | Default | Description                                    |
| ----------------------- | ------: | ---------------------------------------------- |
| `enable_auto_backup`    | `false` | Enables automatic backups                      |
| `backup_frequency`      | `daily` | Defines the configured backup frequency        |
| `backup_retention_days` |    `30` | Defines the configured backup retention period |

## Summary

The Bishal Starter Kit uses **Spatie Laravel Backup** for backup functionality.

The starter kit provides application-level settings for:

* Enabling automatic backups
* Configuring backup frequency
* Configuring backup retention

The main manual backup command is:

```bash
php artisan backup:run
```

Old backups can be cleaned using:

```bash
php artisan backup:clean
```

Package-specific backup behavior should be configured through:

```text
config/backup.php
```

while application-level backup preferences are stored in the `configurations` table.

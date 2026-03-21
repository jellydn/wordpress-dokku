# WordPress on Dokku

This guide documents how to deploy WordPress to Dokku with MySQL database support.

## Prerequisites

- Dokku server with MySQL plugin installed
- Git access to the Dokku server

## Setup Steps

### 1. Create the Dokku App

```bash
dokku apps:create wp
```

### 2. Create and Link MySQL Database

```bash
# Create database
dokku mysql:create wp-db

# Link to app
dokku mysql:link wp wp-db
```

### 3. Configure Security Keys (Recommended)

Generate and set WordPress security salts:

```bash
dokku config:set wp \
  AUTH_KEY="$(openssl rand -base64 48)" \
  SECURE_AUTH_KEY="$(openssl rand -base64 48)" \
  LOGGED_IN_KEY="$(openssl rand -base64 48)" \
  NONCE_KEY="$(openssl rand -base64 48)" \
  AUTH_SALT="$(openssl rand -base64 48)" \
  SECURE_AUTH_SALT="$(openssl rand -base64 48)" \
  LOGGED_IN_SALT="$(openssl rand -base64 48)" \
  NONCE_SALT="$(openssl rand -base64 48)"
```

### 4. Add Git Remote and Deploy

```bash
git remote add dokku dokku@docklight.itman.fyi:wp
git push dokku main
```

## Required Files

### composer.json

Required for PHP buildpack detection:

```json
{
  "name": "wordpress/wordpress",
  "description": "WordPress",
  "type": "project",
  "require": {
    "php": ">=8.0"
  },
  "config": {
    "vendor-dir": "vendor"
  }
}
```

### composer.lock

Must be committed alongside `composer.json`. Run `composer update` locally to generate it, or create a minimal one:

```json
{
  "_readme": ["..."],
  "content-hash": "...",
  "packages": [],
  "packages-dev": [],
  "aliases": [],
  "minimum-stability": "stable",
  "stability-flags": [],
  "prefer-stable": false,
  "prefer-lowest": false,
  "platform": {
    "php": ">=8.0"
  },
  "platform-dev": [],
  "plugin-api-version": "2.6.0"
}
```

### wp-config.php

Key configuration to read from environment variables:

```php
<?php
// Parse DATABASE_URL from environment
$database_url = getenv('DATABASE_URL');

if ($database_url) {
    $url = parse_url($database_url);

    define('DB_NAME', ltrim($url['path'], '/'));
    define('DB_USER', $url['user']);
    define('DB_PASSWORD', $url['pass']);
    define('DB_HOST', $url['host'] . (isset($url['port']) ? ':' . $url['port'] : ''));
} else {
    // Fallback for local development
    define('DB_NAME', 'wordpress');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '');
    define('DB_HOST', 'localhost');
}

// Read security keys from environment or use defaults
define('AUTH_KEY', getenv('AUTH_KEY') ?: 'default-key');
// ... other keys

// Debug mode from environment
define('WP_DEBUG', getenv('WP_DEBUG') ?: false);
```

## Useful Commands

```bash
# View app logs
dokku logs wp -t

# Check configuration
dokku config wp

# Restart app
dokku ps:restart wp

# Open app in browser
dokku open wp
```

## Troubleshooting

### "Unable to select a buildpack"

Add `composer.json` and `composer.lock` files to trigger PHP buildpack detection.

### "No 'composer.lock' found!"

Ensure `composer.lock` is committed to git (not in `.gitignore`).

### Database Connection Errors

Verify `DATABASE_URL` is set:

```bash
dokku config:get wp DATABASE_URL
```

If empty, re-link the database:

```bash
dokku mysql:link wp wp-db
```

## Notes

- WordPress core files are committed to this repo (unusual but necessary for Dokku deployment)
- `wp-config.php` reads all sensitive data from environment variables
- The `.gitignore` has been modified to allow `wp-config.php` (normally ignored)

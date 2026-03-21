<?php
/**
 * The base configuration for WordPress
 *
 * @package WordPress
 */

// ** Parse DATABASE_URL from environment ** //
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

/** Database charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The database collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 */
define('AUTH_KEY',         getenv('AUTH_KEY')         ?: 'put your unique phrase here');
define('SECURE_AUTH_KEY',  getenv('SECURE_AUTH_KEY')  ?: 'put your unique phrase here');
define('LOGGED_IN_KEY',    getenv('LOGGED_IN_KEY')    ?: 'put your unique phrase here');
define('NONCE_KEY',        getenv('NONCE_KEY')        ?: 'put your unique phrase here');
define('AUTH_SALT',        getenv('AUTH_SALT')        ?: 'put your unique phrase here');
define('SECURE_AUTH_SALT', getenv('SECURE_AUTH_SALT') ?: 'put your unique phrase here');
define('LOGGED_IN_SALT',   getenv('LOGGED_IN_SALT')   ?: 'put your unique phrase here');
define('NONCE_SALT',       getenv('NONCE_SALT')       ?: 'put your unique phrase here');

/**#@-*/

/**
 * WordPress database table prefix.
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 */
define('WP_DEBUG', getenv('WP_DEBUG') ?: false);

/* Add any custom values between this line and the "stop editing" line. */

// Force HTTPS for Dokku deployments behind reverse proxy
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
	$_SERVER['HTTPS'] = 'on';
}

// Define site URLs if needed (optional, WordPress will auto-detect)
// define('WP_HOME', 'https://wp.itman.fyi');
// define('WP_SITEURL', 'https://wp.itman.fyi');

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
	define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

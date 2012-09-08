<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */

if (strpos($_SERVER['HTTP_HOST'], 'vcap.me')) {
    define('DB_NAME', 'wpsite');
    define('DB_USER', 'root');
    define('DB_PASSWORD', '');
    define('DB_HOST', 'localhost');
    define('WP_HOME', 'http://wpsite.vcap.me:8080');
    define('WP_SITEURL', 'http://wpsite.vcap.me:8080/wordpress');
} elseif (strpos($_SERVER['HTTP_HOST'], 'st.wpsite.co.uk')) {
    define('DB_NAME', 'wpsite_stage');
    define('DB_USER', 'wpsite_stage');
    define('DB_PASSWORD', '');
    define('DB_HOST', 'localhost');
    define('WP_HOME', 'http://test.wpsite.co.uk');
    define('WP_SITEURL', 'http://test.wpsite.co.uk/wordpress');
} else {
    define('DB_NAME', 'wpsite_prod');
    define('DB_USER', 'wpsite_prod');
    define('DB_PASSWORD', '');
    define('DB_HOST', 'localhost');
    define('WP_HOME', 'http://wpsite.co.uk');
    define('WP_SITEURL', 'http://wpsite.co.uk/wordpress');
}

 define('WP_CONTENT_DIR', dirname(__FILE__) . '/wordpress-content');
 define('WP_CONTENT_URL', WP_HOME . '/wordpress-content');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
throw new Exception('The encryption keys here are empty - head to https://api.wordpress.org/secret-key/1.1/salt/ to get some, and paste them in to wp-config.php, removing this exception');
define('AUTH_KEY',         '');
define('SECURE_AUTH_KEY',  '');
define('LOGGED_IN_KEY',    '');
define('NONCE_KEY',        '');
define('AUTH_SALT',        '');
define('SECURE_AUTH_SALT', '');
define('LOGGED_IN_SALT',   '');
define('NONCE_SALT',       '');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

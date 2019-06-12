<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', 'usbw');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         '%;4]*pf|Y/`iqE#l:64t3VkB7ZZGoyO0)mU{wTCc?kWu$6R^qJ$}1G%@-$`W;06s');
define('SECURE_AUTH_KEY',  ']t!}3jjlT/du?.;<TP=k6x!j`k$/;Q)NUAIe]p}$h9<PcYO;/yA2;[4jgcr~JM{%');
define('LOGGED_IN_KEY',    'NAsV!< rvbxrcPJ8E+ZlounY)27*~fa`pxozw=Z.. 8SJdZ>?tyZG[g&Yc#*$Z}L');
define('NONCE_KEY',        'qKlP_,Gt S|ydlerhh([M[wks>m#;p+2kgryNngVYW-KbZ`p<exhK),^}h%xEYl*');
define('AUTH_SALT',        'evQ7.m97K.VKG Qg_p)@~Jn1#KE`w_98UG~#u3o<z~MpmcVe*gJdrH9TUv*yPb$2');
define('SECURE_AUTH_SALT', '#;+mt;[Z($n].9saf;@!+.U-+f7.8n7KTv^pS.N|mKi_TtMQrkr9u7+koIk)Q~_?');
define('LOGGED_IN_SALT',   '$n-808G&Or|F4oH3[&3|7v!N{n(|e SPfW;FPcn}d9Ld>81t80D*HW#9w&koqmT ');
define('NONCE_SALT',       'L/o08OxDl38XZ5oROTM_GFTV`>i68w2%PTc7voN_>o`EkQ_YRr}Hedl_0xUZQS~-');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');

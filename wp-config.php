<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'sbrvhcom_DEV' );

/** Database username */
define( 'DB_USER', 'sbrvhcom_DEV' );

/** Database password */
define( 'DB_PASSWORD', '{Z;%_l+Dvdcgy0Gm' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'pe+SC-pWx<z;/x)R6W-jk(aF+MybwYTF#G`XaiOun)-$* Nu}c+VmBEq,B^Q5EB4');
define('SECURE_AUTH_KEY',  '8+Wt!8G|zjXB2|FUFMoru%/1xlY)$:*g +lJ[;N}3q9#ayQ0d-+:V4ZY$OchoHg?');
define('LOGGED_IN_KEY',    '71u`)t!Rm75-2hi.-i=/ifa;$a]o_k+]1.a gA08?F+[|*4RVZjQ!1lR}zbxaF/|');
define('NONCE_KEY',        '?bKS>+mz&qD3lzR@~uxG{)WjV^yI-$d0)+Dvr#SS6G!l|PxFa{Nlu_QQ&;xiOaf`');
define('AUTH_SALT',        'anM_g[cU9qP-u9+hA8nSw2-5]mn3Gmb}u^Emw>_x|dLS/9 -ABLI| UxqkTU`PP ');
define('SECURE_AUTH_SALT', 'r1 K[9i0,.+m!P==[hbZ?z&li*[f)@WM;S5evD+hT|9ck[}$&[NEmL0lBL4a^@Dy');
define('LOGGED_IN_SALT',   'X`hp:_q>bNJG|A-H,bsFmK4/6RvqC)Ge{Zs)>bR $**>g~82T}=3y1;mT*P%5O+~');
define('NONCE_SALT',       'llzx3K=ro):Gj8d|;SYp-iJgI;9E,f=A.-^+9z_x]?N?u9cMCfM3IKR#Spz;3Z_P');

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'sb_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';

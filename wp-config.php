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
define( 'DB_NAME', 'sbrvh' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'g#nuZEYh6f~D_tt:H!%$+J[J=Py*i,kzk#|#;58DfXxj<YzJ/E6!ms,GO.:JhXOd' );
define( 'SECURE_AUTH_KEY',  '-&.q{h^WUnJG>j$QQm[j2C[NL4l=AuWwNz=[/6-&^%2JA#jaBGl *(Qxsw*a-Z>x' );
define( 'LOGGED_IN_KEY',    '5huG?B?MTDmB&][}NOP8vig1@ $@YH(a!o|v$wgOXJ1V6e(Hk!&iP-twe}c=Sa6 ' );
define( 'NONCE_KEY',        '/kcq*=G/,T/kl3nhsolbG~e.0h]A(bc:D&:.^3q`n(+>3(o;&C7Jx2>etr-n]K=#' );
define( 'AUTH_SALT',        '<q8N_.-=!%7lK:iB{S=YY=p2E45JV=fq8*GwXC@/gLK2Z)!pl)JUzR<bwlj!#S.c' );
define( 'SECURE_AUTH_SALT', '(:tVa#o0CD=Tj!#bdsZa:uh[HYSC^FC-K#<^cNII* (|)Z!MC[FF{x,],$rn>Y~,' );
define( 'LOGGED_IN_SALT',   'PCy,R~ENMzGw.pDbElymE5>({zmj7>dZU1M,@cZ#e{v)uBVIK$2_M:#&Xwh(,45J' );
define( 'NONCE_SALT',       'n7w%]P:} HbhS)+DK2:X;CG,ogF/KP$FpSkc 9mHjzf6a]~,O+n^32@sG9$3beWp' );

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
$table_prefix = 'wp_';

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

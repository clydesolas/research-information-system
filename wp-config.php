<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'cvsuris' );

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
define( 'AUTH_KEY',         'LfRKa@,=W9R?qw/hX]O8l&evY)u&s@uk<eaCyw@B=w#VjF0VVHv3A =lrP}C3W0Y' );
define( 'SECURE_AUTH_KEY',  '|lMV>=0ROXi;3-+*GoWAQjwup]_$%q|,o7HK:eA3Zzs`;w^uCtJdYK,aiJGXgd(>' );
define( 'LOGGED_IN_KEY',    'cO^9RubO[*U)dmFa={6% }KPY9Zw9+w}8$o#zT(&DL2&BoJMfd-Ph&U>J=a y>p1' );
define( 'NONCE_KEY',        '^n:w5Qegiu^B0LgLGP&e?F2~ O+3iU3&e(89%f}sY7E*g>AB^HbpMAYI: q2;8Kf' );
define( 'AUTH_SALT',        ',$[K z9*yc=|^zo`!kaQXbdUGpm m9i@-]{g9W`aQ9H+.Y@P/xpJ=gz$yt6;]-2>' );
define( 'SECURE_AUTH_SALT', '#.[jXtdRZk~H[vOca-!9%b26~wia[waL{sw_?H]Q4O^(K!,n!;=Y;d1g7,G74TmH' );
define( 'LOGGED_IN_SALT',   '/b|#6)ShtD_2/]JXpmzUWDHF$?_ejMdG_Inw>j^bqE<DzPATbSd?Oq~D%yz/8|yW' );
define( 'NONCE_SALT',       '.l%i,OdQqTyq0wlxV{#!w6x gPDV?3,eIsS5]/ZrrhI{Ac=NzJPS4k<PgE{qPxs/' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
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

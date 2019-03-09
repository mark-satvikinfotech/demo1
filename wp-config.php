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

 error_reporting(E_ALL);
ini_set('display_errors', 1);

// ** MySQL settings - You can get this info from your web host ** //
define('WP_MEMORY_LIMIT', '512M');

/** The name of the database for WordPress */

define('DB_NAME', 'satvikso_demo');



/** MySQL database username */

define('DB_USER', 'satvikso_demo');



/** MySQL database password */

define('DB_PASSWORD', 's!ZtJOZ%.e5K');



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

define('AUTH_KEY',         'YYWv3BV=QkO 0Bmn<T;ZXoB24Vc=N2u8?_GB.w%lJ?KwLQ,s_RT0hxL<s]F~{8#g');

define('SECURE_AUTH_KEY',  '-)cgJGE2>NGwpk!~,IR^bNeEkSF*7B?)`,XcV6sgjj/O(x-1h*Y7:alG*~*<)cY0');

define('LOGGED_IN_KEY',    'u}J$<]oR{^tZDAE;S8U0s5R=~-yU,I(T;CC%N9FQzc5*[LdNB)1/tCl`xBVV.|7(');

define('NONCE_KEY',        'K9 R-#Bn&#mH}B.}@^8,&3IlXPM0f1h7B6z88RZ63:q`CXnW31D:8gwy3XvFG;],');

define('AUTH_SALT',        '60u&viKWr/%cIJ1W lUbPs8%Z6^,=4_ T1@v=W^Fxg}ofae13/Ui*2AO}}vShz;e');

define('SECURE_AUTH_SALT', '>h**.Viwu+!SP%AeI 4z:81hE|Z8m?adCje!_VvQ[.HSZ>y3Di]BU8rdGBoT}yG&');

define('LOGGED_IN_SALT',   '&%Q(ejJp$<Ec[*1-=0&M=ZY0oF6<q(abcMC>JM!H rPzUUwnDG0uP{,R>`}[IX_Y');

define('NONCE_SALT',       'l-t@YHo<v]s!d;rSc]Fam?>C A8ra-6D?Zb3ISH7%;ie[;EV(]w-V! ^[O`6[? l');



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

define('WP_DEBUG', true);



/* That's all, stop editing! Happy blogging. */



/** Absolute path to the WordPress directory. */

if ( !defined('ABSPATH') )

	define('ABSPATH', dirname(__FILE__) . '/');



/** Sets up WordPress vars and included files. */

require_once(ABSPATH . 'wp-settings.php');


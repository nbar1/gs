<?php
/**
 * Config entries for various individual installation variables
 */

define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'nick');
define('DB_HOST', 'localhost');
define('DB_NAME', 'nbar1_gs');

define('GSAPI_KEY', 'nbar1');
define('GSAPI_SECRET', '1f64634987618265edb26fe236c00011');

define('TINYSONGAPI_KEY', 'a2e5ffd9cc99a2bee6207e4921def6a7');

define('GROOVESHARK_USERNAME', 'nbarone');
define('GROOVESHARK_PASSWORD', '17543366');

define('GS_AUTOPLAY', TRUE);
define('GS_PROMOTION_MAX', 3);
define('GS_PROMOTION_TIMELIMIT', 120);

// Registration
define('USER_CREATED', 1001);
define('USER_CREATE_FAILED', 1002);
define('USER_ALREADY_EXISTS', 1003);
define('USERNAME_REQUIRED', 1101);
define('PASSWORD_REQUIRED', 1102);
define('USERNAME_TOO_LONG', 1103);

// Login
define('LOGIN_SUCCESS', 2001);
define('LOGIN_FAILED', 2002);
define('BAD_PASSWORD', 2003);

// User
define('USER_EXISTS', 2101);
define('USER_NOT_FOUND', 2102);

// Queue
define('QUEUE_SUCCESS', 3001);
define('QUEUE_FAILED', 3002);
define('QUEUE_EMPTY', 3003);

// Song
define('SONG_ADDED', 4001);
define('SONG_ADD_FAILED', 4002);
define('SONG_ALREADY_QUEUED', 4003);
define('SONG_NOT_FOUND', 4004);

define('SONG_STORED', 4101);
define('SONG_STORE_FAILED', 4202);
?>
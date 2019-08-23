<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| CUSTOM CONSTANTS
|--------------------------------------------------------------------------
|
| These are constants defined specially for this application.
|
*/

/*
|--------------------------------------------------------------------------
| Authentication system constants
|--------------------------------------------------------------------------
*/
define('ACCESS_LVL_USER', 1);
define('ACCESS_LVL_MANAGER', 2);
define('ACCESS_LVL_ADMIN', 4);

define('USERNAME_MIN_LENGTH', 3);
define('USERNAME_MAX_LENGTH', 45);
define('PASSWORD_MIN_LENGTH', 6);
define('PASSWORD_MAX_LENGTH', 72);
define('TOPIC_MAX_LENGTH', 150);

define('PASSWORD_HASH_ALGORITHM', PASSWORD_BCRYPT);


define('ITEMS_PER_PAGE',25);

define('PDF_HEADER_COLOR', [
    'red' => 0,
    'green' => 0,
    'blue' => 0
]);
define('PDF_FOOTER_COLOR', [
    'red' => 0,
    'green' => 0,
    'blue' => 0
]);

define('GITHUB_USERNAME', 'orif-support');
define('GITHUB_PASSWORD', '8iw83FDewVA6');


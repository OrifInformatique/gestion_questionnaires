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
define('ACCESS_LVL_GUEST', 1);
define('ACCESS_LVL_OBSERVATION', 2);
define('ACCESS_LVL_FORMATION', 4);
define('ACCESS_LVL_MSP', 8);
define('ACCESS_LVL_ADMIN', 16);

define('USERNAME_MIN_LENGTH', 3);
define('USERNAME_MAX_LENGTH', 45);
define('PASSWORD_MIN_LENGTH', 6);
define('PASSWORD_MAX_LENGTH', 72);

define('PASSWORD_HASH_ALGORITHM', PASSWORD_BCRYPT);

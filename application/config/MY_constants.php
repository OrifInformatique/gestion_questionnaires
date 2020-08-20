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
define('TOPIC_MAX_LENGTH', 150);

// ITEMS_PER_PAGE replaced by $items_per_page in controller Question.php

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

// Define nb of questions displayed, will take the array's first value as default value
define('QUESTIONS_NB', array(
    25,
    50,
    100
    )
);

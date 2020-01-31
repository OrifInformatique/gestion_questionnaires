<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$autoload = [];
$autoload['language'] = array('MY_auth');
$autoload['config'] = array('MY_auth_config');


if (ENVIRONMENT !== 'testing') {
	$autoload['language'] = array('MY_auth');
	$autoload['config'] = array('MY_auth_config');
} else {
	// CI-PHPUnit checks from application/folder instead of module/folder
	$autoload['language'] = ['../../modules/auth/language/french/MY_auth'];
	$autoload['config'] = array('../modules/auth/config/MY_auth_config');
}

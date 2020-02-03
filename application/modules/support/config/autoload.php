<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if (ENVIRONMENT !== 'testing') {
	$autoload['language'] = ['MY_support'];
	$autoload['config'] = array('MY_support_config', 'user/MY_user_config');
} else {
	// CI-PHPUnit checks from application/folder instead of module/folder
	$autoload['language'] = ['../../modules/support/language/french/MY_support'];
	$autoload['config'] = array(
		'../modules/support/config/MY_support_config',
		'../modules/user/config/MY_user_config'
	);
}

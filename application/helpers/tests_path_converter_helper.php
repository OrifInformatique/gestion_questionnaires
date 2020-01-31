<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('to_test_path'))
{
	function to_test_path($path)
	{
		if (ENVIRONMENT !== 'testing') {
			return $path;
		} else {
			$parts = explode('/', $path);
			if(count($parts) == 2){
				return '../../modules/'.$parts[0].'/config/'.$parts[1];
			}
		}
	}
}
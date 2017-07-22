<?php
/*
Plugin Name: Wordpress Dash for Developers
Plugin URI:  https://github.com/alesanchezr/wpas-wordpress-dash
Description: A WordPress Plugin for Developers: Easy AJAX, Easy Admin Notifications, Extend VC Composer, etc.
Version:     1.0.0
Author:      Alejandro Sanchez
Author URI:  https://alesanchezr.com/
License:     MIT
License URI: https://en.wikipedia.org/wiki/MIT_License
Text Domain: wpas
Domain Path: /languages
*/

require 'src/autoload.php';

if(!defined('WPAS_ABS_PATH')) define('WPAS_ABS_PATH', plugin_dir_url( __FILE__ ));
if(!defined('WPAS_DOMAIN')) define('WPAS_DOMAIN', 'default');
$test = new \WPAS\Controller\WPASController();
function is_wpas_plugin_active(){ return true; }
<?php
/*
Plugin Name: WPAS WP Route Controller
Plugin URI:  https://github.com/alesanchezr/wpas-rout-controller
Description: A WordPress Plugin for Developers: Controller for easier AJAX Requests and data preloading for any view. 
Version:     1.0.0
Author:      Alejandro Sanchez
Author URI:  https://alesanchezr.com/
License:     MIT
License URI: https://en.wikipedia.org/wiki/MIT_License
Text Domain: wpas
Domain Path: /languages
*/

require 'src/autoload.php';
$test = new \WPAS\Controller\WPASController();
function wpas_is_plugin_active(){}
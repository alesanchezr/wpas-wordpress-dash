<?php

//Declare global variables
if(!defined('ABSPATH')) define('ABSPATH', dirname(__FILE__) . '/');
if(!defined('PUBLICPATH')) define('PUBLICPATH', get_site_url());
require ABSPATH . 'vendor/autoload.php';

/**
 * Activate the command line tool
 */
if ( class_exists( 'WP_CLI' ) ) { $i = new \WPAS\CLI\CLIManager(); }



/**
 * Import the Controller
 */
use \WPAS\Controller\WPASController;
$controller = new WPASController([
        //Here you specify the path to your consollers folder
        'namespace' => 'php\\Controller\\'
    ]);
$controller->route([ 'slug' => 'Single:post', 'controller' => 'LessonController' ]);  



/**
 * Import the CustomPostTypes
 */
use \WPAS\Types\PostTypesManager;
$postTypeManager = new PostTypesManager([
    'namespace' => '\php\Types\\'
]);
//You can create a class for an already created post
$postTypeManager->newType(['type' => 'post', 'class' => 'PostPostType'])->register();
<?php
//Define autoloader 
spl_autoload_register('autoloadWPASController');

function autoloadWPASController($controller)
{
    $basePath = 'wpas/controller/';
    $ce = explode('\\', $controller);
    $className = end($ce);
    if (in_array('Controller',$ce)) require($basePath.$className.'.controller.php');
}
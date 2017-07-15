<?php
//Define autoloader 
spl_autoload_register('autoloadWPASBCControllers');

function autoloadWPASBCControllers($controller)
{
    $ce = explode('\\', $controller);
    $className = end($ce);
    if (in_array('Controller',$ce)) require($className.'.controller.php');
}
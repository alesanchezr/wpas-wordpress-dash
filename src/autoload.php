<?php
//Define autoloader 
spl_autoload_register('autoloadWPAS');

function autoloadWPAS($controller)
{
    $ce = explode('\\', $controller);
    $totalFolders = count($ce);

    if($totalFolders==3)
    {
        $className = end($ce);
        $extension = strtolower($ce[$totalFolders-2]);
        unset($ce[$totalFolders-1]);
        $basePath = strtolower(implode('/',$ce) . "/");
        if (in_array('WPAS',$ce)) 
        {
            //echo $basePath.$className.'.'.$extension.'.php'; die();
            require($basePath.$className.'.'.$extension.'.php');
        }
    }
}
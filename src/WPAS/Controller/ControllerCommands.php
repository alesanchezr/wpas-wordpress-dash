<?php

namespace WPAS\Controller;

use \WP_CLI;

class ControllerCommands {
    
    const CONTROLLERS_PATH = '/src/php/Controller/';   

    public static function generate($controllerName)
    {
        $themeDirectory = get_stylesheet_directory().self::CONTROLLERS_PATH;

        if(!is_dir($themeDirectory)) $this->createControllerFolder($themeDirectory);
        WP_CLI::line( "Generating controller file: ".$controllerName.'.php' );
        $myfile = fopen($themeDirectory.$controllerName.".php", "w") or WP_CLI::Error( "Unable to open file!");
        $txt ='<?php
        
namespace php\\\Controller;

use WP_Query;
        
class '.$controllerName.'{

    /**
    * This is a sample method, the idea is to fill the $args array
    * with all the data you need to have available in your view
    **/
    function render<slug>(){
        
        //this array will contain all your data
        $args = [];
        
        //any logic here
        
        return $args;
    }
    
}';
        $data=stripslashes($txt);
        fwrite($myfile, $data);
        fclose($myfile);
        
        WP_CLI::Success( "Controller '".$controllerName."' was created." );
    }
    
    function createControllerFolder($themeDirectory){
        WP_CLI::line( "Generating controller folder in ".$themeDirectory );
        if (!mkdir($themeDirectory, 0777, true)) {
            WP_CLI::Error( "Fail to create the directory structure, check the directory permisions" );
        }
    }
}
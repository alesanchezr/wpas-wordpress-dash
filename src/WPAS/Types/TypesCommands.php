<?php

namespace WPAS\Types;

use \WP_CLI;

class TypesCommands {
    
    const TYPES_PATH = '/src/php/Types/';   

    public static function generate($typeName)
    {
        $themeDirectory = get_stylesheet_directory().self::TYPES_PATH;

        if(!is_dir($themeDirectory)) self::createTypesFolder($themeDirectory);
        WP_CLI::line( "Generating types file: ".$typeName.'.php' );
        $myfile = fopen($themeDirectory.$typeName.".php", "w") or WP_CLI::Error( "Unable to open file!");
        $txt ='<?php
        
namespace php\\\Types;

use \\\WPAS\\\Types\\\BasePostType;
use WP_Query;
        
class '.$typeName.' extends BasePostType{

    public function __construct(){
        
        /**
         * your action hooks related to this particular 
         * PostType go here, for example:
         */
        add_action(\'init\', [$this, \'function_name_hook\']);
        
    }
    
    public function function_name_hook(){
        //this function will be executed on the \'init\' WordPress hook
    }

    
}';
        $data=stripslashes($txt);
        fwrite($myfile, $data);
        fclose($myfile);
        
        WP_CLI::Success( "PostType '".$typeName."' file was created." );
    }
    
    static function createTypesFolder($themeDirectory){
        WP_CLI::line( "Generating controller folder in ".$themeDirectory );
        if (!mkdir($themeDirectory, 0777, true)) {
            WP_CLI::Error( "Fail to create the directory structure, check the directory permisions" );
        }
    }
}
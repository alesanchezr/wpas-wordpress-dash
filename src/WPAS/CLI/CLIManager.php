<?php

namespace WPAS\CLI;

use WPAS\Controller\ControllerCommands;
use WPAS\Types\TypesCommands;

/**
 * Implements all the commands for WordPress Dash
 */
class CLIManager {
    
    var $actions = [];
    
    function __construct(){
        $this->loadActions();
        foreach($this->actions as $command => $callback){
            //echo $command; die();
            \WP_CLI::add_command( 'dash-'.$command, $callback );
        } 
        
    }
    
    function loadActions(){
        $this->actions["generate"] = function($args){
            if (strpos(strtolower($args[0]), 'controller') !== false) {
                ControllerCommands::generate($args[0]);
            }
            elseif (strpos(strtolower($args[0]), 'posttype') !== false) {
                TypesCommands::generate($args[0]);
            }
        };
    }
    

    /*
    private function generate($args)
    {
        \WP_CLI::Line('Generating files');
        \WP_CLI::Success( "You decided to: ".$args[1] );
    }
    */
}
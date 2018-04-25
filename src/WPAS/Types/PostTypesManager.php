<?php

namespace WPAS\Types;

use PostTypes\PostType;
use WPAS\Utils\WPASException;

class PostTypesManager{
    
    private $customTypes = [];
    private $options = [];
    
    function __construct($options){
        
        $this->options = [
            'namespace' => '\\',
            'rest-support' => true,
            ];
        $this->loadOptions($options);
        
        /**
        * Add REST API support to an already registered post type.
        */
        if($this->options['rest-support']) add_action( 'init', [$this,'addRestSupport'], 25 );
    }
    
    private function loadOptions($options){
        foreach($this->options as $key => $val) 
            if(isset($options[$key])) 
                $this->options[$key] = $options[$key];
                
    }
    
    public function newType($typeDetails){
        
        if(empty($typeDetails['type'])) throw new WPASException('You need to specify the type of your Custom Post');
        if(empty($typeDetails['class'])) throw new WPASException('You need to specify the class path for your Custom Post');
        
        $options = null;
        if(!empty($typeDetails['options'])) $options = $typeDetails['options'];
        
        $classPath = $this->options['namespace'].$typeDetails['class'];
        
        $newType = $this->createInstance($typeDetails['type'], $classPath, $options);
        $this->customTypes[] = $newType;
        return $newType;
    }
    
    private function createInstance($type, $classPath, $options){
        
        if(!class_exists($classPath)) throw new WPASException('Your class '.$classPath.' cour not be found, check your autoload?');
        if(!is_subclass_of($classPath, 'WPAS\Types\BasePostType')) throw new WPASException('Your class '.$classPath.' has to inherit from \WPAS\Types\BasePostType');
        
        $pt = null;
        if(is_array($options)) $pt = new $classPath($type, $options);
        else $pt = new $classPath($type);
        $classPath::setType($type);
        return $pt;
    }
    
    function addRestSupport() {
        global $wp_post_types;
        
        //be sure to set this to the name of your post type!
        foreach($this->customTypes as $type){
            if( isset( $wp_post_types[ $type->name ] ) ) {
                $wp_post_types[$type->name]->show_in_rest = true;
                $wp_post_types[$type->name]->rest_base = $type->name;
                $wp_post_types[$type->name]->rest_controller_class = 'WP_REST_Posts_Controller';
            }
        }
    
    }
    
}

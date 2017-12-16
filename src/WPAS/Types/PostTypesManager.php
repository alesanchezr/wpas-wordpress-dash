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
            ];
        $this->loadOptions($options);
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
        
        if(!is_subclass_of($classPath, 'WPAS\Types\BasePostType')) throw new WPASException('Your class '.$classPath.' has to inherit from \WPAS\Types\BasePostType');
        
        $pt = null;
        if($options) $pt = new $classPath($type, $options);
        else $pt = new $classPath($type);
        return $pt;
    }
    
}
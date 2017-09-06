<?php

namespace WPAS\Types;

use PostTypes\PostType;
use WPAS\Utils\WPASException;

class PostTypesManager{
    
    private $customTypes = [];
    
    function __construct($customTypes){
        
        if(empty($customTypes)) throw new WPASException('You have to specify an array of the types your site will have');
        $this->createPostsTypes($customTypes);
    }
    
    private function createPostsTypes($customTypes){
        foreach($customTypes as $type){
            $classPath = $this->getClassPath($type);
            if(is_array($classPath)) $this->customTypes[] = $this->createInstance($classPath);
            else $this->customTypes[] = new BasePostType($classPath);
        }
    }
    
    private function createInstance($classPath){
        
        $pt = new $classPath[1]($classPath[0]);
        if(!is_subclass_of($classPath[1], 'WPAS\Types\BasePostType')) throw new WPASException('Your class '.$classPath[1].' has to inherit from \WPAS\Types\BasePostType');
/*
        if(is_callable([$v,'render'.$view])){
            self::$args = call_user_func([$v,'render'.$view]);
            if(is_null(self::$args) && WP_DEBUG) echo '<p style="margin-top:50px;margin-bottom:0px;" class="alert alert-warning">Warning: the render method is returning null!</p>';
        }
        else throw new WPASException('Render method for view '.$view.' does not exists in '.$controller);*/
        
        return $pt;
    }
    
    private function getClassPath($path){
        $pieces = explode(':',$path);
        if(count($pieces)==1) return $pieces[0];
        else if(count($pieces)==2) return [$pieces[0],$pieces[1]];
        else throw new WPASException('Invalid custom post type: '.$path);
    }
    
}
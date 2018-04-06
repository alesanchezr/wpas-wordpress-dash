<?php

namespace WPAS\Types;

use PostTypes\PostType;
use WPAS\Utils\WPASException;
use \WP_Query;

class BasePostType extends PostType{

    protected static $postType = '';

    function __construct(){
        $args = func_get_args();
        
        self::$postType = strtolower($args[0]);
        
        call_user_func_array(array('parent', '__construct'), $args);
        
        $this->populate_fields();
        $this->initialize();
    }
    
    public function initialize(){}
    public function populate_fields(){}
    
    public static function setType($theType){
        self::$postType = $theType;
    }
    
    public static function all($args=[], $hook=null){
        $realPostType = get_called_class();
        $realPostType = strtolower(preg_replace( "%[A-Za-z]\w+\\\%", '',$realPostType));
        
        if(empty(self::$postType)) throw new WPASException('Please register() the class '.get_called_class().' at least one time before using it');
        $args = array_merge([
            'post_type' => $realPostType
            ],$args);
        
        $query = new WP_Query($args);
        return $query;
    }
    
    public static function get($args){
        $realPostType = get_called_class();
        $realPostType = strtolower(preg_replace( "%[A-Za-z]\w+\\\%", '',$realPostType));
        
        if(empty(self::$postType)) throw new WPASException('Please instanciate the class '.get_called_class().' at least one time before using it');
        if(is_array($args)) $args = array_merge($args,[ 'post_type' => $realPostType ]);
        else if(is_numeric($args)) $args = [ 'post_type' => $realPostType, 'ID' => $args ];
        
        $query = new WP_Query($args);
        if($query->posts && $query->post_count >=1){
            return $query->posts[0];
        }else return null;
    }
    
    public static function getBySlug($slug){
        $realPostType = get_called_class();
        $realPostType = strtolower(preg_replace( "%[A-Za-z]\w+\\\%", '',$realPostType));
        
        if(empty(self::$postType)) throw new WPASException('Please instanciete the class '.get_called_class().' at least one time before using it');
        if(!is_string($slug)) throw new WPASException('getBySlug must receive a string as parameter $slug');
        
        $args = [ 'post_type' => $realPostType, 'name' => $slug ];
        $query = new WP_Query($args);
        if($query->posts && $query->post_count >=1){
            return $query->posts[0];
        }else return null;
    }
    
}

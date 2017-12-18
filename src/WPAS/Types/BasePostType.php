<?php

namespace WPAS\Types;

use PostTypes\PostType;
use \WP_Query;

class BasePostType extends PostType{

    protected static $postType = '';

    function __construct(){
        $args = func_get_args();
        
        self::$postType = $args[0];
        
        call_user_func_array(array('parent', '__construct'), $args);
        
        $this->initialize();
    }
    
    public function initialize(){}
    
    public static function all($args=[], $hook=null){
        
        $args = array_merge($args,[
            'post_type' => self::$postType
            ]);
        
        $query = new WP_Query($args);
        return $query;
    }
    
    public static function get($args){
        
        if(is_array($args)) $args = array_merge($args,[ 'post_type' => self::$postType ]);
        else if(is_numeric($args)) $args = [ 'post_type' => self::$postType, 'ID' => $args ];
        
        $query = new WP_Query($args);
        if($query->posts && $query->post_count >=1){
            return $query->posts[0];
        }else return null;
    }
    
}

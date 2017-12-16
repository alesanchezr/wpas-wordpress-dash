<?php

namespace WPAS\Types;

use PostTypes\PostType;

class BasePostType extends PostType{

    function __construct(){
        $args = func_get_args();
        //print_r($args); die();
        
        call_user_func_array(array('parent', '__construct'), $args);
        
        $this->initialize();
    }
    
    public function initialize(){}
    
}
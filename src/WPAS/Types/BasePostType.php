<?php

namespace WPAS\Types;

use PostTypes\PostType;

class BasePostType extends PostType implements IPostType{

    function __construct(){
        $args = func_get_args();
        call_user_func_array(array('parent', '__construct'), $args);

        $this->populate_fields();
    }
    
    public function populate_fields(){}
    
}
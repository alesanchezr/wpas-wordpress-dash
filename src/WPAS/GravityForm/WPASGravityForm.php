<?php

namespace WPAS\GravityForm;

use GFForms;

class WPASGravityForm{
    
    function __construct(){

        GFForms::include_addon_framework();
        $customSubmit = new CustomSubmitButton();
        //add_action('gform_loaded', [$this,'add_custom_submit'], 5);
    }
    
    /*
    function add_custom_submit(){ 
        $customSubmit = new CustomSubmitButton();
    }
    */
    
}
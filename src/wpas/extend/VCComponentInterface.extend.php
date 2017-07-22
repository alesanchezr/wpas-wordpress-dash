<?php

namespace WPAS\Extend;

interface VCComponentInterface{
    
    const BASE_NAME = '';
    
    function initialize();
    
    function register();
    
	//function render( $atts , $content = null);
}
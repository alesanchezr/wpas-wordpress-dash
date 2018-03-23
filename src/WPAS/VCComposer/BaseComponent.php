<?php

namespace WPAS\VCComponent;

use WPAS\Utils\WPASException;

class BaseComponent{
    
    protected $baseName = null;
    
    function __construct($baseName){
        add_action( 'vc_before_init', array($this,'register'));
        add_shortcode( $baseName, array($this,'render'));
    }
    
    function register(){
        throw new WPASException('The component '.$baseName.' needs a register function');
    }
    function render($atts , $content = null){
        throw new WPASException('The component '.$baseName.' needs a render function');
    }
}
<?php

namespace WPAS\Messaging;

class WPASEmail{
    
    
    
    function __construct(){
        $loader = new Twig_Loader_Filesystem('/wp-content/themes/thedocs-child/src/view/emails');
        $twig = new Twig_Environment($loader, array(
            'cache' => '/wp-content/themes/thedocs-child/.twig_cache',
        ));

    }
}
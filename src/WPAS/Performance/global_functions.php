<?php

require_once('global_functions.php');

function wpas_get_inline_svg($scope, $icon){
    get_template_part($scope, $icon);
}

function wpas_head(){
    do_action('wpas_print_styles');
}

function wpas_footer(){
    do_action('wpas_print_footer_scripts');
}


function wpas_critical_head(){
    do_action('wpas_print_critical_styles');
}
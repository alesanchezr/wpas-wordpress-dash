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

function wpas_load_styles($styles){
    \WPAS\Performance\WPASStylesManager::setStyles($styles);
}
function wpas_filter_manifest($url){
    return WPAS\Performance\WPASAsyncLoader::filter_manifest($url);
}

function wpas_minify_js($data){
    $minifier = new \MatthiasMullie\Minify\CSS($data);
    return $minifier->minify();
}

function wpas_minify_html($data){
    $minifier = new \MatthiasMullie\Minify\HTML($data);
    return $minifier->minify();
}

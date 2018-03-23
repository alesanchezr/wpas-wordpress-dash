<?php

function debug($var){
    print_r($var); die();
}
function wpas_get_view_data(){
    return WPAS\Controller\WPASController::getViewData();
}

function wpas_get_global_context(){
    return WPAS\Controller\WPASController::get_context();
}
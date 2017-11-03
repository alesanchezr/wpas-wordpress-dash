<?php

require_once('global_functions.php');

function wpas_get_theme_setting($key){
    return WPAS\Settings\WPASThemeSettingsBuilder::getThemeOption($key);
}
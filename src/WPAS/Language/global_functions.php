<?php

/**
 * Get the slug translation of an original slug
 **/
function wpas_pll_get_slug($slugIndex){
    return WPAS\Language\WPASLanguages::getSlug($slugIndex);
}
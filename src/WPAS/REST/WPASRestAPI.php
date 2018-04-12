<?php

namespace WPAS\REST;

use WPAS\Utils\WPASException;

class WPASRestAPI{
    
    private $options = [];
    private $endpoints = [];
    
    function __construct($options){

        $this->options = [
            'extended_posts' => [],
            'extended_taxonomies' => []
        ];
        
        if(isset($options['extended_posts'])) $this->options['extended_posts'] = $options['extended_posts'];
        if(isset($options['extended_taxonomies'])) $this->options['extended_posts'] = $options['extended_taxonomies'];
            
		add_action( 'init', [$this,'initialize_wordpress'], 25 );
		add_action( 'rest_api_init', [$this,'initialize_api'] );
    }
    
    function initialize_wordpress(){
        
        if(!is_array($this->options['extended_posts'])) throw new WPASException('The extended_posts option must be an array of custom post types');
        if(!is_array($this->options['extended_taxonomies'])) throw new WPASException('The extended_taxonomies option must be an array of custom taxonomies');
        
        $this->extendTypesAndTaxonomies($this->options['extended_posts'], $this->options['extended_taxonomies']);
    }
    
    function initialize_api(){
        foreach($this->endpoints as $ep)
        {
            register_rest_route( 'breathecode/v1', $ep['path'], array(
    	        'methods' => $ep['request_type'],
        	    'callback' => $ep['callback']
        	  ) );
        }
    }
    function get($path, $callback){
        $this->addEndpoint('GET', $path, $callback);
    }
    function addEndpoint($requestType, $path, $callback){
    	  //register_rest_route( 'wp/v2', '/courses/(?P<id>\d+)', array(
    	  $this->endpoints[] = ['request_type' => $requestType, 'path' => $path, 'callback' => $callback];
    }
    
    function extendTypesAndTaxonomies($postTypes, $taxonomies){
		global $wp_post_types;
		foreach($postTypes as $ctype){
			if(isset( $wp_post_types[ $ctype ] ) ) {
			  $wp_post_types[$ctype]->show_in_rest = true;
			  $wp_post_types[$ctype]->rest_base = $ctype;
			  $wp_post_types[$ctype]->rest_controller_class = 'WP_REST_Posts_Controller';
			}
		}
		
	  	global $wp_taxonomies;
	    foreach($taxonomies as $tax){
    	  	if ( isset( $wp_taxonomies[ $tax ] ) ) {
    	  		$wp_taxonomies[ $tax ]->show_in_rest = true;
    	  		$wp_taxonomies[ $tax ]->rest_base = $tax;
    	  		$wp_taxonomies[ $tax ]->rest_controller_class = 'WP_REST_Terms_Controller';
    	  	}
	    }
    }
}
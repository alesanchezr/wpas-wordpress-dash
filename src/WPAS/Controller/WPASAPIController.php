<?php

namespace WPAS\Controller;

require_once('global_functions.php');

use WPAS\Utils\WPASException;
use WPAS\Utils\WPASLogger;

class WPASAPIController{
    
    private $routes = [];
    private $options = [];
    private $closures = [];
    
    public function __construct($options=[]){
        
        WPASLogger::getLogger('wpas_controller');
        
        $this->options = [
            'namespace' => '',
            'application_name' => null,
            'version' => null,
            'allow-origin' => '*',
            'allow-methods' => 'GET'
            ];
        $this->loadOptions($options);
        
        /**
         * Only allow GET requests
         */
        add_action( 'rest_api_init', function() {
            
        	remove_filter( 'rest_pre_serve_request', 'rest_send_cors_headers' );
        	add_filter( 'rest_pre_serve_request', function( $value ) {
        	    
        		header( 'Access-Control-Allow-Origin: '.$this->options['allow-origin']);
        		header( 'Access-Control-Allow-Methods: '.$this->options['allow-methods']);
        
        		return $value;
        		
        	});
        }, 15 );
        
        add_action( 'rest_api_init', [$this,'load']);
    }
    
    private function loadOptions($options){
        foreach($this->options as $key => $val) 
            if(isset($options[$key])) 
                $this->options[$key] = $options[$key];
                
        if(!isset($options['application_name'])) throw new WPASException('The WPAS_APIController expects an application_name option');
        if(!isset($options['version'])) throw new WPASException('The WPAS_APIController expects an version option');
    }
    
    private function is_closure($t) {
        return is_callable($t);
    }
    
    public function get($args){ $this->route($args, 'GET'); }
    public function post($args){ $this->route($args, 'POST'); }
    public function put($args){ $this->route($args, 'PUT'); }
    public function delete($args){ $this->route($args, 'DELETE'); }
    public function route($args, $method){
        
        if(!isset($args['path'])) throw new WPASException('You need to specify the path parameter for the endpoint');
        if(!isset($args['controller'])) throw new WPASException('You need to specify the controller parameter for the endpoint');
        $path = $args['path'];
        
        $controller = $args['controller'];
        $closureIndex = $controller;
        if($this->is_closure($controller)){

            $closureIndex = "closure_".spl_object_hash($controller);
            $this->closures[$closureIndex] = [
                'action' => $args['controller'],
                'closure' => $controller
            ];
        }
        $capability = '';
        if( isset( $args['capability'] ) && $args['capability'] ){
            $capability = $args['capability'];
        }
        
        $endpointArgs = '';
        if(isset( $args['args'] ) ){
            $endpointArgs = $args['args'];
        }
        
        if( !array_key_exists($path, $this->routes) ){
            
            $this->routes[$path] = [ 
                    [
                        'callback' => $closureIndex , 
                        'method' => $method, 
                        'permission_callback' => $capability,
                        'args' => $endpointArgs
                    ]
                ];
            
        }else{
            array_push($this->routes[$path], array('callback' => $closureIndex , 'method' => $method, 'permission_callback' => $capability, 'args' => $endpointArgs));
        }
    }
    
    public function load(){

        WPASLogger::info('WPAS_APIController: INIT');
        foreach($this->routes as $path => $listOfMethods){
            foreach($listOfMethods as $params){
            
                $controller = $params['callback'];
                $httpMethod = $params['method'];
                $capability = $params['permission_callback'];
                $args = $params['args'];
                
                $controllerObject = $this->getController($controller);
                $className = $controllerObject;
                $methodName = '';
                if(is_array($className))
                {
                    $methodName = $controllerObject[1]; //The view
                    $className = $controllerObject[0]; //The type of the view
                }else{
                    if(strpos($controller, 'closure_') === false) throw new WPASException('You need to specify the controller and class method that will handle the API request');
                } 
                
                $initArray = array('methods' => $httpMethod);
                
                if(isset($this->closures[$controller])){
                    
                    $initArray['callback'] = $this->closures[$controller]['closure'];
                    
                }else{

                    WPASLogger::info('WPAS_APIController: match found for '.$httpMethod.': '.$path.', controller => '.$controller.' ] calling: '.$methodName);
                    $controller = $this->options['namespace'].$className;
                    
                    $v = new $controller();
                    
                    if(is_callable([$v,$methodName])){
                        $initArray['callback'] = [$v,$methodName];
                    }
                    else throw new WPASException('Method "'.$methodName.'" for api path "'.$path.'" does not exists in '.$className);
                }
                
                if( !empty($capability)){
                    $initArray['permission_callback'] = function ($request) use ($capability) {
                        if (current_user_can($capability))
                        return true;
                    };
                }
                else{
                    $initArray['permission_callback'] = function ($request) use ($capability) {
                        return true;
                    };
                }
                
                if( !empty($args) ){
                    $initArray['args'] = $args;
                }
                
                register_rest_route( $this->options['application_name'].'/v'.$this->options['version'], $path, $initArray);
            }
        }
    }
    
    private function getController($controller){
        $pieces = explode(':',$controller);
        if(count($pieces)==1) return $pieces[0];
        else if(count($pieces)==2) return [$pieces[0],$pieces[1]];
        else throw new WPASException('The controller '.$controller.' is invalid');
    }
    
}

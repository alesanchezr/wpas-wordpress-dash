<?php

namespace WPAS\Controller;

use WPAS\Exception\WPASException;

class WPASController{
    
    private static $ajaxRouts = [];
    private static $routes = [];
    protected static $args = [];
    
    public function __construct(){
        add_action('template_redirect', [$this,'load']);
        add_action( 'wp_enqueue_scripts', [$this,'loadScripts'] );
    }
    
    public static function route($args){
        
        $view = $args['slug'];
        $controller = $args['controller'];
        
        self::$routes[$view] = $controller;
    }
    
    public static function routeAjax($args){
        
        if(!is_array($args)) throw new WPASException('routeAjax is expecting an array');
        if(!isset($args['slug']) || !isset($args['controller']) || !isset($args['ajax_action'])){
            print_r($args); die();
            throw new WPASException('routeAjax args must be view,controller and ajax_action');
        } 
        
        $view = $args['slug'];
        $controller = $args['controller'];
        $action = $args['ajax_action'];
        
        if(!isset(self::$ajaxRouts[$view])) self::$ajaxRouts[$view] = [];
        self::$ajaxRouts[$view][$controller] = $action;
    }
    
    public static function loadAjax(){
        
        foreach(self::$ajaxRouts as $view => $routes){
            foreach($routes as $controller => $method){
                $controller = 'WPAS\\Controller\\'.$controller;
                $v = new $controller();

                $pieces = explode(':',$method);
                if(count($pieces)==2)
                {
                    $methodName = 'ajax_'.$pieces[1];
                    
                    $pieces[0] = strtolower($pieces[0]);
                    $hookName = 'wp_ajax_'.$pieces[1];
                    if($pieces[0]=='public') $hookName = 'wp_ajax_nopriv_'.$pieces[1];
                    if(!is_callable([$v,$methodName])) throw new Exception('Ajax method '.$methodName.' does not exists in controller '.$controller);
                    
                    add_action($hookName, [$v,$methodName]);
                }
                else throw new Exception('Ajax rout '.$method.' must be Public or Private');
            }
        }
    }
    
    public function load(){
        foreach(self::$routes as $view => $controller){
            $viewType = null;
            $viewHierarchy = $this->getViewType($view);
            if(is_array($viewHierarchy)) //it means the original view was something like Category:cars
            {
                $view = $viewHierarchy[1]; //The view
                $viewType = $viewHierarchy[0]; //The type of the view
            }
            
            if($this->isCurrentView($view,$viewType)){
                $view = $this->prepareViewName($view);
                $controller = 'WPAS\\Controller\\'.$controller;
                $v = new $controller();
                if(is_callable([$v,'render'.$view])){
                    self::$args = call_user_func([$v,'render'.$view]);
                    if(is_null(self::$args) && WP_DEBUG) echo '<p style="margin-top:50px;margin-bottom:0px;" class="alert alert-warning">Warning: the render method is returning null!</p>';
                }
                else throw new Exception('Render method for view '.$view.' does not exists');
                return;
            } 
            
        }
    }
    
    public function loadScripts(){
        
        foreach(self::$ajaxRouts as $view => $routes)
        {
    	    if($this->isCurrentView($view))
    	    {
    		    wp_register_script( $view, get_stylesheet_directory_uri().'/assets/js/pages/'.strtolower($view).'.js' , array('ajaxmodule'), $this->prependversion, true );
    		    wp_enqueue_script( $view );
    	    }
        }
    }
    
    private function loadJSController($view){ 
        
        //todo inject ajaxurl into JS
        if(self::isCurrentView()){
            $view = $this->prepareViewName($view);
        }
    }
    
    public function getViewData(){
        return self::$args;
    }
    
    public static function ajaxSuccess($data){
        header('Content-type: application/json');
		echo json_encode([ "code" => 200,"data" => $data ]);
		die(); 
    }
    
    public static function ajaxError($message){
        header('Content-type: application/json');
		echo json_encode([ "code" => 500, "msg" => $message ]);
		die(); 
    }
    
    private function getViewType($view){
        $pieces = explode(':',$view);
        if(count($pieces)==1) return $pieces[0];
        else if(count($pieces)==2) return [$pieces[0],$pieces[1]];
        else throw new Exception('The view '.$view.' is invalid');
    }
    
    private function isCurrentView($view, $type='default'){
        $type = strtolower($type);
        $view = strtolower($view);
        switch($type)
        {
            case null: 
                return (is_page($view) || is_singular($view));
            break;
            case "category": 
                return is_tax($view); 
            break;
            
        }
    }
    
    private function prepareViewName($view){
        $view = str_replace(['-','_'], '', $view);
        return $view;
    }
    
}
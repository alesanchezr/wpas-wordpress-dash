<?php

namespace WPAS\Controller;

use WPAS\Exception\WPASException;

class WPASController{
    
    const PRIVATE_SCOPE = 'wp_ajax_';
    const PUBLIC_SCOPE = 'wp_ajax_nopriv_';
    
    private $ajaxRouts = [];
    private $routes = [];
    private $options = [];
    private $closures = [];
    static protected $args = [];
    
    public function __construct($options=[]){
        
        $this->options = [
            'namespace' => '',
            'data' => null,
            'mainscript' => null,
            'mainscript-requierments' => []
            ];
        $this->loadOptions($options);
        
        add_action('template_redirect', [$this,'load']);
        add_action( 'wp_enqueue_scripts', [$this,'loadScripts'] );
        add_action( 'init', [$this,'loadAjax'] );
    
    }
    /*
    function loadAjaxCalls(){
		if ( is_admin() ) {
			add_action( 'wp_ajax_nopriv_ajax-example', array( &$this, 'ajax_call' ) );
			add_action( 'wp_ajax_ajax-example', array( &$this, 'ajax_call' ) );
		}
		add_action( 'init', array( &$this, 'init' ) );
    }*/
    
    private function loadOptions($options){
        foreach($this->options as $key => $val) 
            if(isset($options[$key])) 
                $this->options[$key] = $options[$key];
                
    }
    
    public function route($args){
        
        $view = $args['slug'];
        $controller = $args['controller'];
        
        $this->routes[$view] = $controller;
    }
    
    public function routeAjax($args){
        
        if(!is_array($args)) throw new WPASException('routeAjax is expecting an array');
        if(!isset($args['slug']) || !isset($args['controller'])){
            throw new WPASException('routeAjax is expecting the "slug" and "controller" parameters at least');
        } 
        
        $view = $args['slug'];
        $controller = $args['controller'];
        
        $scope = 'private';
        if(isset($args['scope'])) $scope = strtolower($args['scope']);
        
        $closureIndex = $controller;
        if($this->is_closure($controller)){
            if(!isset($args['action']))  throw new WPASException('Since your controller for '.$view.' is a closure, your need to specify the ajax "action"');
            $closureIndex = spl_object_hash($controller);
            $this->closures[$closureIndex] = [
                'action' => $args['action'],
                'closure' => $controller
            ];
        }

        if(!isset($this->ajaxRouts[$view])) $this->ajaxRouts[$view] = [];
        $this->ajaxRouts[$view][$closureIndex] = $scope;
    }
    
    public function loadAjax(){
        
        foreach($this->ajaxRouts as $view => $routes){
            foreach($routes as $controller => $scope){
                $controller = $this->options['namespace'].$controller;

                $hookName = self::PRIVATE_SCOPE;
                if($scope=='public') $hookName = self::PUBLIC_SCOPE;
                
                $this->executeController($hookName,$controller);
            }
        }
    }
    
    private function executeController($hookName, $controller){
        $pieces = explode(':',$controller);
        if(count($pieces)==2)
        {
            $controller = $pieces[0];
            $methodName .= $pieces[1];
            
            $v = new $controller();
            if(!is_callable([$v,$methodName])) throw new WPASException('Ajax method '.$methodName.' does not exists in controller '.$controller);
            
            add_action($hookName.$methodName, array($v,$methodName)); 
            
            //if it is public I should also make available to logged in users
            if($hookName==self::PUBLIC_SCOPE) add_action(self::PRIVATE_SCOPE.$methodName, array($v,$methodName)); 
        }
        else if(count($pieces)==1)
        {
            if(!$this->is_closure( $this->closures[$controller]['closure'])) throw new WPASException('Ajax method '.$controller.' is not executable or a clousure');
            $methodName .= $this->closures[$controller]['action'];

            add_action($hookName.$methodName, $this->closures[$controller]['closure']);
            //if it is public I should also make available to logged in users
            if($hookName==self::PUBLIC_SCOPE) add_action(self::PRIVATE_SCOPE.$methodName, $this->closures[$controller]['closure']); 
        }
        else throw new WPASException('Invalid ajax controller: '.$controller);
    
    }
    
    private function is_closure($t) {
        return is_callable($t);
    }
    
    public function load(){
        
        foreach($this->routes as $view => $controller){
            $viewType = 'default';
            $viewHierarchy = $this->getViewType($view);
            if(is_array($viewHierarchy)) //it means the original view was something like Category:cars
            {
                $view = $viewHierarchy[1]; //The view
                $viewType = $viewHierarchy[0]; //The type of the view
            }
            if($this->isCurrentView($view,$viewType)){
                $view = $this->prepareControllerName($view);
                $controller = $this->options['namespace'].$controller;
                $v = new $controller();
                if(is_callable([$v,'render'.$view])){
                    self::$args = call_user_func([$v,'render'.$view]);
                    if(is_null(self::$args) && WP_DEBUG) echo '<p style="margin-top:50px;margin-bottom:0px;" class="alert alert-warning">Warning: the render method is returning null!</p>';
                }
                else throw new WPASException('Render method for view '.$view.' does not exists in '.$controller);
                return;
            } 
            
        }
    }
    
    public function loadScripts(){
        
        foreach($this->ajaxRouts as $view => $routes)
        {
    	    if($this->options['mainscript'] && $this->isCurrentView($view))
    	    {
    		    wp_register_script( 'wpas_ajax', WPAS_ABS_PATH . 'public/js/ajax.js' , [], '0.1' );
    		    wp_enqueue_script( 'wpas_ajax' );
    		    $this->options['mainscript-requierments'][] = 'wpas_ajax';
    		    
    		    wp_register_script( 'mainscript', get_stylesheet_directory_uri().$this->options['mainscript'] , $this->options['mainscript-requierments'], '0.1' );
        	    
        	    $data = [];
        	    if($this->options['data'] && is_array($this->options['data'])) $data = $this->options['data'];
                $data['ajax_url'] = admin_url( 'admin-ajax.php' );
                $data['action'] = $this->prepareControllerName($view);
        	    
        	    wp_localize_script( 'mainscript', 'WPAS_APP', $data);
    		    wp_enqueue_script( 'mainscript' );
    	    }
        }
    }
    
    public static function getViewData(){
        return self::$args;
    }
    
    public function ajaxSuccess($data){
        header('Content-type: application/json');
		echo json_encode([ "code" => 200,"data" => $data ]);
		wp_die(); 
    }
    
    public function ajaxError($message){
        header('Content-type: application/json');
		echo json_encode([ "code" => 500, "msg" => $message ]);
		wp_die(); 
    }
    
    private function getViewType($view){
        $pieces = explode(':',$view);
        if(count($pieces)==1) return $pieces[0];
        else if(count($pieces)==2) return [$pieces[0],$pieces[1]];
        else throw new WPASException('The view '.$view.' is invalid');
    }
    
    private function isCurrentView($view, $type='default'){
        $type = strtolower($type);
        $view = strtolower($view);
        switch($type)
        {
            case 'default': 
                return (is_page($view) || is_singular($view));
            break;
            case "category": 
                return is_tax($view); 
            break;
            
        }
    }
    
    private function prepareControllerName($view){
        $view = str_replace(['-','_'], '', $view);
        return $view;
    }
    
}
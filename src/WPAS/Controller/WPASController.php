<?php

namespace WPAS\Controller;

require_once('global_functions.php');

use WPAS\Utils\WPASException;
use WPAS\Utils\TemplateContext;
use WPAS\Utils\WPASLogger;

class WPASController{
    
    const PRIVATE_SCOPE = 'wp_ajax_';
    const PUBLIC_SCOPE = 'wp_ajax_nopriv_';
    
    private $ajaxRouts = [];
    private $routes = [];
    private $options = [];
    private $closures = [];
    
    public static $ajaxController = null;
    static protected $args = [];
    
    public function __construct($options=[]){
        
        WPASLogger::getLogger('wpas_controller');
        
        $this->options = [
            'namespace' => '',
            'data' => null,
            'mainscript' => null,
            'mainscript-requierments' => []
            ];
        $this->loadOptions($options);
        
        if($this->doingAJAX()){
            add_action( 'init', [$this,'loadAjax'] );
        }
        else
        {
            add_action('template_redirect', [$this,'load']);
            add_action ( 'wp_head', function(){ ?>
                <script type="text/javascript">
                    /* <![CDATA[ */
                    var WPAS_APP = <?php echo json_encode($this->loadJavascriptVariables(), JSON_PRETTY_PRINT); ?>
                    /* ]]> */
                </script>
                <?php
            },2 );
        }
    
    }

    function doingAJAX(){
		if(!defined('DOING_AJAX')) return false;
		else return true;
    }
    
    private function loadOptions($options){
        foreach($this->options as $key => $val) 
            if(isset($options[$key])) 
                $this->options[$key] = $options[$key];
                
    }
    
    public function routeAjax($args){
        
        $this->loadControllerCustomHooks();
        
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
        
        WPASLogger::info('INIT AJAX');
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
        $methodName = '';
        if(count($pieces)==2)
        {
            $controller = '\\'.$pieces[0];
            $methodName .= $pieces[1];
            
            if(!class_exists($controller))  throw new WPASException('Controller Class '.$controller.' does not exists');
            $v = new $controller();
            if(!is_callable([$v,$methodName])) throw new WPASException('Ajax method '.$methodName.' does not exists in controller '.$controller);

            add_action($hookName.$methodName, array($v,$methodName)); 
            WPASLogger::info('WPASController: Adding AJAX route '.$hookName.$methodName);
            //if it is public I should also make available to logged in users
            if($hookName==self::PUBLIC_SCOPE){
                WPASLogger::info('WPASController: Adding AJAX route '.self::PRIVATE_SCOPE.$methodName);
                add_action(self::PRIVATE_SCOPE.$methodName, array($v,$methodName)); 
            } 
        }
        else if(count($pieces)==1)
        {
            $controller = explode('\\',$controller);
            if(is_array($controller)) $controller = end($controller);
            
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
    
    public function route($args){
        
        $view = $args['slug'];
        
        $controller = $args['controller'];
        $closureIndex = $controller;
        if($this->is_closure($controller)){
            if(!isset($args['action']))  throw new WPASException('Since your controller for '.$view.' is a closure, your need to specify the ajax "action"');
            $closureIndex = spl_object_hash($controller);
            $this->closures[$closureIndex] = [
                'action' => $args['action'],
                'closure' => $controller
            ];
        }
        
        $this->routes[$view] = $closureIndex;
            
    }
    public function load(){

        WPASLogger::info('WPASController: INIT');
        $this->loadAjaxController();
        foreach($this->routes as $view => $controller){
            
            if($pieces = TemplateContext::matchesViewAndType($view)){
                
                $view = $this->prepareControllerName($pieces[1]);
                
                $controllerObject = $this->getController($controller);
                $methodName = 'render'.$view;
                $className = $controllerObject;
                if(is_array($className))
                {
                    $methodName = $controllerObject[1]; //The view
                    $className = $controllerObject[0]; //The type of the view
                }else if($view=='all') throw new WPASException('When using the "all" keyword you have to specify a method in the controler parameter');
                
                WPASLogger::info('WPASController: match found for [ type => '.$pieces[0].', view => '.$pieces[1].' ] calling: '.$methodName);
                $controller = $this->options['namespace'].$className;
                $v = new $controller();
                if(is_callable([$v,$methodName])){
                    self::$args = call_user_func([$v,$methodName]);
                    if(is_null(self::$args) && WP_DEBUG) echo '<p style="margin-top:50px;margin-bottom:0px;" class="alert alert-warning">Warning: the render method is returning null!</p>';
                }
                else throw new WPASException('Method "'.$methodName.'" for view "'.$view.'" does not exists in '.$className);
                return;
            } 
            
        }
    }
    
    private function loadJavascriptVariables(){

        $context = TemplateContext::getContext();
	    $data = [];
	    if($this->options['data'] && is_array($this->options['data'])) $data = $this->options['data'];
        $data['ajax_url'] = admin_url( 'admin-ajax.php' );
        $data['view'] = $context;
        $data['controller'] = self::getAjaxController();
        
        return $data;
    }
    
    public function loadAjaxController(){
        foreach($this->ajaxRouts as $view => $routes)
        {
            if($pieces = TemplateContext::matchesViewAndType($view)){
                WPASLogger::info('WPASController: match found for [ type => '.$pieces[0].', view => '.$pieces[1].' ]');
                self::$ajaxController = $this->prepareControllerName($view);
            }
        }
    }
    
    public function loadControllerCustomHooks(){
        $userControllers = [];
        foreach($this->routes as $view => $controller){
            
            $controllerObject = $this->getController($controller);
            $className = $controllerObject;
            if(is_array($className)) $className = $controllerObject[0]; //The type of the view

            if(!in_array($className, $userControllers))
            {
                $userControllers[] = $className;
                $controller = $this->options['namespace'].$className;
                $v = new $controller();
                if(is_callable([$v,'load_controller_hooks'])){
                    call_user_func([$v,'load_controller_hooks']);
                }
            }
        }
    }
    
    public static function getAjaxController(){
        return self::$ajaxController;
    }
    
    public static function printError($error){
        if(!is_a($error,'WP_Error')) return new WPASException('Funciton printErrorTemplate is expecting a WP_Error object');

        if($error->errors['default'])
        $content = '<div class="text-left"><div class="inner-message alert alert-danger"><button type="button" class="close" data-dismiss="alert">&times;</button><ul>';
        $messages = $error->get_error_messages();
        foreach($messages as $msg) $content .= '<li>'.$msg.'</li>';
        $content .= '</ul></div></div>';
        
        return $content;
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
    
    private function getController($controller){
        $pieces = explode(':',$controller);
        if(count($pieces)==1) return $pieces[0];
        else if(count($pieces)==2) return [$pieces[0],$pieces[1]];
        else throw new WPASException('The controller '.$controller.' is invalid');
    }
    
    private function prepareControllerName($view){
        $view = str_replace(['-','_'], '', $view);
        return $view;
    }
    
    public static function getViewData(){
        
        if(empty(self::$args['wp_query'])) self::$args['wp_query'] = get_queried_object();
        return self::$args;
    }
}
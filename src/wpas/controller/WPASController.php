<?php

require('../../autoload.php');

class WPASBCController{
    
    private $ajaxRouts = [];
    private $routes = [];
    protected static $args = [];
    
    public function __construct(){
        add_action('template_redirect', [$this,'load']);
        add_action( 'wp_enqueue_scripts', [$this,'loadScripts'] );
        add_action('template_redirect', [$this,'loadFooter'],3,10);
    }
    
    public function route($view, $controller){
        $this->routes[$view] = $controller;
    }
    
    public function routeAjax($view, $controller, $method){
        
        if(!isset($this->ajaxRouts[$view])) $this->ajaxRouts[$view] = [];
        $this->ajaxRouts[$view][$controller] = $method;
    }
    
    public function loadAjax(){
        
        foreach($this->ajaxRouts as $view => $routes){
            foreach($routes as $controller => $method){
                $controller = 'BreatheCode\\Controller\\'.$controller;
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
        foreach($this->routes as $view => $controller){
            $viewType = null;
            $viewHierarchy = $this->getViewType($view);
            if(is_array($viewHierarchy)) //it means the original view was something like Category:cars
            {
                $view = $viewHierarchy[1]; //The view
                $viewType = $viewHierarchy[0]; //The type of the view
            }
            
            if($this->isCurrentView($view,$viewType)){
                $view = $this->prepareViewName($view);
                $controller = 'BreatheCode\\Controller\\'.$controller;
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
        
        foreach($this->ajaxRouts as $view => $routes)
        {
            $view = strtolower($view);
    	    if(is_page($view) || is_singular($view))
    	    {
    		    wp_register_script( $view, get_stylesheet_directory_uri().'/assets/js/pages/'.strtolower($view).'.js' , array('ajaxmodule'), $this->prependversion, true );
    		    wp_enqueue_script( $view );
    	    }
        }
    }
    
    private function loadJSController($view){ 
        if(is_page(strtolower($view)) || is_singular(strtolower($view))){
            $view = $this->prepareViewName($view);
    ?>
        <script type="text/javascript">
        	window.onload = function(){
        		let v = new <?php echo $view ?>Controller({
        			"ajaxurl": '<?php echo admin_url( 'admin-ajax.php' ); ?>'
        		});
        		v.init();
        	}
        </script>
    <?php }
    }
    
    public static function getViewData(){
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
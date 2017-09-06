<?php

namespace WPAS\Extend;

use WPAS\Utils;

class VCComponent implements VCComponentInterface{
    
    private $options = [];
    private $renderFunction = null;
    
    function __construct($name, $category=''){
    	
		//$this->options['base'] = spl_object_hash($this);
		$this->options['name'] = $name;
		$this->options['category'] = $category;
		$this->options['base'] = $this->generateBase();
    }
    
    private function generateBase(){
    	return strtolower($this->options['category'].'_'.$this->options['name']);
    }
    
    public function initialize(){
    	add_action( 'vc_before_init', array($this,'register'));
        
        if(is_callable($this->renderFunction)) add_shortcode( $this->options['base'], $this->renderFunction);
        else throw new WPASException('You must define how to render the component: '.$this->options['name'].' using the "render" method');
        //add_shortcode( $this->options['base'], array($this,'render'));
    }
    
    function register(){
    	//print_r($this->options); die();
	   vc_map( $this->options);
    }
    
    function render($renderFunction){
		$this->renderFunction = $renderFunction;
		
		$this->initialize();
    }
    
	private function _render( $atts , $content = null) {
	   extract( shortcode_atts( array(
	      'linenumbers' => 'false',
	      'newcodeexample' => 'false',
	      'codelanguage' => 'markup'
	   ), $atts ) );

	   if(!$newcodeexample or $newcodeexample=='false') {
	   	$content = wpb_js_remove_wpautop($content, true);
	   }
	   else {
	    $content = urldecode(base64_decode($content));
	   	if($codelanguage=='html' || $codelanguage=='markup') $content = htmlentities($content);
	   }

	   if(!$linenumbers or $linenumbers!='true') $numerstring = '';
	   else $numerstring = 'line-numbers';
	  
	   return '<pre class="'.$numerstring.'"><code class="language-'.$codelanguage.'">'.$content.'</code></pre>';
	}
	
	public function addInput($type, $args){
		$type = strtolower($type);
		if(!isset($this->options["params"])) $this->options["params"] = [];
		
		if(!is_callable([$this, 'add_'.$type])) throw new WPASException('Invalid VCComponent input: '.$type);
		$this->options["params"][] = call_user_func_array([$this, 'add_'.strtolower($type)], $args);
	}
	
	private function add_checkbox($paramId, $heading, $default, $description=''){
		
		$result = array(
	            "type" => "checkbox",
	    );
	    $result = $this->setParam($result, 'value', array('on'   => $default ));
	    $result = $this->setParam($result, 'param_name',$paramId);
	    $result = $this->setOptionalParam($result, 'heading',$heading);
	    $result = $this->setOptionalParam($result, 'description',$description);
	    
	    return $result;
	}
	
	private function add_dropdown($paramId, $heading, $values, $description=''){
		
		$result = array(
	            "type" => "dropdown",
	         );
	    $result = $this->setParam($result, 'value', $values);
	    $result = $this->setParam($result, 'param_name',$paramId);
	    $result = $this->setOptionalParam($result, 'heading',$heading);
	    $result = $this->setOptionalParam($result, 'description',$description);
	    
	    return $result;
	}
	
	private function add_raw($paramId, $heading, $value='', $description='', $weigh=20,$holder='div'){
		
		$result = array(
	            "type" => "textarea_raw_html",
	         );
	    $result = $this->setParam($result, 'param_name',$paramId);
	    $result = $this->setOptionalParam($result, 'value', $value);
	    $result = $this->setOptionalParam($result, 'holder',$holder);
	    $result = $this->setOptionalParam($result, 'weight',$weigh);
	    $result = $this->setOptionalParam($result, 'heading',$heading);
	    $result = $this->setOptionalParam($result, 'description',$description);
	    
	    return $result;
	}
	
	private function add_text($paramId, $heading, $value='', $description='', $weigh=20,$holder='div'){
		
		$result = array(
	            "type" => "textfield",
	         );
	    $result = $this->setParam($result, 'param_name',$paramId);
	    $result = $this->setOptionalParam($result, 'value', $value);
	    $result = $this->setOptionalParam($result, 'holder',$holder);
	    $result = $this->setOptionalParam($result, 'heading',$heading);
	    $result = $this->setOptionalParam($result, 'description',$description);
	    
	    return $result;
	}
	
	private function setOptionalParam($array, $index, $value){
		//if(!empty($value)) 
		$array[$index] = __( $value, WPAS_DOMAIN );
		return $array;
	}
	
	private function setParam($array, $index, $value){
		if(empty($value)) throw new WPASException("Invalid $index parameter for the ".$this->options['name']." VCComponent");
		
		$array[$index] = __( $value, WPAS_DOMAIN );
		return $array;
	}
}
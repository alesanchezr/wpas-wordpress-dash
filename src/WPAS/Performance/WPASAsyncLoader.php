<?php

namespace WPAS\Performance;
use WPAS\Utils\WPASException;
use WPAS\Utils\TemplateContext;

require_once('global_functions.php');

class WPASAsyncLoader{
    
    private static $manifest = [];
    private static $criticalStyles = [];
    private static $scripts = [];
    private static $styles = [];
    private static $loadedStyleCount = 0;
    private static $publicUrl = '';
    private static $ready = false;
    private static $cacheVersion = '1';
    private static $leaveScriptsAlone = null;
    private static $insideAdmin = false;
    
    private static $criticalStylesQueue = [];
    
    public function __construct($options=[]){
        
        self::$insideAdmin = is_admin();
        
        if(!self::$insideAdmin)
        {
            if(!empty($options['leave-scripts-alone'])) $leaveScriptsAlone = $options['leave-scripts-alone'];
            
            if(empty($options['debug'])) $options['debug'] = false;
            
            if(!empty($options['public-url'])){
                self::$publicUrl = $options['public-url'];
            }
            if(empty($options['manifest-url'])) $options['manifest-url'] = 'manifest.json';
            
            $manifestURL = $options['public-url'].$options['manifest-url'];
            $jsonManifiest = json_decode($this->get_file_content($manifestURL));
            if($jsonManifiest) self::$manifest = $this->loadManifiest($jsonManifiest);
            else throw new Exception('Invalid Manifiest Syntax');
            
            if(!empty($options['minify-html']) && $options['minify-html']===true){
                if(!defined('UGLIFY_HTML')) ob_start([$this,"minifyHTML"]);
                else if(UGLIFY_HTML) ob_start([$this,"minifyHTML"]);
            }
            if(!empty($options['critical-styles'])){
                self::$criticalStyles = $options['critical-styles'];
                add_action('wpas_print_critical_styles',[$this,'printCriticalStyles']);
            }
            
            if(!empty($options['scripts'])) self::$scripts = $options['scripts'];
            if(!empty($options['styles'])) self::$styles = $options['styles'];
            
            //If debug=true I load the styles the old way
            if($options['debug']) add_action('wp_enqueue_scripts',[$this,'loadDebuggableScriptsAndStyles']);
            else{
                //If we are not debugging I load the styles the new way
                add_action('wpas_print_footer_scripts',[$this,'loadScriptsAsync']);
                add_action('wpas_print_styles',[$this,'loadStylesAsync'], 20);
            }
            // load our posts-only PHP
            add_action( "wp", [$this,"is_ready"] );
            add_action( 'init', [$this,'remove_previous_styles'], 20 );
        }
        
    }            
    
    public function remove_previous_styles(){
        
        if (!self::$insideAdmin && !self::is_login_page() && !self::$leaveScriptsAlone) {
            wp_deregister_script('jquery');
            wp_register_script('jquery', '', '', '', true);     
            wp_deregister_script( 'wp-embed' ); 
       }
    }
    
    public function is_ready(){
        self::$ready = true;
    }
    
    public function is_login_page(){
        if ( $GLOBALS['pagenow'] === 'wp-login.php' ) {
            // We're on the login page!
            return true;
        }
    }
    
    private function loadManifiest($manifiestObj){
        $manifest = [];
        foreach($manifiestObj as $resource => $path)
            $manifest[$resource] = self::$publicUrl.$path;
            
        return $manifest;
    }
    
    public static function filter_manifest($url){
        
        if(!empty(self::$manifest)){
            if(empty(self::$manifest[$url])) throw new WPASException('The index '.$url.' was not found in the manifest.json');
            else return self::$manifest[$url];
        }
        else return $url;
    }
    
    /**
     * Minifies the HTML outuput only
     **/
    private function minifyHTML($buffer){

        if(!self::$insideAdmin)//if im not in the wordpress admin section
        {
            $search = array(
                '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
                '/[^\S ]+\</s',     // strip whitespaces before tags, except space
                '/(\s)+/s',         // shorten multiple whitespace sequences
                '/<!--(.|\s)*?-->/' // Remove HTML comments
            );
        
            $replace = array(
                '>',
                '<',
                '\\1',
                ''
            );
        
            $buffer = preg_replace($search, $replace, $buffer);
        }
    
        return $buffer;
    }
    
    public static function print_style_tag($path){
        if(self::$loadedStyleCount == 0){
            echo '<script>
    		/*! loadCSS. [c]2017 Filament Group, Inc. MIT License */
    		!function(a){"use strict";var b=function(b,c,d){function e(a){return h.body?a():void setTimeout(function(){e(a)})}function f(){i.addEventListener&&i.removeEventListener("load",f),i.media=d||"all"}var g,h=a.document,i=h.createElement("link");if(c)g=c;else{var j=(h.body||h.getElementsByTagName("head")[0]).childNodes;g=j[j.length-1]}var k=h.styleSheets;i.rel="stylesheet",i.href=b,i.media="only x",e(function(){g.parentNode.insertBefore(i,c?g:g.nextSibling)});var l=function(a){for(var b=i.href,c=k.length;c--;)if(k[c].href===b)return a();setTimeout(function(){l(a)})};return i.addEventListener&&i.addEventListener("load",f),i.onloadcssdefined=l,l(f),i};"undefined"!=typeof exports?exports.loadCSS=b:a.loadCSS=b}("undefined"!=typeof global?global:this);
    		/*! loadCSS rel=preload polyfill. [c]2017 Filament Group, Inc. MIT License */
    		!function(a){if(a.loadCSS){var b=loadCSS.relpreload={};if(b.support=function(){try{return a.document.createElement("link").relList.supports("preload")}catch(b){return!1}},b.poly=function(){for(var b=a.document.getElementsByTagName("link"),c=0;c<b.length;c++){var d=b[c];"preload"===d.rel&&"style"===d.getAttribute("as")&&(a.loadCSS(d.href,d,d.getAttribute("media")),d.rel=null)}},!b.support()){b.poly();var c=a.setInterval(b.poly,300);a.addEventListener&&a.addEventListener("load",function(){b.poly(),a.clearInterval(c)}),a.attachEvent&&a.attachEvent("onload",function(){a.clearInterval(c)})}}}(this);
    		</script>';
        }
        self::$loadedStyleCount++;
        echo "<!-- printing style ".self::$loadedStyleCount." --> \n".'<link rel="preload" href="'.self::filter_manifest($path).'?v=1" as="style" onload="this.rel=\'stylesheet\'">';
    }
    
    public static function print_styles($path){
        $cssContent = self::get_file_content($path);
        echo '<style type="text/css">'.$cssContent.'</style>';
    }
    
    public static function printCriticalStyles(){
        
        if(!empty(self::$criticalStyles))
        {
            $currentPage = TemplateContext::getContext(self::$ready);
            
            $key = self::getMatch($currentPage, self::$criticalStyles);
            //print_r(self::$criticalStyles); die();  
            if($key) self::print_styles(self::filter_manifest(self::$criticalStyles[$currentPage['type']][$key]));
        }
        //echo print_r($currentPage); die();
    }
    
    /**
     * Loads the non-critical styles asynchronously (how google likes it)
     * the critical styles are printed inline in another function adn from a separate files
     **/
    public static function loadStylesAsync($param){
        
        if(!empty(self::$styles))
        {
            $currentPage = TemplateContext::getContext(self::$ready);
            $key = self::getMatch($currentPage, self::$styles);
            if($key) self::print_style_tag(self::$styles[$currentPage['type']][$key]);
        }
    }
    
    /**
     * Loads the scripts asynchronously (how google likes it)
     **/
    public static function loadScriptsAsync(){
        
        if(!empty(self::$scripts))
        {
            $currentPage = TemplateContext::getContext(self::$ready);
            //print_r($currentPage); die();
            $key = self::getMatch($currentPage, self::$scripts);
            if($key){
                self::printScripts(self::$scripts[$currentPage['type']][$key]);
            }
        }
    }
    
    /**
     * Loads the scripts the old traditional way
     **/
    public static function loadDebuggableScriptsAndStyles(){
        
        $currentPage = TemplateContext::getContext(self::$ready);
        if(!empty(self::$scripts))
        {
            $key = self::getMatch($currentPage, self::$scripts);
            if($key){
                $scripts = [];
                $oldScript = [];
                foreach(self::$scripts[$currentPage['type']][$key] as $script) 
                {
                    wp_enqueue_script( $script, self::filter_manifest($script), $oldScript, '1.0.0', true );
                    $oldScript = [$script];
                }
                    
            }
            
            
            if (class_exists( 'GFCommon' )) self::loadGravityFormsOnFooter();
        }
        
        if(!empty(self::$styles))
        {
            $key = self::getMatch($currentPage, self::$styles);
            if($key){
                $styles = [];
                $oldStyle = [];
                if(is_array(self::$styles[$currentPage['type']][$key])) foreach(self::$styles[$currentPage['type']][$key] as $style) 
                {
                    wp_enqueue_style( $style, self::filter_manifest($style), $oldStyle, '1.0.0' );
                    $oldStyle = [$style];
                }
                else{
                    $style = self::$styles[$currentPage['type']][$key];
                    wp_enqueue_style( $style, self::filter_manifest($style), null, '1.0.0' );
                } 
                    
            }
        }
    }
    
    private static function printScripts($scriptsToPrint){
        if(count($scriptsToPrint)>2) throw new Exception('There can only be 2 scripts to load'); 
        $scripts = [];
        foreach($scriptsToPrint as $script) $scripts[] = self::filter_manifest($script);
        
        echo '<script type="text/javascript">
                var WPASScriptManger=function(){var e={};return e.single=function(e,n){var t=new XMLHttpRequest;t.open("GET",e),t.addEventListener("load",function(){var e=document.createElement("script");e.type="text/javascript",e.text=t.responseText,document.getElementsByTagName("head")[0].appendChild(e),n&&n()}),t.send()},e.load=function(n){e.single(n[0],function(){void 0!==n[1]&&e.single(n[1])})},e}();
                window.onload=function(){WPASScriptManger.load('.json_encode($scripts,JSON_UNESCAPED_SLASHES).');};
            </script>';
        
        if (class_exists( 'GFCommon' )) self::loadGravityFormsOnFooter();
    }
    
    private static function get_file_content($file){
        $file_headers = @get_headers($file);
        if(!$file_headers || $file_headers[0] == 'HTTP/1.1 404 Not Found') throw new WPASException("File ".$file." not found");
        else{
            $content = @file_get_contents($file);
            if(empty($content)) throw new WPASException("File ".$file." content was imposible to get or it's empty");
            else return $content;
        };
    }
    
    private static function getMatch($currentPage, $hierarchy){
        if(!empty($hierarchy[$currentPage['type']])){
            if(!empty($hierarchy[$currentPage['type']][$currentPage['slug']])) return $currentPage['slug'];
            if(!empty($hierarchy[$currentPage['type']]['all'])) return 'all';
        }else return null;
    }
    
    private static function loadGravityFormsOnFooter(){
        
        // GF method: http://www.gravityhelp.com/documentation/gravity-forms/extending-gravity-forms/hooks/filters/gform_init_scripts_footer/
        add_filter( 'gform_init_scripts_footer', '__return_true' );
    
        // solution to move remaining JS from https://bjornjohansen.no/load-gravity-forms-js-in-footer

        
        //deregister all scripts
        add_action("gform_enqueue_scripts", function (){
                     //Change this conditional to target whatever page or form you need.
		    if(!is_admin()) { 
                        
                //These are the CSS stylesheets 
                wp_deregister_style("gforms_formsmain_css"); 	
                wp_deregister_style("gforms_reset_css");
                wp_deregister_style("gforms_ready_class_css");
                wp_deregister_style("gforms_browsers_css");
                
                //These are the scripts. 
                //NOTE: Gravity forms automatically includes only the scripts it needs, so be careful here. 
                $base_url = \GFCommon::get_base_url();
                $min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG || isset( $_GET['gform_debug'] ) ? '' : '.min';
                
        		wp_deregister_script('gform_chosen');
        		wp_register_script( 'gform_chosen', $base_url . '/js/chosen.jquery.min.js', array( 'jquery' ), self::$cacheVersion, true );
        		
        		wp_deregister_script('gform_conditional_logic');
        		wp_register_script( 'gform_conditional_logic', $base_url . "/js/conditional_logic{$min}.js", array( 'jquery', 'gform_gravityforms' ), self::$cacheVersion, true );
        		
        		wp_deregister_script('gform_datepicker_init');
        		wp_register_script( 'gform_datepicker_init', $base_url . "/js/datepicker{$min}.js", array( 'jquery', 'jquery-ui-datepicker', 'gform_gravityforms' ), self::$cacheVersion, true );
        		
        		wp_deregister_script('gform_floatmenu');
        		wp_register_script( 'gform_floatmenu', $base_url . "/js/floatmenu_init{$min}.js", array( 'jquery' ), self::$cacheVersion, true );

        		wp_deregister_script('gform_form_admin');
        		wp_register_script( 'gform_form_admin', $base_url . "/js/form_admin{$min}.js", array( 'jquery', 'jquery-ui-autocomplete', 'gform_placeholder' ), self::$cacheVersion, true );

        		wp_deregister_script('gform_form_editor');
        		wp_register_script( 'gform_form_editor', $base_url . "/js/form_editor{$min}.js", array( 'jquery', 'gform_json', 'gform_placeholder' ), self::$cacheVersion, true );

        		wp_deregister_script('gform_forms');
        		wp_register_script( 'gform_forms', $base_url . "/js/forms{$min}.js", array( 'jquery' ), self::$cacheVersion, true );

        		wp_deregister_script('gform_gravityforms');
        		wp_register_script( 'gform_gravityforms', $base_url . "/js/gravityforms{$min}.js", array( 'jquery', 'gform_json' ), self::$cacheVersion, true);

        		wp_deregister_script('gform_json');
        		wp_register_script( 'gform_json', $base_url . '/js/jquery.json.js', array( 'jquery' ), self::$cacheVersion, true );

        		wp_deregister_script('gform_masked_input');
        		wp_register_script( 'gform_masked_input', $base_url . '/js/jquery.maskedinput.min.js', array( 'jquery' ), self::$cacheVersion , true);

        		wp_deregister_script('gform_menu');
        		wp_register_script( 'gform_menu', $base_url . "/js/menu{$min}.js", array( 'jquery' ), self::$cacheVersion, true );

        		wp_deregister_script('gform_placeholder');
        		wp_register_script( 'gform_placeholder', $base_url . '/js/placeholders.jquery.min.js', array( 'jquery' ), self::$cacheVersion , true);

        		wp_deregister_script('gform_tooltip_init');
        		wp_register_script( 'gform_tooltip_init', $base_url . "/js/tooltip_init{$min}.js", array( 'jquery-ui-tooltip' ), self::$cacheVersion , true);

        		wp_deregister_script('gform_textarea_counter');
        		wp_register_script( 'gform_textarea_counter', $base_url . '/js/jquery.textareaCounter.plugin.js', array( 'jquery' ), self::$cacheVersion, true );

        		wp_deregister_script('gform_field_filter');
        		wp_register_script( 'gform_field_filter', $base_url . "/js/gf_field_filter{$min}.js", array( 'jquery', 'gform_datepicker_init' ), self::$cacheVersion, true );

        		wp_deregister_script('gform_shortcode_ui');
        		wp_register_script( 'gform_shortcode_ui', $base_url . "/js/shortcode-ui{$min}.js", array( 'jquery', 'wp-backbone' ), self::$cacheVersion, true );
                
		    }
		    
        }, 99);
        

    }
    
}
<?php

namespace WPAS\Performance;
use WPAS\Utils\WPASException;
use WPAS\Utils\TemplateContext;
use WPAS\Utils\WPASLogger;

require_once('global_functions.php');

class WPASStylesManager{
    
    private static $manifest = [];
    private static $criticalStyles = [];
    private static $styles = [];
    private static $loadedStyleCount = 0;
    private static $publicUrl = '';
    private static $ready = false;
    private static $cacheVersion = '1';
    private static $insideAdmin = false;
    
    private static $criticalStylesQueue = [];
    
    public function __construct($options=[]){

        WPASLogger::getLogger('wpas_styles_manager');
        
        self::$insideAdmin = is_admin();
        if(!self::$insideAdmin){
            
            if(empty($options['debug'])) $options['debug'] = false;
            
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
            if(empty($options['async'])) add_action('wp_enqueue_scripts',[$this,'loadStylesSyncrunous']);
            else{
                //If we are not debugging I load the styles the new way
                add_action('wpas_print_styles',[$this,'loadStylesAsync'], 20);
            }
            // load our posts-only PHP
            add_action( "wp", [$this,"is_ready"] );
        }
        
    }
    
    public static function setStyles($styles){
        if(!empty($styles['above'])) self::setAboveTheFoldStyles($styles['above']);
        if(!empty($styles['below'])) self::setBelowTheFoldStyles($styles['below']);
    }
    
    public static function setAboveTheFoldStyles($aboveTheFoldStyles = null){
        
        if(!empty($aboveTheFold)){
            self::$criticalStyles = $aboveTheFoldStyles;
            add_action('wpas_print_critical_styles',[$this,'printCriticalStyles']);
        }
        
    }
    
    public static function setBelowTheFoldStyles($belowTheFoldSyles = null){
        if(!empty($belowTheFoldSyles)) self::$styles = $belowTheFoldSyles;
        
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
        else return get_stylesheet_directory_uri().'/'.$url;
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
            WPASLogger::info('WPASAsyncLoader: Current Context [ type => '.$currentPage['type'].', slug => '.$currentPage['slug'].' ]');
            
            $key = self::getMatch($currentPage, self::$criticalStyles);
            if($key) self::print_styles(self::filter_manifest(self::$criticalStyles[$currentPage['type']][$key]));
        }
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
     * Loads the scripts the old traditional way
     **/
    public static function loadStylesSyncrunous(){
        $currentPage = TemplateContext::getContext(self::$ready);
        
        if(!empty(self::$styles))
        {
            $key = self::getMatch($currentPage, self::$styles);
            if($key){
                $styles = [];
                $oldStyle = [];
                if(is_array(self::$styles[$currentPage['type']][$key])) 
                {
                    foreach(self::$styles[$currentPage['type']][$key] as $style) 
                    {
                        //print_r(self::filter_manifest($style)); die();
                        wp_enqueue_style( $style, self::filter_manifest($style), $oldStyle, '1.0.0' );
                        $oldStyle = [$style];
                    }
                }
                else{
                    $style = self::$styles[$currentPage['type']][$key];
                    wp_enqueue_style( $style, self::filter_manifest($style), null, '1.0.0' );
                } 
                    
            }
        }
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
            else
            {
                $templateSlug = 'template:'.get_page_template_slug();
                if(!empty($hierarchy[$currentPage['type']][$templateSlug])){
                    return $templateSlug;
                } 
                else if(!empty($hierarchy[$currentPage['type']]['all'])) return 'all';
            } 
            
        }else return null;
    }
    
}
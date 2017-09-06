<?php

namespace WPAS\Performance;
use WPAS\Utils\WPASException;
use WPAS\Utils\TemplateContext;

class WPASAsyncLoader{
    
    private static $manifest = [];
    private static $criticalStyles = [];
    private static $scripts = [];
    private static $styles = [];
    private static $loadedStyleCount = 0;
    private static $publicUrl = '';
    private static $ready = false;
    
    private static $criticalStylesQueue = [];
    
    public function __construct($options=[]){
        
        if(!empty($options['public-url'])){
            self::$publicUrl = $options['public-url'];
        }
        if(empty($options['manifest-url'])) $options['manifest-url'] = 'manifest.json';
        
        $manifestURL = $options['public-url'].$options['manifest-url'];
        $jsonManifiest = json_decode($this->get_file_content($manifestURL));
        if($jsonManifiest) self::$manifest = $this->loadManifiest($jsonManifiest);
        else throw new Exception('Invalid Manifiest Syntax');
        
        if(!empty($options['minify-html']) && $options['minify-html']===true){
            if(!WP_DEBUG) ob_start([$this,"minifyHTML"]);
        }
        if(!empty($options['critical-styles'])){
            self::$criticalStyles = $options['critical-styles'];
            add_action('wpas_print_critical_styles',[$this,'printCriticalStyles']);
        }
        if(!empty($options['scripts'])){
            self::$scripts = $options['scripts'];
            add_action('wp_print_footer_scripts',[$this,'loadScripts']);
        }
        if(!empty($options['styles'])){
            self::$styles = $options['styles'];
            add_action('wp_print_styles',[$this,'loadStyles']);
        }
        // load our posts-only PHP
        add_action( "wp", [$this,"is_ready"] );
    }
    
    public function is_ready(){
        self::$ready = true;
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
    
    private function minifyHTML($buffer){

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
        echo "<!-- printing style ".self::$loadedStyleCount." --> \n".'<link rel="preload" href="'.self::filter_manifest($path).'" as="style" onload="this.rel=\'stylesheet\'">';
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
            if($key) self::print_styles(PUBLICPATH.'../'.self::$criticalStyles[$currentPage['type']][$key]);
        }
        //echo print_r($currentPage); die();
    }
    
    public static function loadStyles(){
        
        if(!empty(self::$styles))
        {
            $currentPage = TemplateContext::getContext(self::$ready);
            $key = self::getMatch($currentPage, self::$styles);
            if($key) self::print_style_tag(self::$styles[$currentPage['type']][$key]);
        }
    }
    
    public static function loadScripts(){
        
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
    
    private static function printScripts($scriptsToPrint){
        if(count($scriptsToPrint)>2) throw new Exception('There can only be 2 scripts to load'); 
        $scripts = [];
        foreach($scriptsToPrint as $script) $scripts[] = self::filter_manifest($script);
        
        echo '<script type="text/javascript">
                var WPASScriptManger=function(){var e={};return e.single=function(e,n){var t=new XMLHttpRequest;t.open("GET",e),t.addEventListener("load",function(){var e=document.createElement("script");e.type="text/javascript",e.text=t.responseText,document.getElementsByTagName("head")[0].appendChild(e),n&&n()}),t.send()},e.load=function(n){e.single(n[0],function(){void 0!==n[1]&&e.single(n[1])})},e}();
                window.onload=function(){WPASScriptManger.load('.json_encode($scripts,JSON_UNESCAPED_SLASHES).');};
            </script>';
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
            if(!empty($hierarchy[$currentPage['type']]['all'])) return 'all';
            else{
                if(!empty($hierarchy[$currentPage['type']][$currentPage['slug']])) return $currentPage['slug'];
            } 
        }else return null;
    }
    
}
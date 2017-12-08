<?php

namespace WPAS\Utils;

class TemplateContext{
    
    private static $current = null;
    
    public static function getContext($ready=null){
        
        if(!empty(self::$current)) return self::$current;
    
        $qo = get_queried_object();
        if(is_page()){
            if(!empty($qo->slug)) self::$current = ['type'=>'page', 'slug' => $qo->slug];
            else if(!empty($qo->post_name)) self::$current = ['type'=>'page', 'slug' => $qo->post_name];
        } 
        else if(is_singular('post')){
            self::$current = ['type'=>'post', 'slug' => $qo->post_name];
        }
        else if(is_singular()){
            self::$current = ['type'=>'custom-post', 'slug' => $qo->post_type];
        }
        else if(is_tax()){
            $qo = get_queried_object();
            self::$current = ['type'=>'taxonomy', 'slug' => $qo->slug];
        } 
        else if(is_category()){
            $qo = get_queried_object();
            self::$current = ['type'=>'category', 'slug' => $qo->slug];
        } 
        else if(is_tag()){
            $qo = get_queried_object();
            self::$current = ['type'=>'tag', 'slug' => $qo->slug];
        } 
        else if(is_archive()){
            $qo = get_queried_object();
            self::$current = ['type'=>'archive', 'slug' => $qo->slug];
        } 
        else if(is_home()){
            global $wp_query;
            if(isset($wp_query->query['pagename'])) self::$current = ['type'=>'page', 'slug' => $wp_query->query['pagename']];
            else self::$current = ['type'=>'page', 'slug' => null];
        } 
        else if(is_attachment()){
            global $wp_query;
            self::$current = ['type'=>'attachment', 'slug' => $wp_query->name];
        }
        else if(is_404()){
            self::$current = ['type'=>'404', 'slug' => 'all'];
        }
        else{
            global $wp_query;
            if(!empty($wp_query->query['pagename'])) self::$current = ['type'=>'page', 'slug' => $wp_query->query['pagename']];
            else if(!empty($wp_query->name)) self::$current = ['type'=>'page', 'slug' => $wp_query->name];
        }
        //print_r(self::$current); die();
        self::$current['template'] = str_replace('.php','',get_page_template_slug());
        return self::$current;
    }
    
    public static function matchesViewAndType($view){
        
        $pieces = self::getViewPieces($view);
        $type = 'default';
        if(is_array($pieces))
        {
            $type = strtolower($pieces[0]);
            $view = strtolower($pieces[1]);
            
        }else{
            $view = strtolower($view);
            $pieces = ['default',$pieces];
        } 
        switch($type)
        {
            case 'default': 
                if(is_page($view) || is_singular($view)) return $pieces;
            break;
            case 'page':
                if($view=='all'){
                    if(is_page()) return $pieces;
                } 
                else if(is_page($view)){
                    print_r($pieces); die();
                    return $pieces;
                } 
            break;
            case 'single': 
                if(is_singular($view)) return $pieces;
            break;
            case 'home': 
                if ( is_front_page() && is_home() ) {
                  // Default homepage
                  return $pieces;
                } elseif ( is_front_page() ) {
                  // static homepage
                  return $pieces;
                } elseif ( is_home() ) {
                  // blog page
                  return false;
                } 
              return false;
            break;
            case 'blog': 
                if ( is_front_page() && is_home() ) {
                  // Default homepage
                  return false;
                } elseif ( is_front_page() ) {
                  // static homepage
                  return false;
                } elseif ( is_home() ) {
                  // blog page
                  return $pieces;
                } 
              return false;
            break;
            case "tag": 
                if($view=='all')
                {
                    if(is_tax() || is_tag()) return $pieces;
                }
                else if(is_tax($view) || is_tag($view)) return $pieces; 
            break;
            case "category": 
                if($view=='all'){
                    if(is_tax() || is_category()) return $pieces;
                } 
                else if(is_tax($view) || is_category($view)){
                    return $pieces;
                } 
            break;
            case "template":
                if(strpos($view, '.php') == false) throw new WPASException('Your template name '.$view.' has to be a .php file name');
                if(get_page_template_slug()==$view)
                {
                    return $pieces;
                }
            break;
            case "search": 
                if(is_search()) return $pieces;
            break;
            
        }
        
        return false;
    }
    
    private static function getViewPieces($view){
        
        $pieces = explode(':',$view);
        if(count($pieces)==1) return $pieces[0];
        else if(count($pieces)==2) return [$pieces[0],$pieces[1]];
        else throw new WPASException('The view '.$view.' is invalid');
        
    }
}
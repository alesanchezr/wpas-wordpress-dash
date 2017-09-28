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
            self::$current = ['type'=>'page', 'slug' => $wp_query->query['pagename']];
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
        return self::$current;
    }
    
    public static function matchesViewAndType($view, $type='default'){
        $type = strtolower($type);
        $view = strtolower($view);
        
        switch($type)
        {
            case 'default': 
                return (is_page($view) || is_singular($view));
            break;
            case 'page':
                if($view=='all') return is_page();
                return is_page($view);
            break;
            case 'single': 
                return is_singular($view);
            break;
            case 'home': 
                if ( is_front_page() && is_home() ) {
                  // Default homepage
                  return true;
                } elseif ( is_front_page() ) {
                  // static homepage
                  return true;
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
                  return true;
                } 
              return false;
            break;
            case "tag": 
                if($view=='all') return is_tax() || is_tag();
                else return is_tax($view) || is_tag($view); 
            break;
            case "category": 
                if($view=='all') return is_tax() || is_category();
                else return is_tax($view) || is_category($view); 
            break;
            case "template":
                if(strpos($view, '.php') == false) throw new WPASException('Your template name '.$view.' has to be a .php file name');
                return is_page_template($view);
            break;
            case "search": 
                return is_search(); 
            break;
            
        }
    }
}
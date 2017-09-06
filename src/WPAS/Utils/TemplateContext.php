<?php

namespace WPAS\Utils;

class TemplateContext{
    
    private static $current = null;
    
    public static function getContext($ready){
        
        if(!empty(self::$current)) return self::$current;
    
        $qo = get_queried_object();
        if(is_page()){
            if(!empty($qo->slug)) self::$current = ['type'=>'page', 'slug' => $qo->slug];
            else if(!empty($qo->post_name)) self::$current = ['type'=>'page', 'slug' => $qo->post_name];
        } 
        else if(is_single('post')){
            self::$current = ['type'=>'post', 'slug' => $qo->post_name];
        }
        else if(is_single()){
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
            self::$current = ['type'=>'page', 'slug' => $wp_query->query['pagename']];
        } 
        else if(is_attachment()){
            global $wp_query;
            self::$current = ['type'=>'attachment', 'slug' => $wp_query->name];
        }
        else{
            global $wp_query;
            if(!empty($wp_query->query['pagename'])) self::$current = ['type'=>'page', 'slug' => $wp_query->query['pagename']];
            else if(!empty($wp_query->name)) self::$current = ['type'=>'page', 'slug' => $wp_query->name];
        }
        
        return self::$current;
    }
}
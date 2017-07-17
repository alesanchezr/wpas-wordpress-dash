<?php

namespace WPAS\Messaging;

use WPAS\Exception\WPASException;

class WPASAdminNotifier{
    
    const ERROR = 'error';
    const WARNING = 'warning';
    const SUCCESS = 'success';
    const INFO = 'info';
    public static $messages = [];
    
    static function addTransientMessage($type, $message)
    {
        if(!in_array($type,[self::ERROR,self::INFO,self::SUCCESS,self::WARNING]))
            throw new WPASException('Invalid WPASNotifier message type');
            
        $user_id = get_current_user_id();
        
        $transientMessages = get_transient( "bc_".$type."_{$user_id}" );
        if(!$transientMessages) $transientMessages = [];
        $transientMessages[] = $message;
        
        set_transient("bc_".$type."_{$user_id}", $transientMessages, 45);
        
    }
    
    static function loadTransientMessages(){
        $user_id = get_current_user_id();
        
        foreach([self::ERROR,self::INFO,self::SUCCESS,self::WARNING] as $type)
        {
            if ( $transientMessages = get_transient( "bc_".$type."_{$user_id}" ) ) {
                self::$messages[$type] = $transientMessages;
                if(count(self::$messages[$type])>0) add_action( 'admin_notices', 'WPAS\Messaging\WPASAdminNotifier::notice__message');
            }
        }
        
    }
    
    static function notice__message() {
    	
        foreach(self::$messages as $type => $messages)
        {
        	$class = 'notice is-dismissible notice-'.$type;
        	$content = $type.'! <ul>';
        	foreach($messages as $msg)  $content .= '<li>'.$msg.'</li>';
        	$content .= '</ul>';
            
            printf( '<div class="%1$s">%2$s</div>', esc_attr( $class ), $content ); 
            
            $user_id = get_current_user_id();
            delete_transient("bc_".$type."_{$user_id}");
        }
    }
    
}
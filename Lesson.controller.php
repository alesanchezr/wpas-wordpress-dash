<?php

namespace BreatheCode\Controller;

class Lesson{
   
    function ajax_create() {
        
    	// first check if data is being sent and that it is the data we want
      	if ( isset( $_POST["username"] ) && isset( $_POST["email"] ) && isset( $_POST["password"] ) ) {
    		// now set our response var equal to that of the POST var (this will need to be sanitized based on what you're doing with with it)
    		$username = $_POST["username"];
    		$password = $_POST["password"];
    		$email = $_POST["email"];
    		
            if( null == username_exists( $email ) ) {
        
              // Generate the password and create the user
              $password = wp_generate_password( 12, false );
              $user_id = wp_create_user( $username, $password, $email );
            
              // Set the nickname
              wp_update_user(
                array(
                  'ID'          =>    $user_id,
                  'nickname'    =>    $email
                )
              );
        
              // Set the role
              $user = new WP_User( $user_id );
              $user->set_role( 'student' );
            
            } // end if
        
			\BCController::ajaxSuccess(get_permalink(get_page_by_path( 'thanyou' )));
    	}
    	
        \BCController::ajaxError('There was an error in the signup process');
    }
    function ajax_edit() {
        
    	// first check if data is being sent and that it is the data we want
      	if ( isset( $_POST["username"] ) && isset( $_POST["email"] ) && isset( $_POST["password"] ) ) {
    		// now set our response var equal to that of the POST var (this will need to be sanitized based on what you're doing with with it)
    		$username = $_POST["username"];
    		$password = $_POST["password"];
    		$email = $_POST["email"];
    		
            if( null == username_exists( $email ) ) {
        
              // Generate the password and create the user
              $password = wp_generate_password( 12, false );
              $user_id = wp_create_user( $username, $password, $email );
            
              // Set the nickname
              wp_update_user(
                array(
                  'ID'          =>    $user_id,
                  'nickname'    =>    $email
                )
              );
        
              // Set the role
              $user = new WP_User( $user_id );
              $user->set_role( 'student' );
            
            } // end if
        
			\BCController::ajaxSuccess(get_permalink(get_page_by_path( 'thanyou' )));
    	}
    	
        \BCController::ajaxError('There was an error in the signup process');
    }
    function ajax_create() {
        
    	// first check if data is being sent and that it is the data we want
      	if ( isset( $_POST["username"] ) && isset( $_POST["email"] ) && isset( $_POST["password"] ) ) {
    		// now set our response var equal to that of the POST var (this will need to be sanitized based on what you're doing with with it)
    		$username = $_POST["username"];
    		$password = $_POST["password"];
    		$email = $_POST["email"];
    		
            if( null == username_exists( $email ) ) {
        
              // Generate the password and create the user
              $password = wp_generate_password( 12, false );
              $user_id = wp_create_user( $username, $password, $email );
            
              // Set the nickname
              wp_update_user(
                array(
                  'ID'          =>    $user_id,
                  'nickname'    =>    $email
                )
              );
        
              // Set the role
              $user = new WP_User( $user_id );
              $user->set_role( 'student' );
            
            } // end if
        
			\BCController::ajaxSuccess(get_permalink(get_page_by_path( 'thanyou' )));
    	}
    	
        \BCController::ajaxError('There was an error in the signup process');
    }
    function ajax_create() {
        
    	// first check if data is being sent and that it is the data we want
      	if ( isset( $_POST["username"] ) && isset( $_POST["email"] ) && isset( $_POST["password"] ) ) {
    		// now set our response var equal to that of the POST var (this will need to be sanitized based on what you're doing with with it)
    		$username = $_POST["username"];
    		$password = $_POST["password"];
    		$email = $_POST["email"];
    		
            if( null == username_exists( $email ) ) {
        
              // Generate the password and create the user
              $password = wp_generate_password( 12, false );
              $user_id = wp_create_user( $username, $password, $email );
            
              // Set the nickname
              wp_update_user(
                array(
                  'ID'          =>    $user_id,
                  'nickname'    =>    $email
                )
              );
        
              // Set the role
              $user = new WP_User( $user_id );
              $user->set_role( 'student' );
            
            } // end if
        
			\BCController::ajaxSuccess(get_permalink(get_page_by_path( 'thanyou' )));
    	}
    	
        \BCController::ajaxError('There was an error in the signup process');
    }
    

    
}
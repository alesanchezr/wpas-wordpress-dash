<?php

namespace WPAS\Roles;

class WPASRoleAccessManager {
  
  const PUBLIC_SCOPE = 'wpas_public';
  private $allowedPages = [];
  private $options = [];
  private static $restrictAll = false;
  
  public function __construct($newOptions=[]){
    
      $options = [
        'default_visibility' => 'restricted'
        ];
    
      $this->options = array_merge($this->options, $newOptions);
    
      add_action( 'wp', [$this,'redirect'] );
      add_action( 'admin-init', [$this,'redirect_admin'] );
  }
  
  private function initialize(){
    
    self::$roles = get_editable_roles();
    print_r(self::$roles); die();
    
  }
  
  public static function start()
  {
  }
 
  public function allow($role_name, $slug){
    $this->allowedPages[$role_name][$slug] = true;
  }
  
  private function getCurrentViewId(){

    if(is_page() || is_single() || is_home()){
      global $post; 
      return $post->post_name;
    }
    //else if(is_single()) return 'single';
    else return null;
  }
  
  private function isAllowed($role_name){
    if($role_name==='administrator') return true;
    
    if($this->is_login_page()) return true;
    if( !isset($this->allowedPages[$role_name]) || 
        empty($this->allowedPages[$role_name][$this->getCurrentViewId()]))
          return false;
    if($this->allowedPages[$role_name][$this->getCurrentViewId()] == true) return true;
  }
  
  
  public function redirect() {
    
      $roleName = $this->getCurrentRole();

      if (!$this->isAllowed($roleName)) {
          wp_redirect( wp_login_url() ); exit();
      } // if $role_name
  }
  
  public function redirect_admin() {
    
      self::initialize();
   
      if ( ! defined( 'DOING_AJAX' ) ) {
   
          $roleName = $this->getCurrentRole();
   
          if ( 'subscriber' === $roleName ) {
              wp_redirect( get_home_url() );
          } // if $role_name
   
      } // if DOING_AJAX
   
  } // cm_redirect_users_by_role
  
  private function getCurrentRole(){
      $user   = wp_get_current_user();
      return ($user->roles[0]) ? $user->roles[0] : self::PUBLIC_SCOPE;
  }

  private function is_login_page() {
    return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
  }
  
}
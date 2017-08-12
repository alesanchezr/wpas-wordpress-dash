<?php

namespace WPAS\Roles;
use WPAS\Exception\WPASException;

class WPASRoleAccessManager{
  
  const PUBLIC_SCOPE = 'wpas_public';
  private $allowedPages = [];
  private $options = [];
  private $newOptions = [];
  private static $restrictAll = false;
  
  public function __construct($newOptions=[]){
    
      $this->getOptions();
      add_action( 'wp', [$this,'redirect'] );
      add_action( 'admin-init', [$this,'redirect_admin'] );
      //add_action( 'init', [$this,'initialize'] );
  }
  
  private function getOptions(){
      $this->options = [
        'default_visibility' => 'restricted',
        'redirect_url' => wp_login_url()
        ];
    
      foreach($this->newOptions as $key => $value)
        if(!empty($value)) $this->options[$key] = $value;
        
  }
  
  public function allowDefaultAccess($contexts){
    $this->allow(self::PUBLIC_SCOPE,$contexts);
  }
  
  public function allowAccessFor($role, $contexts){
    
    if(!$role) throw new WPASException('Invalid role object');
    
    $this->allow($role->getName(),$contexts);
  }
 
  private function allow($role, $contexts){

    if($role=='administrator') throw new WPASException('The administrator role can not be restricted');
    
    foreach($contexts as $context => $slugs)
    {
      if($context == 'parent'){
          $this->allowedPages[$role]['parent'] = $slugs;
          
          $parentSlugs = $this->allowedPages[$slugs->getName()];
          $auxContext = [];
          foreach($parentSlugs as $key => $slugs){
            if(!isset($auxContext[$key])) $auxContext[$key] = [];
            if($key=='parent') $auxContext[$key] = $slugs;
            else foreach($slugs as $slug => $bool) $auxContext[$key][] = $slug;
          }
          //if(!is_callable([$slugs, 'getName'])) print_r($auxContext); die();
          //if($role=='teacher') { print_r($auxContext); die(); }
          $this->allow($role,$auxContext);
          continue;
      }
      else $this->validateContext($context,$slugs);
      
      foreach($slugs as $slug)
      {
        if(!isset($this->allowedPages[$role])) $this->allowedPages[$role] = [];
        if(!isset($this->allowedPages[$role][$context])) $this->allowedPages[$role][$context] = [];
        $this->allowedPages[$role][$context][$slug] = true;
      }
    }
      
    return true;
  }
  
  private function validateContext($contextName, $slugs){
    if(!in_array($contextName,['page','post','category','tag','taxonomy'])) throw new WPASException('The context "'.$contextName.'" is invalid');
    
    if(!is_array($slugs)) throw new WPASException('The value for the context "'.$contextName.'" needs to be an array');
    
    return true;
  }
  
  private function getCurrentViewId(){

    global $post; 
    if(is_page()) return      ['type'=>'page', 'slug' => $post->post_name];
    else if(is_singular()) return  ['type'=>'post', 'slug' => $post->post_name];
    else if(is_category()){
      $qo = get_queried_object();
      return  ['type'=>'category', 'slug' => $qo->slug];
    } 
    else if(is_tag()){
      $qo = get_queried_object();
      return  ['type'=>'tag', 'slug' => $qo->slug];
    } 
    else if(is_home()) return      ['type'=>'page', 'slug' => $post->post_name];
    else return null;
  }
  
  public function isAllowed($role_name, $currentContext=null){

    if($role_name==='administrator') return true;
    if($this->is_login_page()) return true;
    
    if(!$currentContext) $currentContext = $this->getCurrentViewId();
    if( !isset($this->allowedPages[$role_name]) || 
        empty($this->allowedPages[$role_name][$currentContext['type']][$currentContext['slug']]))
          return false;
    if($this->allowedPages[$role_name]['parent'] && $this->allowedPages[$role_name]['parent']->getName()=='administrator') return true;
    if($this->allowedPages[$role_name][$currentContext['type']][$currentContext['slug']] == true) return true;
  }
  
  
  public function redirect() {
    
      if(!isset($this->allowedPages[self::PUBLIC_SCOPE])) throw new WPASException('You need to define at least one allowDefaultAccess slug');
    
      $roleName = $this->getCurrentRole();

      if (!$this->isAllowed($roleName)) {
        //echo 'not allowed'; print_r($this->allowedPages); die();
          wp_redirect( $this->options['redirect_url'] ); exit();
      }
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
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
    
      $this->getOptions($newOptions);
      add_action( 'wp', [$this,'redirect'] );
      add_action( 'admin-init', [$this,'redirect_admin'] );
      //add_action( 'init', [$this,'initialize'] );
      if(!empty($this->options['private_url'])) add_filter( 'login_redirect', [$this,'login_redirect'], 10, 3 );
  }
  
  private function getOptions($newOptions){
      $this->options = [
        'default_visibility' => 'restricted',
        'message' => 'The requested URL is restricted',
        'private_url' => null,
        'redirect_url' => wp_login_url()
        ];
      
      foreach($newOptions as $key => $value)
        if(!empty($value)) $this->options[$key] = $value;
        
  }
  
  function login_redirect( $redirect_to, $request, $user ) {
  	//is there a user to check?
  	if ( isset( $user->roles ) && is_array( $user->roles ) ) {
  		//check for admins
  		if ( in_array( 'administrator', $user->roles ) ) {
  			// redirect them to the default place
  			return $redirect_to;
  		} else {

  			if(!empty($this->options['private_url'])) return $this->options['private_url'];
  			else return home_url();
  			
  		}
  	} else {
  		return $redirect_to;
  	}
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
    
    if(!is_array($contexts) && $contexts!=='all')  throw new WPASException('The roel context must be "all" or an array including all the contexts');
    else if(!is_array($contexts) && $contexts==='all') $this->allowedPages[$role] = 'all';
    else foreach($contexts as $context => $slugs)
    {
      if($context == 'parent'){
          $this->allowedPages[$role]['parent'] = $slugs;
          
          $parentSlugs = $this->allowedPages[$slugs->getName()];
          $auxContext = [];
          if($parentSlugs==='all') $this->allow($role,'all');
          else 
          {
            foreach($parentSlugs as $key => $slugs){
              if(!isset($auxContext[$key])) $auxContext[$key] = [];
              if($key=='parent') $auxContext[$key] = $slugs;
              else if($slugs==='all') $auxContext[$key] = 'all';
              else foreach($slugs as $slug => $bool) $auxContext[$key][] = $slug;
              //if(!is_callable([$slugs, 'getName'])) print_r($auxContext); die();
              //if($role=='teacher') { print_r($auxContext); die(); }
              $this->allow($role,$auxContext);
            }
          }
          continue;
      }
      else $this->validateContext($context,$slugs);
      
      if($slugs==='all') $this->allowedPages[$role][$context] = 'all';
      else foreach($slugs as $slug)
      {
        if(!isset($this->allowedPages[$role])) $this->allowedPages[$role] = [];
        if(!isset($this->allowedPages[$role][$context])) $this->allowedPages[$role][$context] = [];
        
        if($this->allowedPages[$role][$context] === 'all') return true;
        else $this->allowedPages[$role][$context][$slug] = true;
      }
    }
      
    return true;
  }
  
  private function validateContext($contextName, $slugs){
    if(!in_array($contextName,['page','post','category','tag','taxonomy','archive'])) throw new WPASException('The context "'.$contextName.'" is invalid');
    
    if(!is_array($slugs) && $slugs!='all') throw new WPASException('The value for the context "'.$contextName.'" needs to be an array or the word "all"');
    
    return true;
  }
  
  private function getCurrentViewId(){
    
    global $post; 
    if(is_page()) return      ['type'=>'page', 'slug' => $post->post_name];
    else if(is_single()){
      //echo 'post!'; die();
      return  ['type'=>'post', 'slug' => $post->post_name];
    }
    else if(is_tax()){
      echo 'taxonomy!'; die();
      return  ['type'=>'taxonomy', 'slug' => $qo->slug];
    } 
    else if(is_category()){
      //echo 'category!'; die();
    //print_r($this->allowedPages[$role_name]); die();
      $qo = get_queried_object();
      return  ['type'=>'category', 'slug' => $qo->slug];
    } 
    else if(is_tag()){
      //echo 'tag!'; die();
      $qo = get_queried_object();
      return  ['type'=>'tag', 'slug' => $qo->slug];
    } 
    else if(is_archive()){
      //echo 'archive!'; die();
      $qo = get_queried_object();
      return  ['type'=>'archive', 'slug' => $qo->slug];
    } 
    else if(is_home()) return ['type'=>'page', 'slug' => $post->post_name];
    
    //echo 'Ending'; die();
    return null;
  }
  
  public function isAllowed($role_name, $currentContext=null){

    if($role_name==='administrator') return true;
    if($this->is_login_page()) return true;
    if($this->allowedPages[$role_name]==='all') return true;
    if(!$currentContext) $currentContext = $this->getCurrentViewId();
    
    //print_r($this->allowedPages[$role_name]); die();
    
    if( !isset($this->allowedPages[$role_name])) return false;
    
    if($this->allowedPages[$role_name][$currentContext['type']] === 'all') return true;
    else if(empty($this->allowedPages[$role_name][$currentContext['type']][$currentContext['slug']])) return false;
    
    if(isset($this->allowedPages[$role_name]['parent']) && $this->allowedPages[$role_name]['parent']->getName()=='administrator') return true;
    if($this->allowedPages[$role_name][$currentContext['type']][$currentContext['slug']] == true) return true;
  }
  
  
  public function redirect() {
    
      if(!isset($this->allowedPages[self::PUBLIC_SCOPE])) throw new WPASException('You need to define at least one allowDefaultAccess slug');
    
      $roleName = $this->getCurrentRole();

      if (!$this->isAllowed($roleName)) {
          //echo 'not allowed'; print_r($this->allowedPages); die();
          wp_redirect( $this->options['redirect_url'] . '?message=' . urlencode($this->options['message']) ); exit();
      }
  }
  
  public function redirect_admin() {
    
      self::initialize();
   
      if ( ! defined( 'DOING_AJAX' ) ) {
   
          $roleName = $this->getCurrentRole();
   
          if ( 'subscriber' === $roleName ) {
              wp_redirect( $this->options['redirect_url'] );
          } // if $role_name
   
      } // if DOING_AJAX
   
  } // cm_redirect_users_by_role
  
  private function getCurrentRole(){
      $user   = wp_get_current_user();
      $totalRoles = count($user->roles);
      if($totalRoles>1) throw new WPASException('The WPASRoleAccessManager class does not support multiroles, the user '.$user->name.' has '.$totalRoles);
      
      return ($user->roles[0]) ? $user->roles[0] : self::PUBLIC_SCOPE;
  }

  private function is_login_page() {
    return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
  }
  
}
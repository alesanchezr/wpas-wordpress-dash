<?php

namespace WPAS\Roles;

class WPASRole {
  
  private $name = null;
  private $role = [];
  private $options = array( 'read' => true );
  
  public function __construct($roleName){
        $this->name = $roleName;
        $this->role = get_role($roleName);
        
        if($this->role) return $this;
        else return $this->createRole($roleName);
  }
  
  public function getName(){ return $this->name; }
  
  private function createRole($slug){
      
        $name = $this->getNameFromSlug($slug);
        $result = add_role(
            $slug,
            __( $name ),
            $this->options
        );
        
        if(!$result) throw new WPASException('Implsible to create the role '.$slug);
        return $this;
  }
  
  function getNameFromSlug($slug){
      $output = preg_split( "/(-|_)/", $slug );
      $output = ucwords(implode(" ",$output));
      return $output;
  }
  
}
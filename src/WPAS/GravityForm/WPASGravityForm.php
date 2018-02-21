<?php

namespace WPAS\GravityForm;

use WPAS\GravityForm\Fields\BaseGravityFormField;
use WPAS\Controller\WPASController;
use GFForms;

class WPASGravityForm{
    
    private $fields = [];
    
    function __construct($settings){

        GFForms::include_addon_framework();
        
        if(!empty($settings['submit-button-class']))
        
        $customSubmit = new CustomSubmitButton();
        if(!empty($settings['fields'])){
            
            $this->fields = $settings['fields'];
            add_filter( 'gform_add_field_buttons', [$this,'add_fields'] );
        } 
        
        if(!empty($settings['populate-current-language'])) add_filter("gform_field_value_wpas_language", [$this,'get_language_value']);
        if(!empty($settings['populate-adwords-gclid'])) add_filter("gform_field_value_wpas_gclid", [$this,'get_gclid_value']);
        
        if(!empty($settings['bootstrap4-styles'])) add_filter( 'gform_field_container', [$this,'add_bootstrap_container_class'], 10, 6 );
    }
    
    function add_bootstrap_container_class( $field_container, $field, $form, $css_class, $style, $field_content ) {
      $id = $field->id;
      $field_id = is_admin() || empty( $form ) ? "field_{$id}" : 'field_' . $form['id'] . "_$id";
      return '<li id="' . $field_id . '" class="' . $css_class . ' form-group">{FIELD_CONTENT}</li>';
    }
    
    public function add_fields($field_groups){
        
        for($i=0; $i<count($this->fields); $i++){
            $field = $this->createField($this->fields[$i]['type'],$this->fields[$i]['label']);
            $this->fields[$i] = $field->getMetaInfo();
        }
        
        array_push($field_groups,[
        'name' => 'wpas_fields', 
        'label' => __( 'WordPress Dash Fields', 'gravityforms' ), 
        'fields' => $this->fields, 
        'tooltip_class' => 'tooltip_bottomleft'
        ]);
        
        return $field_groups;
    }
    
    private function createField($type, $value){
        return new BaseGravityFormField($type,$value);
    }
    
    public function get_language_value($value){
        
        if(function_exists('pll_current_language')){
            return pll_current_language();
        }
        else{
            return null;
        }
        
    }
    public function get_gclid_value($value){
        
        $WPAS_APP = WPASController::get_context();
        
        if(isset($_GET['gclid'])) return $_GET['gclid'];
        else if(isset($WPAS_APP['gclid'])) return $WPAS_APP['gclid'];
        else return null;
        
    }
    
    
}
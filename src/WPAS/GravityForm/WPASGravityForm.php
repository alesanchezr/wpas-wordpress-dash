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
        
        add_filter("gform_field_value", [$this,'populate_gform_fields'],3,10);
        
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
    
    private function getPllLanguageValue(){
        
        if(function_exists('pll_current_language')){
            return pll_current_language();
        }
        else{
            return null;
        }
        
    }
    
    function populate_gform_fields( $value, $field, $name ) {
     
        $WPAS_APP = WPASController::get_context();
        $values = [];

        //check if its an object
        $sfield = $this->serializeDynamicKey($name);
        if($sfield && $sfield['type']=='object')
        {
            if(is_object($WPAS_APP[$sfield['key']]))
            {
                $aux = (array) $WPAS_APP[$sfield['key']];
                if(isset($aux[$sfield['key_pieces'][1]])) $values[$name] = $aux[$sfield['key_pieces'][1]];
                else $values[$name] = 'undefined';
            }
            
        }
        else if($sfield && $sfield['type']=='primitive'){

            if(isset($_GET[$sfield['key']])) $values['wpas_'.$sfield['key']] = $_GET[$sfield['key']];
            else if($sfield['key'] == 'lang'){
                $values['wpas_lang'] = $this->getPllLanguageValue();
                $values['wpas_language'] = $this->getPllLanguageValue();
            }
            else if(isset($WPAS_APP[$sfield['key']]))
            {
                
            }else if($value!='') $values['wpas_'.$sfield['key']] = $value;
            else $values['wpas_'.$sfield['key']] = 'undefined';
        }
        else $values[$name] = 'undefined';

        return isset( $values[ $name ] ) ? $values[ $name ] : $value;
    }
    
    private function serializeDynamicKey($originalName){
        $name = str_replace("wpas_language",'wpas_lang',$originalName);
        $key = str_replace("wpas_",'',$name);
        
        $keypieces = explode(".",$key);
        //if has more than one piece its a nested object
        if(count($keypieces)>1)
        {
            return [
                'type'=> 'object',
                'key'=> $keypieces[0],
                'key_pieces' => $keypieces,
                'original_name'=> $originalName
            ];
        }
        else if(count($keypieces)==1)
        {
            return [
                'type'=> 'primitive',
                'key'=> $key,
                'original_name'=> $originalName
            ];
        }
        else return false;
    }
    
}
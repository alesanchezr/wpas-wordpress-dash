<?php

namespace WPAS\GravityForm;

use WPAS\GravityForm\Fields\BaseGravityFormField;
use WPAS\Controller\WPASController;
use GFForms;

class WPASGravityForm{
    
    private $fields = [];
    
    function __construct($settings){

        GFForms::include_addon_framework();
        
        if(!empty($settings['submit-button-class'])) $customSubmit = new CustomSubmitButton();
        
        if(!empty($settings['fields'])){
            
            $this->fields = $settings['fields'];
            $this->add_fields();
            
            add_filter( 'gform_add_field_buttons', [$this,'add_fields_group'] );
            add_action( "gform_register_init_scripts", [$this,"display_form_scripts"] );
        }
        
        add_filter("gform_field_value", [$this,'populate_gform_fields'],3,10);
        
        if(!empty($settings['bootstrap4-styles'])) add_filter( 'gform_field_container', [$this,'add_bootstrap_container_class'], 10, 6 );
    }
    
    function add_bootstrap_container_class( $field_container, $field, $form, $css_class, $style, $field_content ) {
      $id = $field->id;
      $field_id = is_admin() || empty( $form ) ? "field_{$id}" : 'field_' . $form['id'] . "_$id";
      return '<li id="' . $field_id . '" class="' . $css_class . ' form-group">{FIELD_CONTENT}</li>';
    }
    
    private function classFactory($type){
        if(!preg_match('/^[a-z][-a-z0-9]*$/', $type)) throw new WPASException('The field type must start with a letter and contain only letters or -');
        
        $classNameParts = explode("-", $type);
        $classNameParts = array_map(function($part){ 
            return ucfirst($part); 
        },$classNameParts);
        $className = implode($classNameParts);
        
        return 'WPAS\\GravityForm\\Fields\\'.$className.'Field';
    }
    
    private function getFieldMetaInfo($type, $label){
        return [
            'class' => 'button', 
            'data-type' => $type, 
            'value' => __( $label, 'gravityforms' ),
            'onclick'   => "StartAddField('".$type."');"
        ];
    }
    
    public function add_fields(){
        
        for($i=0; $i<count($this->fields); $i++){
            $fieldClassName = $this->classFactory($this->fields[$i]['type']);
            \GF_Fields::register( new $fieldClassName() );
        }
        
    }
    
    public function add_fields_group($field_groups){
        
        for($i=0; $i<count($this->fields); $i++){
            $this->fields[$i] = $this->getFieldMetaInfo($this->fields[$i]['type'],$this->fields[$i]['label']);
        }
        
        array_push($field_groups,[
        'name' => 'wpas_fields', 
        'label' => __( 'WordPress Dash Fields', 'gravityforms' ), 
        'fields' => $this->fields, 
        'tooltip_class' => 'tooltip_bottomleft'
        ]);
        
        return $field_groups;
    }
    
    public function display_form_scripts($form){
            //Add all textarea settings to the "TOS" field plus custom "tos_setting"
            // fieldSettings["tos"] = fieldSettings["textarea"] + ", .tos_setting"; // this will show all fields that Paragraph Text field shows plus my custom setting
            
            // from forms.js; can add custom "tos_setting" as well
            //fieldSettings["tos"] = ".label_setting, .description_setting, .admin_label_setting, .size_setting, .default_value_textarea_setting, .error_message_setting, .css_class_setting, .visibility_setting, .tos_setting"; //this will show all the fields of the Paragraph Text field minus a couple that I didn’t want to appear.
            
            //binding to the load field settings event to initialize the checkbox
            //$(document).bind("gform_load_field_settings", function(event, field, form){
            //        jQuery("#field_tos").attr("checked", field["field_tos"] == true);
            //        $("#field_tos_value").val(field["tos"]);
            //});
            $script = "\n\n 
                    jQuery('.wpas-button-group .wpas-button-group-btn').click(function(evt){
                    jQuery('.wpas-button-group .wpas-button-group-btn').removeClass('card-inverse');
                    jQuery(this).addClass('card-inverse');
                    let value = jQuery(this).attr('data-value');
                    $(jQuery(this).parent().attr('data-target')).val(value);
                    evt.preventDefault();
                    return false;
                 });";
        \GFFormDisplay::add_init_script( $form['id'], 'format_money', \GFFormDisplay::ON_PAGE_RENDER, $script );
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
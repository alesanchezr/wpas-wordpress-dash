<?php

namespace WPAS\GravityForm\Fields;

class BaseGravityFormField{
    
    private $label;
    private $type;
    
    function __construct($type, $label){
        
        $this->type = $type;
        $this->label = $label;
        
        add_filter( 'gform_field_type_title', [$this,'render_label'], 10, 2);
        add_filter( 'gform_field_input', [$this,'render_html'], 10, 5 );
        add_action( "gform_editor_js", [$this,"display_script"] );
        
    }
    
    public function getMetaInfo(){
        return [
            'class' => 'button', 
            'data-type' => $this->type, 
            'value' => __( $this->label, 'gravityforms' ),
            'onclick'   => "StartAddField('".$this->type."');"
        ];
    }
    
    public function render_label( $title, $type ){
        if ( $type == $this->type ) 
        {
            echo $this->label; die();
            return __( $this->label , 'gravityforms' );
        }
    }
    
    private function render_html( $input, $field, $value, $lead_id, $form_id ) {
        if ( $field["type"] == $this->type ) {
            
            $input_name = $form_id .'_' . $field["id"];
            //$tabindex = GFCommon::get_tabindex();
            $css = isset( $field['cssClass'] ) ? $field['cssClass'] :'';
            
            
            $input = '<div class="btn-group '.$css.'" role="group" aria-label="Basic example">
                          <button type="button" class="btn btn-secondary">Left</button>
                          <button type="button" class="btn btn-secondary">Middle</button>
                          <button type="button" class="btn btn-secondary">Right</button>
                          <input type="hidden" name="'.$input_name.'" value="'.$value.'" />
                        </div>';
        }
        return $input;
    }
    
    function display_script(){
        ?>
        <script type=’text/javascript’>
        jQuery(document).ready(function($) {
            //Add all textarea settings to the "TOS" field plus custom "tos_setting"
            // fieldSettings["tos"] = fieldSettings["textarea"] + ", .tos_setting"; // this will show all fields that Paragraph Text field shows plus my custom setting
            
            // from forms.js; can add custom "tos_setting" as well
            fieldSettings["tos"] = ".label_setting, .description_setting, .admin_label_setting, .size_setting, .default_value_textarea_setting, .error_message_setting, .css_class_setting, .visibility_setting, .tos_setting"; //this will show all the fields of the Paragraph Text field minus a couple that I didn’t want to appear.
            
                //binding to the load field settings event to initialize the checkbox
            $(document).bind("gform_load_field_settings", function(event, field, form){
                    jQuery("#field_tos").attr("checked", field["field_tos"] == true);
                    $("#field_tos_value").val(field["tos"]);
            });
        });
        
        </script>
        <?php
    }
    
}
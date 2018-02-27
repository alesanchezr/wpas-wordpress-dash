<?php

namespace WPAS\GravityForm\Fields;

class AlertMessageField extends \GF_Field{
    
    public $type = 'alert-message';
    
    
    public function get_form_editor_field_settings(){
        return array(
            'conditional_logic_field_setting',
            'description_setting',
            'css_class_setting',
        );
    }
    
    public function get_form_editor_button() {
        return array(
            'group' => 'wpas_fields',
            'text'  => $this->get_form_editor_field_title(),
        );
    }
    public function get_form_editor_field_title() {
        return esc_attr__( 'Alert Message', 'gravityforms' );
    }
    
    public function get_field_input( $form, $value = '', $entry = null ) {
        $form_id         = $form['id'];
        $is_entry_detail = $this->is_entry_detail();
        $id              = (int) $this->id;
        
        $css = isset( $this->cssClass ) ? $this->cssClass :'';
        $html = '<div class="alert '.$css.'">'.$this->description.'</div>';
                    
        return $html;
    }
    public function get_field_content( $value, $force_frontend_label, $form ) {
        $form_id         = $form['id'];
        $admin_buttons   = $this->get_admin_buttons();
        $is_entry_detail = $this->is_entry_detail();
        $is_form_editor  = $this->is_form_editor();
        $is_admin        = $is_entry_detail || $is_form_editor;
        $field_label     = $this->get_field_label( $force_frontend_label, $value );
        $field_id        = $is_admin || $form_id == 0 ? "input_{$this->id}" : 'input_' . $form_id . "_{$this->id}";
        
        if(!$is_admin) $field_content = '{FIELD}';
        else{
            $field_content = sprintf( "%s{FIELD}", $admin_buttons );
        }
        
        return $field_content;
    }
}
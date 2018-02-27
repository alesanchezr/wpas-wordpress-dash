<?php

namespace WPAS\GravityForm\Fields;

use WPAS\Utils\WPASException;

class ButtonGroupField extends \GF_Field{
    
    public $type = 'button-group';
    
    public function get_form_editor_button() {
        return array(
            'group' => 'wpas_fields',
            'text'  => $this->get_form_editor_field_title(),
        );
    }
    public function get_form_editor_field_title() {
        return esc_attr__( 'Button Group', 'gravityforms' );
    }
    
    //execute some javascript for the field on the wordpress administration interface
    public function get_form_editor_inline_script_on_page_render(){
        //return "your scriipt here";
    }
    
    public function get_field_input( $form, $value = '', $entry = null ) {
        $form_id         = $form['id'];
        $is_entry_detail = $this->is_entry_detail();
        $is_form_editor  = $this->is_form_editor();
        $is_admin        = $is_entry_detail || $is_form_editor;
        $id              = (int) $this->id;
        $field_id        = $is_admin || $form_id == 0 ? "input_{$this->id}" : 'input_' . $form_id . "_{$this->id}";
        
        $css = isset( $this->cssClass ) ? $this->cssClass :'';

        $html = '<label class="gfield_label" for="'.$field_id.'">'.$this->label.'</label>';
        $html .= '<div class="wpas-button-group card-columns '.$css.'" data-target="#'.$field_id.'">';
        if($this->choices) foreach ($this->choices as $c){
            $choice_selected = ($c['value'] == $value);
            $html .= '<div href="#" class="card wpas-button-group-btn '.($choice_selected ? 'card-inverse':'').'" data-value="'.$c['value'].'">'.$c['text'].'</div>';
        } 
        $html .= '</div>';
        $html .= '<input type="hidden" name="input_'.$id.'" id="'.$field_id.'" value="'.$value.'" />';
                    
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
        
        $validation_message = ( $this->failed_validation && ! empty( $this->validation_message ) ) ? sprintf( "<div class='gfield_description validation_message'>%s</div>", $this->validation_message ) : '';
        
        if(!$is_admin) $field_content = '{FIELD}'.$validation_message;
        else{
            $field_content = sprintf( "%s{FIELD}", $admin_buttons );
        }
        
        return $field_content;
    }
    
	function get_form_editor_field_settings() {
		return array(
			'conditional_logic_field_setting',
			'prepopulate_field_setting',
			'error_message_setting',
			'enable_enhanced_ui_setting',
			'label_setting',
			'label_placement_setting',
			'admin_label_setting',
			'size_setting',
			'choices_setting',
			'rules_setting',
			'placeholder_setting',
			'default_value_setting',
			'visibility_setting',
			'duplicate_setting',
			'description_setting',
			'css_class_setting',
		);
	}

	public function is_conditional_logic_supported() {
		return true;
	}

	public function get_choices( $value ) {
		return GFCommon::get_select_choices( $this, $value );
	}

	public function get_value_entry_list( $value, $entry, $field_id, $columns, $form ) {
		return GFCommon::selection_display( $value, $this, $entry['currency'] );
	}
	
	public function get_value_merge_tag( $value, $input_id, $entry, $form, $modifier, $raw_value, $url_encode, $esc_html, $format, $nl2br ) {
		
		echo $input_id; die();
		$use_value       = $modifier == 'value';
		$use_price       = in_array( $modifier, array( 'price', 'currency' ) );
		$format_currency = $modifier == 'currency';

		if ( is_array( $raw_value ) && (string) intval( $input_id ) != $input_id ) {
			$items = array( $input_id => $value ); //float input Ids. (i.e. 4.1 ). Used when targeting specific checkbox items
		} elseif ( is_array( $raw_value ) ) {
			$items = $raw_value;
		} else {
			$items = array( $input_id => $raw_value );
		}

		$ary = array();

		foreach ( $items as $input_id => $item ) {
			if ( $use_value ) {
				list( $val, $price ) = rgexplode( '|', $item, 2 );
			} elseif ( $use_price ) {
				list( $name, $val ) = rgexplode( '|', $item, 2 );
				if ( $format_currency ) {
					$val = GFCommon::to_money( $val, rgar( $entry, 'currency' ) );
				}
			} elseif ( $this->type == 'post_category' ) {
				$use_id     = strtolower( $modifier ) == 'id';
				$item_value = GFCommon::format_post_category( $item, $use_id );

				$val = RGFormsModel::is_field_hidden( $form, $this, array(), $entry ) ? '' : $item_value;
			} else {
				$val = RGFormsModel::is_field_hidden( $form, $this, array(), $entry ) ? '' : RGFormsModel::get_choice_text( $this, $raw_value, $input_id );
			}

			$ary[] = GFCommon::format_variable_value( $val, $url_encode, $esc_html, $format );
		}

		return GFCommon::implode_non_blank( ', ', $ary );
	}

	public function get_value_entry_detail( $value, $currency = '', $use_text = false, $format = 'html', $media = 'screen' ) {
		$return = esc_html( $value );
		$selection = \GFCommon::selection_display( $return, $this, $currency, $use_text );
		return $selection;
	}

	public function get_value_export( $entry, $input_id = '', $use_text = false, $is_csv = false ) {
		if ( empty( $input_id ) ) {
			$input_id = $this->id;
		}

		$value = rgar( $entry, $input_id );

		return $is_csv ? $value : GFCommon::selection_display( $value, $this, rgar( $entry, 'currency' ), $use_text );
	}
}
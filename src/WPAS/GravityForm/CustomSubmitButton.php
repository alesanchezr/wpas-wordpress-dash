<?php

namespace WPAS\GravityForm;

class CustomSubmitButton extends \GFAddOn {
	protected $_version = '1.0'; 
    protected $_min_gravityforms_version = '1.9.12.16';
    protected $_slug = 'customize-submit-button-for-gravity-forms';
    protected $_path = 'customize-submit-button-for-gravity-forms/customize-submit-button-for-gravity-forms.php';
    protected $_full_path = __FILE__;
    protected $_title = 'Customize Submit Button for Gravity Forms';
    protected $_short_title = 'GF Custom Submit Button';

    public function init() {  
        parent::init();            

    }

    public function return_button_css( $form ) {
        if ( $form['button']['type'] == 'image' ) {
            return 'gform_button gform_image_button';
        } 
        return 'gform_button button';
    }

    public function filter_form_button_settings($form_settings, $form) {

        if ( isset( $form['button']['button_css_class'] ) ) {
            $button_css = $form['button']['button_css_class'];
            if ( empty($button_css) ) {
                $button_css = $this->return_button_css( $form );
            }
        } else {
            $button_css = $this->return_button_css( $form );
        }

        $subsetting_open  = '
        <tr>
            <td colspan="2" class="gf_sub_settings_cell">
                <div class="gf_animate_sub_settings">
                    <table>
                        <tr>';
        $subsetting_close = '
                        </tr>
                    </table>
                </div>
            </td>
        </tr>';

        $form_button_type     = rgars( $form, 'button/type' );
        $text_button_checked  = '';
        $image_button_checked = '';
        $html_button_checked = '';
        $text_style_display   = '';
        $image_style_display  = '';
        $html_style_display = '';
        if ( $form_button_type == 'text' ) {
            $text_button_checked = 'checked="checked"';
            $image_style_display = 'display:none;';
            $html_style_display = 'display:none;';
        } else if ( $form_button_type == 'image' ) {
            $image_button_checked = 'checked="checked"';
            $text_style_display   = 'display:none;';
            $html_style_display = 'display:none;';
        } else if ( $form_button_type == 'html' ) {
            $html_button_checked = 'checked="checked"';
            $image_style_display = 'display:none;';
            $text_style_display   = 'display:none;';
        }

        // form button radio buttons
        $form_settings['Form Button']['form_button_type'] = '
        <tr>
            <th>
                ' . __( 'Input type', 'gravityforms' ) . '
            </th>
            <td>

                <input type="radio" id="form_button_text" name="form_button" value="text" onclick="GFSBToggleButton();" ' . $text_button_checked . ' />
                <label for="form_button_text" class="inline">' .
            __( 'Text', 'gravityforms' ) .
            '</label>

            &nbsp;&nbsp;

            <input type="radio" id="form_button_image" name="form_button" value="image" onclick="GFSBToggleButton();" ' . $image_button_checked . ' />
                <label for="form_button_image" class="inline">' .
            __( 'Image', 'gravityforms' ) . '</label>

            &nbsp;&nbsp;
            <input type="radio" id="form_button_html" name="form_button" value="html" onclick="GFSBToggleButton();" ' . $html_button_checked . ' />
            <label for="form_button_html" class="inline">' .
            __( 'Button', 'customize-submit-button-for-gravity-forms' ) . '</label>

            </td>
        </tr>';

        //form button text
        $form_settings['Form Button']['form_button_text'] = $subsetting_open . '
        <tr id="form_button_text_setting" class="child_setting_row" style="' . $text_style_display . '">
            <th>
                ' .
            __( 'Button text', 'gravityforms' ) . ' ' .
            gform_tooltip( 'form_button_text', '', true ) .
            '
        </th>
        <td>
            <input type="text" id="form_button_text_input" name="form_button_text_input" class="fieldwidth-3" value="' . esc_attr( rgars( $form, 'button/text' ) ) . '" />
            </td>
        </tr>';

        // form button image path and html5 button input
        $form_settings['Form Button']['form_button_image_path'] = '
        <tr id="form_button_image_path_setting" class="child_setting_row" style="' . $image_style_display . '">
            <th>
                    ' .
                __( 'Button image path', 'gravityforms' ) . '  ' .
                gform_tooltip( 'form_button_image', '', true ) .
                '
            </th>
            <td>
                <input type="text" id="form_button_image_url" name="form_button_image_url" class="fieldwidth-3" value="' . esc_attr( rgars( $form, 'button/imageUrl' ) ) . '" />
            </td>
        </tr>
        <tr id="form_button_html_setting" class="child_setting_row" style="' . $html_style_display . '">
            <th>
                <label for="form_button_html_input" style="display:block;">' . 
                    __( 'HTML Button Text', 'customize-submit-button-for-gravity-forms' ) . ' ' .
                    gform_tooltip( 'form_button_html_input', '', true ) .
                '</label>
            </th>
            <td>
                <input type="text" id="form_button_html_input" name="form_button_html_input" class="fieldwidth-3" value="' . esc_attr( rgars( $form, 'button/html' ) ) . '" />
            </td>
        </tr>' . $subsetting_close;

        // add CSS Button Class field
        $form_settings['Form Button']['form_button_image_path'] .= '
        <tr>
            <th>
                <label for="button_css_class" style="display:block;">' .
            __( 'Submit Button CSS Classes', 'customize-submit-button-for-gravity-forms' ) . ' ' .
            gform_tooltip( 'button_css_class', '', true ) .
            '</label>
        </th>
        <td>
            <input type="text" id="button_css_class" name="button_css_class" class="fieldwidth-3" value="' . $button_css . '" />
            </td>
        </tr>';

    	return $form_settings;
    }

    public function save_form_button_settings( $form ) {
          
        $form['button']['type']     = rgpost( 'form_button' );
        $form['button']['text']     = rgpost( 'form_button' ) == 'text' ? rgpost( 'form_button_text_input' ) : '';
        $form['button']['imageUrl'] = rgpost( 'form_button' ) == 'image' ? rgpost( 'form_button_image_url' ) : '';
        $form['button']['html']     = rgpost( 'form_button' ) == 'html' ? rgpost( 'form_button_html_input' ) : '';

        $button_css = rgpost( 'button_css_class' );
        if ( $button_css == 'gform_button button' || $button_css == 'gform_button gform_image_button') {
            $button_css = '';
        }

        $form['button']['button_css_class'] = strip_tags( str_replace(array('"', "'"), '', $button_css) );

        return $form;
    }

    public function disable_form_settings_sanitization() {
        return true;
    }

    public function add_tooltips( $tooltips ) {
        $tooltips['form_button_html_input'] = '<h6>' . __( 'Form Button Element Text', 'customize-submit-button-for-gravity-forms' ) . '</h6>' . __( 'Enter the text you would like to appear on the form submit button element. HTML tags are allowed.', 'customize-submit-button-for-gravity-forms' );
        $tooltips['button_css_class'] = '<h6>' . __( 'Form Button CSS Classes', 'customize-submit-button-for-gravity-forms' ) . '</h6>' . __( 'These are the CSS classes that are attached to the form submit button. You can change or overwrite them here.', 'customize-submit-button-for-gravity-forms' );
        return $tooltips;
    }

    public function init_admin() {
        // add plugin options to the Gforms form settings
    	add_filter('gform_form_settings', array( $this, 'filter_form_button_settings' ), 20, 2  );

        // add tooltips for new plugin options
        add_filter( 'gform_tooltips', array( $this, 'add_tooltips' ) );

        // save custom setting to the DB
        add_filter('gform_pre_form_settings_save', array( $this, 'save_form_button_settings' ), 20, 2  );
    
        // disable sanitization which prevents custom form settings from being saved
        add_filter('gform_disable_form_settings_sanitization', array( $this, 'disable_form_settings_sanitization' ) );
        
        $styleSheetDirectory = get_stylesheet_directory_uri();
        // enqueue plugin JS
        wp_enqueue_script( 
            'gform-custom-submit-button', 
            $styleSheetDirectory .	'/assets/js/wpas_gravityform.js', 
            
            array('jquery'), 
            '1.0.0', 
            true 
        );
    }

    public function filter_form_button_markup( $button_input, $form ) {

        // check to see if HTML button is selected
        if ( $form['button']['type'] == 'html') {

            if ( $form['button']['html'] ) {
                $text = $form['button']['html'];
            } else {
                $text = 'Submit';
            }

            // change input to a button
            $button_input = str_replace( 'input', 'button', $button_input );
            $button_input = str_replace( '/', '', $button_input );
            $button_input .= $text . "</button>";
        }

        // change button css classes if needed
        if ( isset( $form['button']['button_css_class'] ) ) {
            $button_css = $form['button']['button_css_class'];
            if ( !empty( $button_css ) ) {
                $button_input = preg_replace("/class='[^']*'/", "class='" . $button_css . "'", $button_input);
            }
        }

        return $button_input;
    }

    public function init_frontend() {
        
        // filter front end form markup
        add_filter( 'gform_submit_button', array( $this, 'filter_form_button_markup' ), 20, 3 );
    }

}
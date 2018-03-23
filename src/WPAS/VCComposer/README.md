# Extending Visual Composer

```php
namespace WPAS\VCComposer\;

class CodePreview extends BaseComponent{
    //your component unique slug
    const BASE_NAME = 'codepreview';
    
    function __construct(){
    	parent::__construct(self::BASE_NAME);
    }
    
    function register(){
    	
	   vc_map( array(
	      "name" => __( "Code Preview", "wpas_vc_dash" ),
	      "base" => "codepreview",
	      "category" => __( "BreatheCode", "wpas_vc_dash"),
	      .. vc_map options ..
	   ) );
    }
    
	function render( $atts , $content = null) 
	{
	    extract( shortcode_atts( array(
	      'input_param' => 'value'
	   ), $atts ) );
	   
	   //prepare your $htmlcontent variable
	   
	   return $htmlcontent;
	}
}
```